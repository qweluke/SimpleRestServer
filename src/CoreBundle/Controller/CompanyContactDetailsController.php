<?php

namespace CoreBundle\Controller;

use CoreBundle\Entity\Company;
use CoreBundle\Entity\Contact;
use CoreBundle\Entity\ContactDetail;
use CoreBundle\Form as Forms;
use CoreBundle\Security\CompanyContactVoter;
use FOS\RestBundle\View\View;
use JMS\Serializer\SerializerBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\Form\Exception\LogicException;
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
class CompanyContactDetailsController extends BaseController
{


    /**
     * Creates a new contact detail.
     * Please note that you can also create contact detail while creating|editing user.
     *
     * @Rest\Post("/{contact}/detail/new", requirements={"contact" = "\d+"})
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
     *  resource="/api/contact/detail",
     *  description="Creates new company",
     *
     *  parameters={
     *      {"name"="type", "dataType"="string", "description"="Contact format type", "format"="PHONE|MOBILE|FAX|EMAIL", "required"="true"},
     *      {"name"="value", "dataType"="string", "description"="Valid contact type value.", "format"="", "required"="true"},
     *  },
     *
     *  output={
     *   "class"="CoreBundle\Entity\ContactDetail",
     *   "parsers"={"Nelmio\ApiDocBundle\Parser\JmsMetadataParser"},
     *   "groups"={"ROLE_USER","ROLE_ADMIN"}
     *  }
     * )
     *
     * @param Request $request
     * @param Contact $contact
     * @return View
     */
    public function newAction(Request $request, Contact $contact)
    {
        $view = View::create()
            ->setSerializationContext(SerializationContext::create()
                ->setGroups($this->getUser()->getRoles())
            );

        $contactDetail = new ContactDetail();
        $form = $this->createForm(Forms\ContactDetailFormType::class, $contactDetail);
        $form->submit($request->request->all());


        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $contactDetail->setContact($contact);
            $em->persist($contactDetail);
            $em->flush();

            $view
                ->setStatusCode(Codes::HTTP_OK)
                ->setData($contactDetail);
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
     * Returns contact detail information
     *
     * @Rest\Get( "/{contact}/detail", requirements={"contact" = "\d+"})
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
     *  resource="/api/contact/detail",
     *  description="Returns contact data",
     *
     *  output={
     *   "class"="CoreBundle\Entity\ContactDetail",
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
            ->setData($contact->getContactDetails());

        return $this->handleView($view);
    }


    /**
     * Update contact detail. At least one field must be set to run this method.
     *
     * @Rest\Patch( "/{contact}/detail/{detail}", requirements={
     *     "contact" = "\d+",
     *     "detail" = "\d+"
     * } )
     *
     * @Rest\View(serializerGroups={"ROLE_USER", "ROLE_ADMIN"})
     * @param Request $request
     * @param Contact $contact
     * @param ContactDetail $detail
     * @return View
     * @throws \Exception
     * @internal param ContactDetail $contactDetail
     * @ApiDoc(
     *     statusCodes={
     *         200="Returned when successful",
     *         400="Returned when {detail} not belongs to the give {contact}"
     *     },
     *  headers={
     *      {
     *          "name"="Authorization",
     *          "required"="true",
     *          "description"="Bearer TOKEN"
     *      }
     *  },
     *  resource="/api/contact/detail",
     *  description="Updates contact detail data",
     *
     *  parameters={
     *      {"name"="type", "dataType"="string", "description"="Contact format type", "format"="PHONE|MOBILE|FAX|EMAIL", "required"="true"},
     *      {"name"="value", "dataType"="string", "description"="Valid contact type value.", "format"="", "required"="true"},
     *  },
     *
     *  output={
     *   "class"="CoreBundle\Entity\ContactDetail",
     *   "parsers"={"Nelmio\ApiDocBundle\Parser\JmsMetadataParser"},
     *   "groups"={"ROLE_USER","ROLE_ADMIN"}
     *  }
     * )
     */
    public function editAction(Request $request, Contact $contact, ContactDetail $detail)
    {

        $view = View::create()
            ->setSerializationContext(SerializationContext::create()
                ->setGroups($this->getUser()->getRoles())
            );

        $editForm = $this->createForm(Forms\ContactDetailFormType::class, $detail);
        $editForm->submit($request->request->all(), false);

        if(!$contact->getContactDetails()->contains($detail)) {
            throw new \Exception('ContactDetail not belongs to the given Contact');
        }


        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $this->getDoctrine()->getManager()->merge($contact);
            $this->getDoctrine()->getManager()->flush();
            $view
                ->setStatusCode(Codes::HTTP_OK)
                ->setData($detail);
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
     * Deletes a Contact detail
     *
     * @Rest\Delete( "/{contact}/detail/{detail}", requirements={
     *     "contact" = "\d+",
     *     "detail" = "\d+"
     * } )
     *
     * @Rest\View(serializerGroups={"ROLE_USER","ROLE_ADMIN"})
     * @param Request $request
     * @param Contact $contact
     * @param ContactDetail $detail
     * @return View
     * @throws \Exception*
     *
     * @ApiDoc(
     *     statusCodes={
     *         200="Returned when successful",
     *         400="Returned when {detail} not belongs to the give {contact}"
     *     },
     *  headers={
     *      {
     *          "name"="Authorization",
     *          "required"="true",
     *          "description"="Bearer TOKEN"
     *      }
     *  },
     *  resource="/api/contact/detail",
     *  description="Deletes contact detail"
     * )
     */
    public function deleteContactAction(Request $request, Contact $contact, ContactDetail $detail)
    {

        $view = View::create()
            ->setSerializationContext(SerializationContext::create()
                ->setGroups($this->getUser()->getRoles())
            );

        if(!$contact->getContactDetails()->contains($detail)) {
            throw new \Exception('ContactDetail not belongs to the given Contact');
        }

        $form = $this->createFormBuilder()->setMethod('DELETE')->getForm();
        $form->submit($request->request->get($form->getName()));

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($detail);
            $em->flush();

            $view->setStatusCode(Codes::HTTP_OK)
                ->setData([
                    'success' => true,
                    'message' => 'Contact detail successfully deleted.'
                ]);
        }
//        else {
//            $view
//                ->setStatusCode(Codes::HTTP_BAD_REQUEST)
//                ->setData([
//                    'success' => false,
//                    'message' => 'Unable to delete Contact.',
//                    'exception' => $this->getFormErrors($form)
//                ]);
//        }

        return $this->handleView($view);
    }

}
