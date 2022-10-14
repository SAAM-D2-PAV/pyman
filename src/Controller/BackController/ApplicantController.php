<?php

namespace App\Controller\BackController;

use App\Entity\Applicant;
use App\Form\ApplicantType;
use App\Service\MessageGenerator;
use App\Repository\ApplicantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
/**
 * @Route("/contacts")
 * @isGranted("ROLE_EDITOR", message="Vous n'avez pas accès à cette section !")
 */
class ApplicantController extends AbstractController
{

    /**
     * @Route("/liste", name="applicants_list")
     */
    public function applicantList(ApplicantRepository $applicantRepository)
    {
        $emptyList = false;
        $applicants = $applicantRepository->findAll();
       
        if (!$applicants) {
           $emptyList = true;
        }
        return $this->render('/back/applicant/applicants_list.html.twig', [
            'applicants' => $applicants,
            'emptyList' => $emptyList
        ]);
    }

    //*******************************************************
    //*******************************************************
    // SECTION ADMIN

    //Ajout contact

    /**
     * @Route("/creer",name="applicant_create")
     */
    public function applicantCreate(Request $request, MessageGenerator $messageGenerator)
    {

        // nouvelle entité contact
        $contact = new Applicant;

        // formulaire d'ajout d'un contact
        // ... sur lequel on "map" l'entité contact
        $form = $this->createForm(ApplicantType::class, $contact);

        // On demande au form de "prendre en charge" la requête
        $form->handleRequest($request);

        // Si form est soumis ? Est-il valide ?
        if ($form->isSubmitted() && $form->isValid()) {
        

            $em = $this->getDoctrine()->getManager();
            $em->persist($contact);
            $em->flush($contact);
            
            $this->addFlash('success', $messageGenerator->getHappyMessage());
            
            // On redirige vers la liste
            return $this->redirectToRoute('applicants_list');
        }
        
        return $this->render('back/applicant/edit.html.twig', [
            // createView() permet de récupérer
            // la représentation HTML du form
            'form' => $form->createView(),
            'cat' => 'AJOUT D\'UN CONTACT',
            'btnText' => 'Ajouter',
            'btnLabel' => 'bg-aqua_velvet',
            'ico' => 'plus'
        ]);
    }

    //Modifier contact
    /**
     * @Route("/{id}/editer",name="applicant_edit")
     * @isGranted("ROLE_ADMIN", message="Vous n'avez pas accès à cette section !")
     */
    public function edit($id, ApplicantRepository $applicantRepository, Request $request, EntityManagerInterface $em, MessageGenerator $messageGenerator)
    {
        $contact = $applicantRepository->find($id);


        if(!$contact){
            //Gérer les erreurs de requêtes 
             throw $this->createNotFoundException("Ce contact n'existe pas !");
         }

         $form = $this->createForm(ApplicantType::class, $contact);
         //$form->setData($equipment);

         $form->handleRequest($request);


         if ($form->isSubmitted() && $form->isValid()) {

            $em->flush();

            $this->addFlash('success', $messageGenerator->getHappyMessage());
            
            // On redirige vers la liste
            return $this->redirectToRoute('applicants_list');
         }
        return $this->render('back/applicant/edit.html.twig', [
             // createView() permet de récupérer
            // la représentation HTML du form
            'form' => $form->createView(),
            'cat' => 'MODIFIER LE CONTACT',
            'btnText' => 'Modifier',
            'btnLabel' => 'bg-casandora_yellow',
            'ico' => 'plus'
            
        
        ]);
    }


    //JSON -> vérification doublons
    /**
     * @Route("/cheeck", name="contact_cheeck")
     */
    public function cheeckContact(Request $request, ApplicantRepository $applicantRepository)
    {
       
        
            $mail = $request->request->get("mail");

            $applicant = $applicantRepository->findOneBy(['email' => $mail]);
            if ($applicant) {
                return $this->json(404);
            }
            return $this->json(200);
            
        
    }
}
