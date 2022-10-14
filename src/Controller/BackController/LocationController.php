<?php

namespace App\Controller\BackController;

use App\Entity\Location;
use App\Form\LocationType;
use App\Service\MessageGenerator;
use App\Repository\LocationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/lieux")
 * @isGranted("ROLE_VIEWER", message="Vous devez être connecté !")
 */
class LocationController extends AbstractController
{

    //Liste des lieux de tournage et d'intervention
    /**
     * @Route("/liste", name="locations_list")
     */
    public function locationList(LocationRepository $locationRepository)
    {
        $emptyList = false;
        $locationList = $locationRepository->findAll();

        if (!$locationList) {
            $emptyList = true;
         }
        /* if(!$locationList){
            //Gérer les erreurs de requêtes 
             throw $this->createNotFoundException("aucune tache à afficher !");
             
         } */

         return $this->render('back/location/locations_list.html.twig',[
             'locationList' => $locationList,
             'emptyList' => $emptyList
         ]);
    }
    //Afficher un lieu de tournage et d'intervention
    /**
     * @Route("/{slug}/afficher", name="location_show")
     */
    public function locationShow($slug, LocationRepository $locationRepository)
    {
        
        $location = $locationRepository->findOneBy([
            'slug' => $slug
        ]);

       
        if(!$location){
            //Gérer les erreurs de requêtes 
             throw $this->createNotFoundException("cet espace n'existe pas !");
             
         }

         return $this->render('back/location/location_show.html.twig',[
             'location' => $location
             
         ]);
    }

    //*******************************************************
    //*******************************************************
    // SECTION ADMIN
    //Ajout d'un lieu de tournage
    /**
     * @Route("/creer", name = "location_create")
     * @isGranted("ROLE_EDITOR", message="Vous n'avez pas accès à cette section !")
     */
    public function taskCategoryCreate(Request $request, EntityManagerInterface $em, SluggerInterface $slugger, MessageGenerator $messageGenerator)
    {
        $location = new Location;

        $form = $this->createForm(LocationType::class, $location);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
         
            
            $toSlug = $form->getData()->getName();

            $location->setSlug(strtolower($slugger->slug($toSlug)));
            $location->setCreatedAt(new \DateTime());
            $location->setUpdatedAt(new \DateTime());

            $em->persist($location);
            $em->flush($location);
            
            $this->addFlash('success', $messageGenerator->getHappyMessage());
            
            // On redirige vers la liste
            return $this->redirectToRoute('locations_list');
        }

        return $this->render('back/location/edit.html.twig', [
            // createView() permet de récupérer
            // la représentation HTML du form
            'form' => $form->createView(),
            'cat' => 'AJOUT D\'UN LIEU DE PRESTATION',
            'btnText' => 'Ajouter',
            'btnLabel' => 'bg-aqua_velvet',
            'ico' => 'plus'
        ]);
    }

     //Modification de lieu

    /**
     * @Route("/{id}/editer",name="location_edit")
     * @isGranted("ROLE_EDITOR", message="Vous n'avez pas accès à cette section !")
     */
    public function locationEdit($id, LocationRepository $locationRepository, Request $request,SluggerInterface $slugger, EntityManagerInterface $em, MessageGenerator $messageGenerator)
    {
        $location = $locationRepository->find($id);


        if(!$location){
            //Gérer les erreurs de requêtes 
             throw $this->createNotFoundException("Ce lieu n'existe pas !");
         }

         $form = $this->createForm(LocationType::class, $location);
         //$form->setData($equipment);

         $form->handleRequest($request);

         if ($form->isSubmitted() && $form->isValid()) {

            $toSlug = $form->getData()->getName();

            $location->setSlug(strtolower($slugger->slug($toSlug)));
            $location->setUpdatedAt(new \DateTime());

            $em->flush();

            $this->addFlash('success', $messageGenerator->getHappyMessage());
            
            // On redirige vers la liste
            return $this->redirectToRoute('locations_list');
           
         }
        

        return $this->render('back/location/edit.html.twig', [
            'form' => $form->createView(),
            'item' => $location,
            'cat' => 'Modifier le lieu',
            'btnText' => 'Modifier',
            'btnLabel' => 'bg-double_dragon_skin',
            'ico' => 'edit'
        
        ]);
    }
    /**
     * @Route("/{id}/supprimer", name="location_delete", methods={"DELETE"})
     * @isGranted("ROLE_ADMIN", message="Vous n'avez pas accès à cette fonctionnalité !")
     */
    public function userDelete($id, Request $request, Location $location)
    {
        if ($this->isCsrfTokenValid('delete'.$location->getId(), $request->request->get('_token'))) {
        
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($location);
            $entityManager->flush();
        }

        return $this->redirectToRoute('users_list');
    }
}