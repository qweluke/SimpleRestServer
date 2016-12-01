<?php

namespace CoreBundle\Controller;

use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Guard\JWTTokenAuthenticator as BaseAuthenticator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * User controller.
 *
 * @Route("/api/security")
 */
class SecurityController extends BaseAuthenticator
{

    /**
     * Returns token which should be used in request HEADERS. Ex:
     * Authorization: Bearer YOUR_TOKEN_HERE
     *
     * @Rest\Post("/login")
     * @Rest\View(serializerGroups={"ROLE_USER","ROLE_ADMIN"})
     *
     * @ApiDoc(
     *  resource="/api/security/",
     *  description="Returns user token",
     *
     *  parameters={
     *      {"name"="_username", "dataType"="string", "required"=true, "description"="username or email"},
     *      {"name"="_password", "dataType"="string", "required"=true, "description"="user password"}
     *  },
     * )
     * @param Request $request
     * @return View
     */
    public function getCredentials(Request $request)
    {
        return parent::getCredentials($request);
    }

}
