<?php

namespace App\Controller\BackController;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


// #[IsGranted('ROLE_ADMIN')]
#[Route('/react')]
class ReactController extends AbstractController
{



    // Template principale 
    #[Route('/', name: 'app_react')]
    public function index(): Response
    {
        return $this->render('back/react/index.html.twig', [
            'controller_name' => 'ReactController',
        ]);
    }
    //NOT USED ANYMORE
    // https://www.twilio.com/fr/blog/application-monopage-symfony-php-react
    #[Route('/users', name: 'app_react_users')]
    public function getUsers(UserRepository $userRepository)
    {
       
       return $this->json($userRepository->findBy(['taskOwner' => 1],['firstname' => 'ASC']), 200, [], ['groups' => 'u_read:collection']);
      
    }
}
