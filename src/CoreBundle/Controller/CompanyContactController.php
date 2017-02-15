<?php

namespace CoreBundle\Controller;

use CoreBundle\Entity\Company;
use CoreBundle\Entity\Contact;
use CoreBundle\Form as Forms;
use CoreBundle\Security\CompanyContactVoter;
use FOS\RestBundle\View\View;
use JMS\Serializer\SerializerBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Finder\Exception\AccessDeniedException;
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
     *     {
     *         "data": <Array of Contact>,
     *         "pagesCount": int,
     *         "totalItems": int
     *     }
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
     *  description="Search for a contact",
     *  filters={
     *      {"name"="page", "dataType"="int", "description"="Number of page to display"},
     *      {"name"="limit", "dataType"="int", "description"="Results per page"},
     *      {"name"="query", "dataType"="string", "description"="Search for firstName, lastName, jobTitle or company containing a {query} value"},
     *      {"name"="orderBy[]", "dataType"="array", "pattern"="(id|firstName|lastName|jobTitle|company) ASC|DESC"}
     *  },
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


        $users = $em->getRepository(Contact::class)->search($this->get('app.request_handler')->handle($request));

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
     *  description="Creates new contact",
     *
     *  parameters={
     *      {"name"="firstName", "dataType"="string", "description"="Contact First name", "required"="true"},
     *      {"name"="lastName", "dataType"="string", "description"="Contact last name", "required"="true"},
     *      {"name"="jobTitle", "dataType"="string", "description"="Contact job title", "required"="false"},
     *      {"name"="contactDetails[0][type]", "dataType"="string", "description"="Contact format type", "format"="PHONE|MOBILE|FAX|EMAIL", "required"=""},
     *      {"name"="contactDetails[0][value]", "dataType"="string", "description"="Valid contact type value. To add multiple values increase index {0} for each record.", "format"="", "required"=""},
     *      {"name"="company", "dataType"="int", "description"="Company id", "required"=""},
     *      {"name"="image", "dataType"="file", "description"="Contact image", "required"=""},
     *      {"name"="editableAll", "dataType"="boolean", "description"="Allow all users to edit contact", "format"="(1|0)", "required"=""},
     *  },
     *
     *  output={
     *   "class"="CoreBundle\Entity\Contact",
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
//        if ($form->isSubmitted() && $form->isValid()) {
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
                    'message' => 'Unable to create new contact.',
                    'exception' => (string) $form->getErrors(true, false)
                ]);
        }

        return $this->handleView($view);
    }

    /**
     *
     * @Rest\Get( "/{contact}", requirements={"contact" = "\d+"})
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
     * @Rest\View(serializerGroups={"ROLE_USER", "ROLE_ADMIN"})
     * @param Request $request
     * @param Contact $contact
     * @return View
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
     *  resource="/api/contact/",
     *  description="Updates contact data",
     *
     *  parameters={
     *      {"name"="firstName", "dataType"="string", "description"="Contact First name", "required"=""},
     *      {"name"="lastName", "dataType"="string", "description"="Contact last name", "required"=""},
     *      {"name"="jobTitle", "dataType"="string", "description"="Contact job title", "required"=""},
     *      {"name"="contactDetails[0][type]", "dataType"="string", "description"="Contact format type", "format"="PHONE|MOBILE|FAX|EMAIL", "required"=""},
     *      {"name"="contactDetails[0][value]", "dataType"="string", "description"="Valid contact type value. To add multiple values increase index {0} for each record.", "format"="", "required"=""},
     *      {"name"="company", "dataType"="int", "description"="Company id", "required"=""},
     *      {"name"="image", "dataType"="file", "description"="Contact image", "required"=""},
     *      {"name"="birthDate", "dataType"="string", "description"="Contact birth date", "format"="YYYY-MM-DD", "required"=""},
     *      {"name"="editableAll", "dataType"="boolean", "description"="Allow all users to edit contact", "format"="(1|0)", "required"=""},
     *  },
     *
     *  output={
     *   "class"="CoreBundle\Entity\Contact",
     *   "parsers"={"Nelmio\ApiDocBundle\Parser\JmsMetadataParser"},
     *   "groups"={"ROLE_USER","ROLE_ADMIN"}
     *  }
     * )
     */
    public function editAction(Request $request, Contact $contact)
    {
        if (!$this->isGranted(CompanyContactVoter::EDIT, $contact)) {
            throw $this->createAccessDeniedException();
        }

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
     * Deletes a Contact entity with all contactDetails.
     *
     * @Rest\Delete( "/{contact}", requirements={"contact" = "\d+"} )
     *
     * @Rest\View(serializerGroups={"ROLE_USER","ROLE_ADMIN"})
     * @param Request $request
     * @param Contact $contact
     * @return View
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
     *  resource="/api/contact/",
     *  description="Deletes contact"
     * )
     */
    public function deleteContactAction(Request $request, Contact $contact)
    {
        if (!$this->isGranted(CompanyContactVoter::DELETE, $contact)) {
            throw $this->createAccessDeniedException();
        }

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
