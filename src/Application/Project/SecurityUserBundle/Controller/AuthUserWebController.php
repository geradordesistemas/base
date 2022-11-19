<?php

namespace App\Application\Project\SecurityUserBundle\Controller;

use App\Application\Project\ContentBundle\Controller\Base\BaseWebController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[Route('/web', name: 'web_')]
class AuthUserWebController extends BaseWebController
{

    /**
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    #[Route('/login', name: 'login', methods: ['GET', 'POST'])]
    public function loginAction(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('@ApplicationProjectSecurityUser/auth/login.html..twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    #[Route('/logout', name: 'logout', methods: ['GET', 'POST'])]
    public function logoutAction(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /** @return Response */
    #[Route('/error', name: 'error', methods: ['POST'])]
    public function accessDeniedAction(): Response
    {
        return $this->render('@ApplicationProjectSecurityAdmin/error/error_403.html.twig');
    }

}