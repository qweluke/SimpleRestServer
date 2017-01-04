<?php

namespace CoreBundle\Controller;

use CoreBundle\Entity\Company;
use CoreBundle\Entity\Contact;
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
 * @Route("/api/contact")
 */
class CompanyContactController extends BaseController
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
     *  resource="/api/contact/",
     *  description="Search for a company",
     *  filters={
     *      {"name"="query", "dataType"="string", "description"="Search for firstName, lastName, jobTitle or company containing a {query} value"},
     *      {"name"="orderBy[]", "dataType"="array", "pattern"="(id|firstName|lastName|jobTitle|company) ASC|DESC"}
     *  },
     *
     *
     *  output={
     *   "class"="CoreBundle\Entity\Contact",
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


        $users = $em->getRepository(Contact::class)->search($request->query->all());

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
     *  resource="/api/contact/",
     *  description="Creates new company",
     *
     *  parameters={
     *      {"name"="firstName", "dataType"="string", "description"="Contact First name", "required"="true"},
     *      {"name"="lastName", "dataType"="string", "description"="Contact last name", "required"="true"},
     *      {"name"="jobTitle", "dataType"="string", "description"="Contact job title", "required"="false"},
     *      {"name"="company", "dataType"="int", "description"="Company id", "required"=""},
     *      {"name"="image", "dataType"="file", "description"="Contact image", "required"=""},
     *      {"name"="birthDate", "dataType"="string", "description"="Contact birth date", "format"="YYYY-MM-DD", "required"=""},
     *      {"name"="visibleAll", "dataType"="boolean", "description"="Is contact visible to all users.", "format"="(1|0)", "required"="true"},
     *      {"name"="editableAll", "dataType"="boolean", "description"="If true, {visibleAll} must be true as well.", "format"="(1|0)", "required"="true"},
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

        $contact = new Contact();
        $form = $this->createForm(Forms\ContactFormType::class, $contact);
        $form->submit($request->request->all());


        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($contact);
            $em->flush();

            $view
                ->setStatusCode(Codes::HTTP_OK)
                ->setData($contact);
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
     * @Rest\Get( "/{contact}", requirements={"company" = "\d+"})
     * @Rest\View(serializerGroups={"ROLE_USER", "ROLE_ADMIN"})
     * @param Contact $contact
     * @return View
     * @internal param Company $company
     * @ApiDoc(
     *  headers={
     *      {
     *          "name"="Authorization",
     *          "required"="true",
     *          "description"="Bearer TOKEN"
     *      }
     *  },
     *  resource="/api/contact/",
     *  description="Returns company information",
     *
     *  output={
     *   "class"="CoreBundle\Entity\Contact",
     *   "parsers"={"Nelmio\ApiDocBundle\Parser\JmsMetadataParser"},
     *   "groups"={"ROLE_USER", "ROLE_ADMIN"}
     *  }
     * )
     */
    public function showAction(Contact $contact)
    {

        $view = View::create()
            ->setSerializationContext(SerializationContext::create()
                ->setGroups($this->getUser()->getRoles())
            )
            ->setData($contact);

        return $this->handleView($view);
    }


    /**
     * Update contact. At least one field must be set to run this method.
     *
     * @Rest\Patch( "/{contact}", requirements={"contact" = "\d+"} )
     *
     * @Security("is_granted('delete', contact)")
     *
     * @Rest\View(serializerGroups={"ROLE_USER", "ROLE_ADMIN"})
     * @param Request $request
     * @param Contact $contact
     * @return View
     * @ApiDoc(
     *  headers={
     *      {
     *          "name"="Authorization",
     *          "required"="true",
     *          "description"="Bearer TOKEN"
     *      }
     *  },
     *  resource="/api/contact/",
     *  description="Updates contact data",
     *
     *  parameters={
     *      {"name"="firstName", "dataType"="string", "description"="Contact First name", "required"=""},
     *      {"name"="lastName", "dataType"="string", "description"="Contact last name", "required"=""},
     *      {"name"="jobTitle", "dataType"="string", "description"="Contact job title", "required"=""},
     *      {"name"="company", "dataType"="int", "description"="Company id", "required"=""},
     *      {"name"="image", "dataType"="file", "description"="Contact image", "required"=""},
     *      {"name"="birthDate", "dataType"="string", "description"="Contact birth date", "format"="YYYY-MM-DD", "required"=""},
     *      {"name"="visibleAll", "dataType"="boolean", "description"="Is contact visible to all users.", "format"="(1|0)", "required"="true"},
     *      {"name"="editableAll", "dataType"="boolean", "description"="If true, {visibleAll} must be true as well.", "format"="(1|0)", "required"=""},
     *  },
     *
     *  output={
     *   "class"="CoreBundle\Entity\Contact",
     *   "parsers"={"Nelmio\ApiDocBundle\Parser\JmsMetadataParser"},
     *   "groups"={"ROLE_ADMIN"}
     *  }
     * )
     */
    public function editAction(Request $request, Contact $contact)
    {
        $view = View::create()
            ->setSerializationContext(SerializationContext::create()
                ->setGroups($this->getUser()->getRoles())
            );

        $editForm = $this->createForm(Forms\ContactFormType::class, $contact);
        $editForm->submit($request->request->all(), false);


        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $this->getDoctrine()->getManager()->merge($contact);
            $this->getDoctrine()->getManager()->flush();
            $view
                ->setStatusCode(Codes::HTTP_OK)
                ->setData($contact);
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
     * Deletes a Company entity.
     *
     * @Security("is_granted('delete', contact)")
     *
     * @Rest\Delete( "/{contact}", requirements={"contact" = "\d+"} )
     *
     * @Rest\View(serializerGroups={"ROLE_USER","ROLE_ADMIN"})
     * @param Request $request
     * @param Contact contact
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
     *  resource="/api/contact/",
     *  description="Deletes contact"
     * )
     */
    public function deleteContactAction(Request $request, Contact $contact)
    {
        $view = View::create()
            ->setSerializationContext(SerializationContext::create()
                ->setGroups($this->getUser()->getRoles())
            );

        $form = $this->createFormBuilder()->setMethod('DELETE')->getForm();
        $form->submit($request->request->get($form->getName()));

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($contact);
            $em->flush();

            $view->setStatusCode(Codes::HTTP_OK)
                ->setData([
                    'success' => true,
                    'message' => 'Contact successfully deleted.'
                ]);
        } else {
            $view
                ->setStatusCode(Codes::HTTP_BAD_REQUEST)
                ->setData([
                    'success' => false,
                    'message' => 'Unable to delete Contact.',
                    'exception' => $this->getFormErrors($form)
                ]);
        }

        return $this->handleView($view);
    }

}
