<?php

namespace App\Application\Project\ContentBundle\Controller;

use App\Application\Project\ContentBundle\Controller\Base\BaseWebController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/', name: 'web_')]
class ContentWebController extends BaseWebController
{
    #[Route('/', name: 'home', methods: ['GET'])]
    public function home(): Response
    {
        return $this->render('@ApplicationProjectContent/home/home.html.twig');
    }

    #[Route('/web', name: 'dashboard', methods: ['GET'])]
    public function dashboard(): Response
    {
        return $this->render('@ApplicationProjectContent/home/dashboard.html.twig');
    }

}