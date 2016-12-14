<?php

namespace CoreBundle\Controller;

use CoreBundle\Entity\Company;
use CoreBundle\Form as Forms;
use FOS\RestBundle\View\View;
use JMS\Serializer\SerializerBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Util\Codes;
use JMS\Serializer\SerializationContext;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Company controller.
 *
 * @Route("/api/company")
 */
class CompanyController extends BaseController
{

    /**
     * Search for an users. At least one field must be setted in order to use this method.
     *
     * @Rest\Get("/")
     * @Rest\View(serializerGroups={"ROLE_USER","ROLE_ADMIN"})
     *
     * @ApiDoc(
     *  headers={
     *      {
     *          "name"="Authorization",
     *          "required"="true",
     *          "description"="Bearer TOKEN"
     *      }
     *  },
     *
     *  resource="/api/company/",
     *  description="Search for a company",
     *  filters={
     *      {"name"="query", "dataType"="string", "description"="Search for name or description containing a {query} value"},
     *      {"name"="orderBy[]", "dataType"="array", "pattern"="(id|name|createdAt|updatedAt) ASC|DESC"}
     *  },
     *
     *
     *  output={
     *   "class"="CoreBundle\Entity\Company",
     *   "parsers"={"Nelmio\ApiDocBundle\Parser\JmsMetadataParser"},
     *   "groups"={"ROLE_USER","ROLE_ADMIN"}
     *  }
     * )
     * @param Request $request
     * @return View
     */
    public function indexAction(Request $request)
    {
        $view = View::create()
            ->setSerializationContext(
                SerializationContext::create()->setGroups($this->getUser()->getRoles())
            );

        $em = $this->getDoctrine()->getManager();


        $users = $em->getRepository(Company::class)->search($request->query->all());

        $view
            ->setStatusCode(Codes::HTTP_OK)
            ->setData($users);

        return $this->handleView($view);
    }

    /**
     * Creates a new Company.
     *
     * @Rest\Post("/new")
     * @Rest\View(serializerGroups={"ROLE_USER","ROLE_ADMIN"})
     *
     * @ApiDoc(
     *  headers={
     *      {
     *          "name"="Authorization",
     *          "required"="true",
     *          "description"="Bearer TOKEN"
     *      }
     *  },
     *  resource="/api/company/",
     *  description="Creates new company",
     *
     *  input={
     *     "class"="CoreBundle\Form\CompanyFormType",
     *      "name"=""
     *  },
     *
     *  output={
     *   "class"="CoreBundle\Entity\Company",
     *   "parsers"={"Nelmio\ApiDocBundle\Parser\JmsMetadataParser"},
     *   "groups"={"ROLE_USER","ROLE_ADMIN"}
     *  }
     * )
     *
     * @param Request $request
     * @return View
     */
    public function newAction(Request $request)
    {
        $view = View::create()
            ->setSerializationContext(SerializationContext::create()
                ->setGroups($this->getUser()->getRoles())
            );

        $company = new Company();
        $form = $this->createForm(Forms\CompanyFormType::class, $company);
        $form->submit($request->request->all());


        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($company);
            $em->flush();

            $view
                ->setStatusCode(Codes::HTTP_OK)
                ->setData($company);
        } else {
            $view
                ->setStatusCode(Codes::HTTP_BAD_REQUEST)
                ->setData([
                    'success' => false,
                    'message' => 'Unable to create new company.',
                    'exception' => $this->getFormErrors($form)
                ]);
        }

        return $this->handleView($view);
    }
}
