<?php

namespace CoreBundle\Controller;

use CoreBundle\Entity\Company;
use CoreBundle\Entity\Contact;
use CoreBundle\Form as Forms;
use CoreBundle\Security\CompanyContactVoter;
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
     *  parameters={
     *      {"name"="name", "dataType"="string", "description"="User login", "required"="true"},
     *      {"name"="description", "dataType"="string", "description"="User email", "required"=""},
     *      {"name"="contacts[]", "dataType"="Array<int>", "description"="Array of contact's ID's", "format"="\d+", "required"=""},
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

    /**
     *
     * @Rest\Get( "/{company}", requirements={"company" = "\d+"})
     * @Rest\View(serializerGroups={"ROLE_USER", "ROLE_ADMIN"})
     * @param Company $company
     * @return View
     * @throws \NotFoundHttpException
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
     *  description="Returns company information",
     *
     *  output={
     *   "class"="CoreBundle\Entity\Company",
     *   "parsers"={"Nelmio\ApiDocBundle\Parser\JmsMetadataParser"},
     *   "groups"={"ROLE_USER", "ROLE_ADMIN"}
     *  }
     * )
     */
    public function showAction(Company $company)
    {

        $view = View::create()
            ->setSerializationContext(SerializationContext::create()
                ->setGroups($this->getUser()->getRoles())
            )
            ->setData($company);

        return $this->handleView($view);
    }

    /**
     * Update user. At least one field must be set to run this method.
     *
     * @Rest\Patch( "/{company}", requirements={"company" = "\d+"} )

     * @Rest\View(serializerGroups={"ROLE_USER", "ROLE_ADMIN"})
     * @param Request $request
     * @param Company $company
     * @return View
     * @ApiDoc(
     *  headers={
     *      {
     *          "name"="Authorization",
     *          "required"="true",
     *          "description"="Bearer TOKEN"
     *      }
     *  },
     *  resource="/api/company/",
     *  description="Updates content data",
     *
     *  parameters={
     *      {"name"="name", "dataType"="string", "description"="User login", "required"=""},
     *      {"name"="description", "dataType"="string", "description"="User email", "required"=""},
     *  },
     *
     *  output={
     *   "class"="CoreBundle\Entity\Company",
     *   "parsers"={"Nelmio\ApiDocBundle\Parser\JmsMetadataParser"},
     *   "groups"={"ROLE_ADMIN"}
     *  }
     * )
     */
    public function editAction(Request $request, Company $company)
    {
        $view = View::create()
            ->setSerializationContext(SerializationContext::create()
                ->setGroups($this->getUser()->getRoles())
            );

        $editForm = $this->createForm(Forms\CompanyFormType::class, $company);
        $editForm->submit($request->request->all(), false);


        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $this->getDoctrine()->getManager()->merge($company);
            $this->getDoctrine()->getManager()->flush();
            $view
                ->setStatusCode(Codes::HTTP_OK)
                ->setData($company);
        } else {
            $view
                ->setStatusCode(Codes::HTTP_BAD_REQUEST)
                ->setData([
                    'success' => false,
                    'message' => 'Unable to update contact.',
                    'exception' => $this->getFormErrors($editForm)]);
        }

        return $this->handleView($view);
    }

    /**
     * Deletes a Company with all contacts.
     *
     * @Rest\Delete( "/{company}", requirements={"company" = "\d+"} )
     *
     * @Rest\View(serializerGroups={"ROLE_USER","ROLE_ADMIN"})
     * @param Request $request
     * @param Company $company
     * @return View
     * @internal param User $user
     * @ApiDoc(
     *  headers={
     *      {
     *          "name"="Authorization",
     *          "required"="true",
     *          "description"="Bearer TOKEN"
     *      }
     *  },
     *  resource="/api/company/",
     *  description="Deletes company with all contacts"
     * )
     */
    public function deleteCompanyAction(Request $request, Company $company)
    {
        $view = View::create()
            ->setSerializationContext(SerializationContext::create()
                ->setGroups($this->getUser()->getRoles())
            );

        $form = $this->createFormBuilder()->setMethod('DELETE')->getForm();
        $form->submit($request->request->get($form->getName()));

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($company);
            $em->flush();

            $view->setStatusCode(Codes::HTTP_OK)
                ->setData([
                    'success' => true,
                    'message' => 'Company successfully deleted.'
                ]);
        } else {
            $view
                ->setStatusCode(Codes::HTTP_BAD_REQUEST)
                ->setData([
                    'success' => false,
                    'message' => 'Unable to delete Company.',
                    'exception' => $this->getFormErrors($form)
                ]);
        }

        return $this->handleView($view);
    }

    /**
     * Deletes a Company contact.
     *
     * @Rest\Delete( "/{company}/{contact}", requirements={
     *     "company" = "\d+",
     *     "contact" = "\d+"
     * })
     *
     * @Rest\View(serializerGroups={"ROLE_USER","ROLE_ADMIN"})
     * @param Company $company
     * @param Contact $contact
     * @return View
     * @internal param Request $request
     * @internal param User $user
     * @ApiDoc(
     *     statusCodes={
     *         200="Returned when successful",
     *         403="Returned when {editableAll} is set to false (0) and current user is not owner."
     *     },
     *  headers={
     *      {
     *          "name"="Authorization",
     *          "required"="true",
     *          "description"="Bearer TOKEN"
     *      }
     *  },
     *  resource="/api/company/",
     *  description="Deletes company contact"
     * )
     */
    public function deleteCompanyContactAction(Company $company, Contact $contact)
    {
        if (!$this->isGranted(CompanyContactVoter::DELETE, $contact)) {
            throw $this->createAccessDeniedException();
        }

        $view = View::create()
            ->setSerializationContext(SerializationContext::create()
                ->setGroups($this->getUser()->getRoles())
            );

        try {
            $company->removeContact($contact);

            $view->setStatusCode(Codes::HTTP_OK)
                ->setData([
                    'success' => true,
                    'message' => 'Contact successfully deleted from a Company.'
                ]);
        } catch (\Exception $ex) {
            $view
                ->setStatusCode(Codes::HTTP_BAD_REQUEST)
                ->setData([
                    'success' => false,
                    'message' => 'Unable to delete Company contact.',
                    'exception' => $this->getFormErrors($ex->getMessage())
                ]);
        }


        return $this->handleView($view);
    }
}
