<?php

namespace App\Application\Project\ContentBundle\Controller;

use App\Application\Project\ContentBundle\Controller\Base\BaseAdminController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class ContentAdminController extends BaseAdminController
{
    /**
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function loginAction(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('@ApplicationProjectSecurityAdmin/auth/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    public function logoutAction(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /** @return Response */
    public function accessDeniedAction(): Response
    {
        return $this->render('@ApplicationProjectSecurityAdmin/error/error_403.html.twig');
    }


    public function listAllRolesAction(): JsonResponse
    {
        $rolesGroups = [
            "adminRoles" => $this->adminACL->getAdminGroupRoles(),
            "apiRoles" => $this->apiACL->getApiGroupRoles(),
            "webRoles" => $this->webACL->getWebGroupRoles(),
        ];

        return $this->json($rolesGroups);
    }

    public function listAdminGroupRolesAction(): JsonResponse
    {
        return $this->json($this->adminACL->getAdminGroupRoles());
    }

    public function listAdminRolesAction(): JsonResponse
    {
        return $this->json($this->adminACL->getAdminRoles());
    }



}