<?php

namespace CoreBundle\Controller;

use CoreBundle\Form as Forms;
use FOS\RestBundle\View\View;
use JMS\Serializer\SerializerBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use CoreBundle\Entity\User;
use FOS\RestBundle\Util\Codes;
use JMS\Serializer\SerializationContext;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * User controller.
 *
 * @Route("/api/user")
 */
class UserController extends BaseController
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
     *  resource="/api/user/",
     *  description="Search for users",
     *  filters={
     *      {"name"="query", "dataType"="string", "description"="Paraphrase to search for"},
     *      {"name"="orderBy[]", "dataType"="array", "pattern"="(id|firstName|lastName|gender) ASC|DESC"},
     *      {"name"="gender", "dataType"="string", "pattern"="(male|female)"},
     *      {"name"="role", "dataType"="string", "pattern"="(user|admin)"},
     *      {"name"="active", "dataType"="string", "pattern"="(true|false) "},
     *  },
     *
     *
     *  output={
     *   "class"="CoreBundle\Entity\User",
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


        $users = $em->getRepository(User::class)->search($request->query->all());

        $view
            ->setStatusCode(Codes::HTTP_OK)
            ->setData($users);

        return $this->handleView($view);


    }

    /**
     * Creates a new User.
     *
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @Rest\Post("/new")
     * @Rest\View(serializerGroups={"ROLE_ADMIN"})
     *
     * @ApiDoc(
     *  headers={
     *      {
     *          "name"="Authorization",
     *          "required"="true",
     *          "description"="Bearer TOKEN"
     *      }
     *  },
     *  authentication="true",
     *  resource="/api/content/",
     *  description="Creates new user",
     *
     *  parameters={
     *      {"name"="username", "dataType"="string", "description"="User login", "required"="true"},
     *      {"name"="email", "dataType"="string", "description"="User email", "required"="true"},
     *      {"name"="firstName", "dataType"="string", "description"="First name", "required"="true"},
     *      {"name"="lastName", "dataType"="string", "description"="Last name", "required"="true"},
     *      {"name"="plainPassword", "dataType"="string", "description"="User plain password", "required"="true"},
     *      {"name"="enabled", "dataType"="boolean", "description"="Default value is 0", "format"="(1|0)", "required"="false"},
     *      {"name"="gender", "dataType"="string", "description"="Gender", "format"="(male|female)", "required"="true"},
     *      {"name"="birthDate", "dataType"="string", "description"="Birth date", "format"="YYYY-MM-DD", "required"="false"},
     *      {"name"="roles[]", "dataType"="Array", "description"="Array of roles", "format"="(user|admin)", "required"="false"},
     *  },
     *
     *  output={
     *   "class"="CoreBundle\Entity\User",
     *   "parsers"={"Nelmio\ApiDocBundle\Parser\JmsMetadataParser"},
     *   "groups"={"ROLE_ADMIN"}
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

        $user = new User();
        $form = $this->createForm(Forms\UserType::class, $user);
        $form->submit($request->request->all());


        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($user);
            $em->flush();

            $view
                ->setStatusCode(Codes::HTTP_OK)
                ->setData($user);
        } else {
            $view
                ->setStatusCode(Codes::HTTP_BAD_REQUEST)
                ->setData([
                    'success' => false,
                    'message' => 'Unable to create new user.',
                    'exception' => $this->getFormErrors($form)
                ]);
        }

        return $this->handleView($view);
    }

    /**
     *
     * @Rest\Get( "/{user}", requirements={"user" = "\d+"})
     * @Rest\View(serializerGroups={"ROLE_USER", "ROLE_ADMIN"})
     * @param User $user
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
     *  resource="/api/user/",
     *  description="Returns user information",
     *
     *  output={
     *   "class"="CoreBundle\Entity\User",
     *   "parsers"={"Nelmio\ApiDocBundle\Parser\JmsMetadataParser"},
     *   "groups"={"ROLE_USER", "ROLE_ADMIN"}
     *  }
     * )
     */
    public function showAction(User $user)
    {

        $view = View::create()
            ->setSerializationContext(SerializationContext::create()
                ->setGroups($this->getUser()->getRoles())
            )
            ->setData($user);

        return $this->handleView($view);
    }

    /**
     * Update user. At least one field must be set to run this method.
     *
     * @Security("has_role('ROLE_ADMIN')")
     * @Rest\Patch( "/{user}", requirements={"user" = "\d+"} )
     *
     * @Rest\View(serializerGroups={"ROLE_USER", "ROLE_ADMIN"})
     * @param Request $request
     * @param User $user
     * @return View
     * @ApiDoc(
     *  headers={
     *      {
     *          "name"="Authorization",
     *          "required"="true",
     *          "description"="Bearer TOKEN"
     *      }
     *  },
     *  authentication="true",
     *  resource="/api/user/",
     *  description="Updates content data",
     *
     *  parameters={
     *      {"name"="username", "dataType"="string", "description"="User login", "required"=""},
     *      {"name"="email", "dataType"="string", "description"="User email", "required"=""},
     *      {"name"="firstName", "dataType"="string", "description"="First name", "required"=""},
     *      {"name"="lastName", "dataType"="string", "description"="Last name", "required"=""},
     *      {"name"="plainPassword", "dataType"="string", "description"="User plain password", "required"=""},
     *      {"name"="enabled", "dataType"="boolean", "description"="Default value is 0", "format"="(1|0)", "required"=""},
     *      {"name"="gender", "dataType"="string", "description"="Gender", "format"="(male|female)", "required"=""},
     *      {"name"="birthDate", "dataType"="string", "description"="Birth date", "format"="YYYY-MM-DD", "required"=""},
     *      {"name"="roles[]", "dataType"="Array", "description"="Array of roles", "format"="(user|admin)", "required"=""},
     *  },
     *
     *  output={
     *   "class"="CoreBundle\Entity\User",
     *   "parsers"={"Nelmio\ApiDocBundle\Parser\JmsMetadataParser"},
     *   "groups"={"ROLE_ADMIN"}
     *  }
     * )
     */
    public function editAction(Request $request, User $user)
    {
        $view = View::create()
            ->setSerializationContext(SerializationContext::create()
                ->setGroups($this->getUser()->getRoles())
            );

        $editForm = $this->createForm(Forms\UserType::class, $user);
        $editForm->submit($request->request->all(), false);


        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $this->get('fos_user.user_manager')->updateUser($user, true);

            $view
                ->setStatusCode(Codes::HTTP_OK)
                ->setData($user);
        } else {
            $view
                ->setStatusCode(Codes::HTTP_BAD_REQUEST)
                ->setData([
                    'success' => false,
                    'message' => 'Unable to update user.',
                    'exception' => $this->getFormErrors($editForm)]);
        }

        return $this->handleView($view);
    }

    /**
     * Update logged user data.
     *
     * @Rest\Patch( "/" )
     *
     * @Rest\View(serializerGroups={"ROLE_USER", "ROLE_ADMIN"})
     * @param Request $request
     * @return View
     *
     * @ApiDoc(
     *  headers={
     *      {
     *          "name"="Authorization",
     *          "required"="true",
     *          "description"="Bearer TOKEN"
     *      }
     *  },
     *  resource="/api/user/",
     *  description="Update current user data",
     *
     *  parameters={
     *      {"name"="email", "dataType"="string", "description"="User email", "required"=""},
     *      {"name"="firstName", "dataType"="string", "description"="First name", "required"=""},
     *      {"name"="lastName", "dataType"="string", "description"="Last name", "required"=""},
     *      {"name"="plainPassword", "dataType"="string", "description"="User plain password", "required"=""},
     *      {"name"="gender", "dataType"="string", "description"="Gender", "format"="(male|female)", "required"=""},
     *      {"name"="birthDate", "dataType"="string", "description"="Birth date", "format"="YYYY-MM-DD", "required"=""},
     *  },
     *
     *  output={
     *   "class"="CoreBundle\Entity\User",
     *   "parsers"={"Nelmio\ApiDocBundle\Parser\JmsMetadataParser"},
     *   "groups"={"ROLE_USER", "ROLE_ADMIN"}
     *  }
     * )
     */
    public function editUserAction(Request $request)
    {
        $view = View::create()
            ->setSerializationContext(SerializationContext::create()
                ->setGroups($this->getUser()->getRoles())
            );

        /** @var $user User */
        $user = $this->getUser();

        $editForm = $this->createForm(Forms\UserType::class, $user);
        $editForm->submit($request->request->all(), false);


        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $this->get('fos_user.user_manager')->updateUser($user, true);

            $view
                ->setStatusCode(Codes::HTTP_OK)
                ->setData($user);
        } else {
            $view
                ->setStatusCode(Codes::HTTP_BAD_REQUEST)
                ->setData([
                    'success' => false,
                    'message' => 'Unable to update user.',
                    'exception' => $this->getFormErrors($editForm)
                ]);
        }

        return $this->handleView($view);
    }

    /**
     * Deletes a User entity.
     *
     * @Security("has_role('ROLE_ADMIN')")
     * @Rest\Delete( "/{user}", requirements={"user" = "\d+"} )
     *
     * @Rest\View(serializerGroups={"user","mod","admin"})
     * @param Request $request
     * @param User $user
     * @return View
     * @ApiDoc(
     *  headers={
     *      {
     *          "name"="Authorization",
     *          "required"="true",
     *          "description"="Bearer TOKEN"
     *      }
     *  },
     *  authentication="true",
     *  resource="/api/user/",
     *  description="Deletes content"
     * )
     */
    public function deleteAction(Request $request, User $user)
    {
        $view = View::create()
            ->setSerializationContext(SerializationContext::create()
                ->setGroups($this->getUser()->getRoles())
            );

        $form = $this->createFormBuilder()->setMethod('DELETE')->getForm();
        $form->submit($request->request->get($form->getName()));

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();

            $view->setStatusCode(Codes::HTTP_OK)
                ->setData([
                    'success' => true,
                    'message' => 'User successfully deleted.'
                ]);
        } else {
            $view
                ->setStatusCode(Codes::HTTP_BAD_REQUEST)
                ->setData([
                    'success' => false,
                    'message' => 'Unable to delete user.',
                    'exception' => $this->getFormErrors($form)
                ]);
        }

        return $this->handleView($view);
    }

}
