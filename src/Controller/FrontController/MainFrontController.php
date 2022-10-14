<?php

namespace App\Controller\FrontController;

use App\Entity\Equipment;
use App\Event\ProjectSuccessEvent;
use App\Form\ApplicantRatingType;
use App\Repository\EquipmentCategoryRepository;
use App\Repository\EquipmentRepository;
use App\Repository\EquipmentTypeRepository;
use App\Repository\ProjectRateByApplicantRepository;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainFrontController extends AbstractController
{
    private $em;
    /**
     * Manager des entitées doctrine EntityManagerInterface. 
     * Ici on l'appelle dans le constructeur. 
     * Peut également être utilisé dans les methodes par injection de dépendance,
     * ou dans la méthode comme suit : $em = $this->getDoctrine()->getManager();
     */
    public function __construct(EntityManagerInterface $entityManagerInterface)
    {
        $this->em = $entityManagerInterface;
    }

    // HOME PAGE


    /**
     * @Route("/" , name="homepage")
     */
    public function homepage()

    {
       
        // Pour récupérer le EquipmentRepository soit par injection de dép. 
        // Soit :
        // $equipmentRepository = $this->em->getRepository(Equipment::class);

        // Les setters et les getters renvoient un objet dans on peut chainer les setters
        return $this->redirect('https://www.education.gouv.fr/');
       
        //return $this->render("front/homepage.html.twig");
    }

    /**
     * @Route("/exit" , name="exit")
     */
    public function exit()

    {
        return $this->render("front/exit.html.twig");
    }

    //Notation du projet par demandeur
    /**
     * @Route("/{id}/notation", name="project_applicant_rating")
     */
    public function ratingByApplicantTicket($id, Request $request, ProjectRateByApplicantRepository $projectRateByApplicantRepository, EntityManagerInterface $em, EventDispatcherInterface $dispatcher)
    {


        //Vérification de sécurité if existe ou redirection
        $projectToRate = $projectRateByApplicantRepository->findOneBy(['url' => $id]);

        if (!$projectToRate){
            throw $this->createNotFoundException("Petite erreur technique ... ou humaine !");
        }
        if ($projectToRate->getNote() != "En attente") {
           return $this->render('front/exit.html.twig',[
               'title' => 'Vous avez déjà donné votre avis sur ce projet, merci.',
               'picture' => 'RATE_2.svg'
           ]);
        }

        // affichage du formulaire ...
        //création du formulaire de notation
        $form = $this->createForm(ApplicantRatingType::class, $projectToRate);

        $form->handleRequest($request);
        //traitement du formulaire
        if ($form->isSubmitted() && $form->isValid()) {

            $em->flush($projectToRate);

            // EventSubscriber + Log de l'event
            $projectEvent = new ProjectSuccessEvent($projectToRate->getProject());
            $dispatcher->dispatch($projectEvent, 'project.rated');

            return $this->render('front/exit.html.twig',[
                'title' => 'Merci !',
                'picture' => 'THANKS.svg'
            ]);

        }




        return $this->render('front/notation.html.twig', [
            'projectToRate' => $projectToRate,
            'form' => $form->createView(),
            'item' => null,
            'cat' => 'Votre notation',
            'btnText' => 'Envoyer',
            'btnLabel' => 'bg-red_flag',
            'ico' => 'vote-yea'
        ]);
    }

    /**
     * @Route("/mediatheque" , name="media")
     */
    public function media(ProjectRepository $projectRepository)
    {
        $projects = $projectRepository->findAll();

        $cats = "";
        if ($projects){
            //Filters by category
            $projectCatName = [];
            foreach ($projects as $project){
                $cat = $project->getCategory()->getName();
                array_push($projectCatName,$cat);
            }
            $array2 = array_count_values($projectCatName);
            /* 0 => "Signature de convention"
             1 => "Cérémonie"
             2 => "Signature de convention"
             3 => "Cérémonie"
             4 => "Séminaire, colloque, conférence, table ronde"
             5 => "Réunion"
             6 => "Séminaire, colloque, conférence, table ronde"
             7 => "Production"
             8 => "Réunion"*/
            $cats = array_keys($array2);
            /*0 => "Signature de convention"
            1 => "Cérémonie"
            2 => "Séminaire, colloque, conférence, table ronde"
            3 => "Réunion"
            4 => "Production"*/

        }

        return $this->render('front/media/media.html.twig',[

            'projects' => $projects,
            'cats' => $cats,
            'btnText' => 'Filtrer',
            'btnLabel' => 'bg-casandora_yellow',
            'ico' => 'filter'

        ]);
    }

    /**
     * @Route("/mediatheque/{id}/{slug}" , name="media_show")
     */
    public function media_show($id, $slug,ProjectRepository $projectRepository)
    {
        $project = $projectRepository->find($id);

        if(!$project){
            throw $this->createNotFoundException("Cette vidéo n'existe plus !");
        }
        elseif ($project->getPubVideoStatus() == 0){
            throw $this->createNotFoundException("Cette vidéo ne peut pas être visualisée !");
        }
        else {
            return $this->render('front/media/media_show.html.twig',[
                'project' => $project
            ]);


        }


    }

    

}