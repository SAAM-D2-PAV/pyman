<?php

namespace App\Controller\BackController;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ReactController extends AbstractController
{
    
    #[Route('/react', name: 'app_react')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(): Response
    {
        return $this->render('back/react/index.html.twig', [
            'controller_name' => 'ReactController',
        ]);
    }
}
