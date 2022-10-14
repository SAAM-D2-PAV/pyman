<?php

namespace App\Controller\FrontController;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



class HelloWorldController extends AbstractController
{
     /**
     * @Route("/test/{test<\d+>?0}", name ="test", methods={"GET", "POST"}, host="localhost", schemes={"http","https"})
     */
    public function test ($test)
    {
        //return new Response("Vous avez $age ans !");
        return $this->render('test/index.html.twig',[

            "test" => $test
         ]);
    }   

}