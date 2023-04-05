<?php

namespace App\Controller\BackController;

use App\Entity\Comment;
use App\Entity\Project;
use App\Entity\Document;
use App\Form\CommentType;
use App\Form\ProjectType;
use App\Service\PdfManager;
use App\Form\UploadFileType;
use App\Service\FileManager;
use App\Entity\ProjectCategory;
use App\Form\ProjectSearchType;
use App\Form\ApplicantRatingType;
use App\Form\ProjectCategoryType;
use App\Service\MessageGenerator;
use App\Event\ProjectSuccessEvent;
use App\Repository\UserRepository;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Message;
use App\Repository\CommentRepository;
use App\Repository\ProjectRepository;
use App\Entity\ProjectRateByApplicant;
use App\Repository\DocumentRepository;
use App\Repository\LogEventRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Event\ApplicantRatingSuccessEvent;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use App\Repository\ProjectCategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProjectRateByApplicantRepository;
use Symfony\Component\String\Slugger\SluggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

/**
 * @Route("/projets")
 * @isGranted("ROLE_VIEWER", message="Vous devez être connecté !")
 */
class ProjectController extends AbstractController
{
     /**
     * @Route("/{id}/accueil-projets", name="home_projects")
     * @isGranted("ROLE_VIEWER", message="Vous n'avez pas accès à cette section !")
     */
    public function userProjects($id, UserRepository $userRepository, PaginatorInterface $paginatorInterface, Request $request, ProjectRepository $projectRepository)
    {
        $user = $userRepository->findOneBy([
            'id' => $id
        ]);

        if (!$user) {
            return $this->redirectToRoute('homepage');
        }
       

        //Formulaire de recherches
        $form = $this->createForm(ProjectSearchType::class);
        $form->handleRequest($request);
        $data = $form->getData();

        if ($data == null) {

            $projects = $projectRepository->getInProgressProjectOrderedByDateAsc();
            $userProjectsPaginated = $paginatorInterface->paginate($projects, $request->query->getInt('page',1), 10);

        }
        else{   
            
            $projects = $projectRepository->findSearch($data);
            
            $count = count($projects);
            if ($count > 0) {
               $int = $count+1;
            }
            else{
                $int = 1;
            }
            $userProjectsPaginated = $paginatorInterface->paginate($projects, $request->query->getInt('page',1), $int);
            
        }
        //$userProjects = $user->getProjects();
        if ($user == $this->getUser()) {

            return $this->render('back/project/user_projects.html.twig',[
                'user' => $user,
                'userProjectsPaginated' => $userProjectsPaginated,
                'form' => $form->createView(),
                'btnText' => 'Filtrer',
                'btnLabel' => 'bg-casandora_yellow',
                'ico' => 'filter'
            
            ]);
        }
        
        return $this->redirectToRoute('dashboard');
    }


    /**
     * @Route("/{id}/accueil-projets-2", name="home_projects_2")
     * @isGranted("ROLE_VIEWER", message="Vous n'avez pas accès à cette section !")
     */
    public function userProjects2($id, UserRepository $userRepository, PaginatorInterface $paginatorInterface, Request $request, ProjectRepository $projectRepository)
    {

        $projects = $projectRepository->getInProgressProjectOrderedByDateAsc();

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

        return $this->render('back/project/user_projects_2.html.twig',[

            'projects' => $projects,
            'cats' => $cats,
            'btnText' => 'Filtrer',
            'btnLabel' => 'bg-casandora_yellow',
            'ico' => 'filter'

        ]);


    }
     //LISTE DES PROJETS EN COURS
    /**
     * @Route("/liste-projets-en-cours", name="projects_list")
     */
    public function projectsList(ProjectRepository $projectRepository, PaginatorInterface $paginatorInterface, Request $request)
    {
        $emptyList =false;
        $projects = $projectRepository->getInProgressProjectOrderedByDateAsc();
        if (!$projects) {
            $emptyList = true;
         }

         $projetsPaginated = $paginatorInterface->paginate($projects, $request->query->getInt('page',1), 500);

         return $this->render('back/project/projects_list.html.twig',[
            'projects' => $projetsPaginated,
            'emptyList' => $emptyList
        ]);
    }
     //LISTE DES PROJETS CLOS
    /**
     * @Route("/liste-projets-clos/{date}", name="completed_projects_list")
     */
    public function completedProjectsList($date = "", ProjectRepository $projectRepository, PaginatorInterface $paginatorInterface, Request $request)
    {
        $emptyList =false;
        $nbDoneProjects = [];
        $selectedDate = "";
        //Pour menu trier par date
        $allDates = $projectRepository->getDates();

        $array = [];
        foreach ($allDates as $da){
            $d = $da['deliveryDate']->format('Y');

            array_push($array,$d);
        }
        //On récupère les années de projets dans un tableau
        //["2021","2022","2021","2021"]
        $uniqueDates = array_unique($array);
        //devient ["2021","2022"]
        $projects = $projectRepository->findAll();
        if (!$projects) {
            $emptyList = true;
        }
        //Vérification de sécurité et vérification si la date envoyée correspond à une date du tableau
        foreach ($uniqueDates as $ud){
            if($ud == $date){
                $selectedDate = $date;
            }

        }
        //Si ce n'est pas le cas alors par défault
        if ($selectedDate == ""){
            foreach ($projects as $project){
                if ( $project->getStatus() != "A faire" and $project->getStatus() != "En cours" ){
                    $nbDoneProjects[] = $project;
                }

            }
        }
        //sinon
        else{
            foreach($projects as $project){

                $dateProj = $project->getDeliveryDate();

                if ($dateProj->format('Y') == $selectedDate ) {

                    if ( $project->getStatus() != "A faire" and $project->getStatus() != "En cours" ){
                        $nbDoneProjects[] = $project;
                    }

                }

            }
        }
        //ici on a nos projets triés par date dans le tableau  $nbDoneProjects[]
         //$projetsPaginated = $paginatorInterface->paginate($nbDoneProjects, $request->query->getInt('page',1), 500);

         return $this->render('back/project/completed_projects_list.html.twig',[
            'projects' => $nbDoneProjects,
            'emptyList' => $emptyList,
             'dates' => $uniqueDates,
             'selectedDate' => $selectedDate
        ]);
    }
     //PROJET
    /**
     * @Route("/{slug}/{id}/afficher", name="project_show")
     */
    public function projectShow($slug, $id, ProjectRepository $projectRepository, Request $request, FileManager $fileManager, MessageGenerator $messageGenerator, EventDispatcherInterface $dispatcher,PaginatorInterface $paginatorInterface, CommentRepository $commentRepository, EntityManagerInterface $em)
    {
        $project = $projectRepository->findOneBy([
            'id' => $id,
            'slug' => $slug
        ]);
        if(!$project){
            throw $this->createNotFoundException("Ce projet n'existe pas !");
            
        }
        $comments = $commentRepository->findBy(['project'=> $project],['createdAt' => 'DESC']);

        $commentsPaginated = $paginatorInterface->paginate($comments, $request->query->getInt('page',1), 5);

        $form = $this->createForm(UploadFileType::class);
        $form->handleRequest($request);

        if ($this->isGranted("ROLE_EDITOR")) {
            
            if ($form->isSubmitted() && $form->isValid()) {
                //gestion du chargement de fichier via //FileManager service 
                $document = $form->get('uploadName')->getData();
                
                
                if ($document != null) {
                    $FileName = $fileManager->upload($document);
    
                    $document = new Document;
                    $document->setUploadName($FileName);
                    $document->setProject($project);
                    $document->setUploadedBy($this->getUser());
    
                    $em->persist($document);
    
                    //$projet->setCreatedAt(new \DateTime()); with LifecylceCallbacks
                    //$project->setUpdatedAt(new \DateTime()); with LifecylceCallbacks
                    //$projet->setCreatedBy($this->getUser());
                    $project->setUpdatedBy($this->getUser());
    

    
                    $em->persist($project);
                    $em->flush();
                    
                    //Envoi d'un mail de confirmation d'ajout de document au projet grace au EventSubscriber  + Log de l'event
                    $projectEvent = new ProjectSuccessEvent($project);
                    $dispatcher->dispatch($projectEvent,'projectDocument.upload');
                 }
                // On redirige vers le projet
                return $this->redirectToRoute('project_show',[
                    'slug' => $project->getSlug(),
                    'id' => $project->getId()
                ]);
            }
        }
        

        return $this->render('back/project/project_show.html.twig',[
            'comments' => $commentsPaginated,
            'project' => $project,
            'form' => $form->createView(),
            'btnText' => 'Ajouter',
            'btnLabel' => 'bg-aqua_velvet',
            'ico' => 'plus'
        ]);
    }

    // LISTE DES CATEGORIES DE PROJETS
    /**
     * @Route("/categories", name="project_categories_list")
     */
    public function projectCategories(ProjectCategoryRepository $projectCategoryRepository)
    {
        $emptyList = false;
        $projectCategory = $projectCategoryRepository->findAll();
        if (!$projectCategory) {
            $emptyList = true;
         }
        /* if(!$projectCategory){
            //Gérer les erreurs de requêtes 
             throw $this->createNotFoundException("aucune catégorie à afficher !");
             
         } */

         return $this->render('back/project/project_categories.html.twig',[
             'categories' => $projectCategory,
             'emptyList' => $emptyList
         ]);

    }
    //Export pdf du projet
    /**
     * @Route ("/{id}/pdf", name="project_as_pdf")
     */
    public function pdfTaskGenerator(Project $project, PdfManager $pdf){

        $html = $this->render('pdf/project_as_pdf.html.twig', ['project' => $project]);

        $pdf->showPdf($html);


    }

    //*******************************************************
    //*******************************************************
    // SECTION ADMIN

    //JSON -> notation d'un projet
    /**
     * @Route("/rating", name="project_rating", methods={"POST"})
     * @isGranted("ROLE_EDITOR", message="Vous n'avez pas accès à cette action !")
     */
    public function ratingProject(Request $request, ProjectRepository $projectRepository, EventDispatcherInterface $dispatcher, EntityManagerInterface $em)
    {

        $token = json_decode($request->request->get("token"));
        $project = $projectRepository->find($token);

        if (!$project) {
            //Gérer les erreurs de requêtes
            return $this->json(404, "erreur, aucun projet trouvé !");
        } else {

            $ratingProject = json_decode($request->request->get("ratingProject"));
            $ratingProjectComment = $request->request->get("ratingProjectComment");


            $project->setRating($ratingProject);
            $project->setNote($ratingProjectComment);

            $em->flush();

            //Envoi d'un mail de confirmation d'ajout de note au projet grace au EventSubscriber + Log de l'event
            $projectEvent = new ProjectSuccessEvent($project);
            $dispatcher->dispatch($projectEvent, 'project.rating');


            return $this->json('ok', 200);

        }
    }

    //Notation du projet par applicant
    /**
     * @Route("/{id<\d+>}/notation-ticket", name="project_applicant_rating_ticket")
     * @isGranted("ROLE_EDITOR", message="Vous n'avez pas accès à cette section !")
     */
    public function createRatingByApplicantTicket($id, Request $request, ProjectRepository $projectRepository, ProjectRateByApplicantRepository $projectRateByApplicantRepository, EntityManagerInterface $em, EventDispatcherInterface $dispatcher)
    {


        //étape 1 récupérer le projet via id send by js back.js
        $project = $projectRepository->find($id);
        if (!$project){
            throw $this->createNotFoundException("Ce projet n'existe pas !");
        }
        //vérifier si projet terminé
        if ($project->getStatus() !== "Fait"){
            throw $this->createNotFoundException("Ce projet n'est pas terminé");
        }
        $cat = "En cours de notation";
        //vérifier si projet n'est pas déjà noté
        $applicantRating = $project->getApplicantRating();
        if ($applicantRating){
            

            if ($applicantRating->getNote() != 'En attente'){ $cat = 'Note du projet';};

           $form = $this->createForm(ApplicantRatingType::class, $applicantRating);
           return $this->render('back/applicant/notation.html.twig', [
                'applicantRating' => $applicantRating,
               'form' => $form->createView(),
               'item' => null,
               'cat' => $cat,
           ]);
        }
        else{
            $applicantRating = new ProjectRateByApplicant;
            //étape 2 insertion de la demande en bdd, creation url cryptée à insérer dans le mail de notification applicant à voir si utile
            $applicantRating->setProject($project);
            $applicantRating->setApplicant($project->getRequestBy());
            $applicantRating->setNote('En attente');
            //Génération d'une url
            $date = new \DateTime();
            $url = $date->getTimestamp().''.$project->getId();
            $applicantRating->setUrl($url);
            $em->persist($applicantRating);
            $em->flush($applicantRating);
            //création du formulaire de notation
            $form = $this->createForm(ApplicantRatingType::class, $applicantRating);
            //étape 3 envoi du mail applicant
            //Envoi d'un mail grace au EventSubscriber + Log de l'event

            $projectEvent = new ApplicantRatingSuccessEvent($applicantRating);
            $dispatcher->dispatch($projectEvent,'project.toRate');
            //étape 4 mail envoyé redirection sur la page de notation du projet
        
            return $this->render('back/applicant/notation.html.twig', [
                 'applicantRating' => $applicantRating,
                'form' => $form->createView(),
                'item' => null,
                'cat' => $cat,
            ]);


        }

    }


    //Ajout de category de projet
    /**
     * @Route("/categorie/ajout", name="project_category_create")
     * @isGranted("ROLE_EDITOR", message="Vous n'avez pas accès à cette section !")
     */
    public function projetCategoryCreate(Request $request, SluggerInterface $slugger, MessageGenerator $messageGenerator, EntityManagerInterface $em)
    {
        $projectCategory = new ProjectCategory;

        $form = $this->createForm(ProjectCategoryType::class, $projectCategory);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
         
            
            $toSlug = $form->getData()->getName();

            $projectCategory->setSlug(strtolower($slugger->slug($toSlug)));

            

            $em->persist($projectCategory);
            $em->flush($projectCategory);
            
            $this->addFlash('success', $messageGenerator->getHappyMessage());
            
            // On redirige vers la liste
            return $this->redirectToRoute('project_categories_list');
        }
        return $this->render('back/project/edit.html.twig', [
            // createView() permet de récupérer
            // la représentation HTML du form
            'form' => $form->createView(),
            'item' => null,
            'cat' => 'Ajout d\'une catégorie de projet',
            'btnText' => 'Ajouter',
            'btnLabel' => 'bg-aqua_velvet',
            'ico' => 'plus'
            
        ]);


    }
    //Modification de catégorie

    /**
     * @Route("/categorie/{id}/editer",name="project_category_edit")
     * @isGranted("ROLE_EDITOR", message="Vous n'avez pas accès à cette section !")
     */
    public function taskEdit($id, ProjectCategoryRepository $projectCategoryRepository, Request $request, SluggerInterface $slugger, EntityManagerInterface $em, MessageGenerator $messageGenerator)
    {
        $projectCat = $projectCategoryRepository->find($id);


        if(!$projectCat){
            //Gérer les erreurs de requêtes 
             throw $this->createNotFoundException("Cette catégorie n'existe pas !");
         }

         $form = $this->createForm(ProjectCategoryType::class, $projectCat);
         //$form->setData($equipment);

         $form->handleRequest($request);

         if ($form->isSubmitted() && $form->isValid()) {

            $toSlug = $form->getData()->getName();

            $projectCat->setSlug(strtolower($slugger->slug($toSlug)));


            $em->flush();

            $this->addFlash('success', $messageGenerator->getHappyMessage());
            
            // On redirige vers la page des catégories
            return $this->redirectToRoute('project_categories_list');
           
         }
        

        return $this->render('back/project/edit.html.twig', [
            'form' => $form->createView(),
            'item' => $projectCat,
            'cat' => 'Modifier la catégorie',
            'btnText' => 'Modifier',
            'btnLabel' => 'bg-double_dragon_skin',
            'ico' => 'edit'
        
        ]);
    }

     //Ajout d'un projet
    /**
     * @Route("/ajout", name="project_create")
     * @isGranted("ROLE_EDITOR", message="Vous n'avez pas accès à cette section !")
     */
    public function projetCreate(Request $request, ProjectRepository $projectRepository, SluggerInterface $slugger, MessageGenerator $messageGenerator, FileManager $fileManager, MailerInterface $mailer, EventDispatcherInterface $dispatcher, EntityManagerInterface $em)
    {
        $projet = new Project;

        $form = $this->createForm(ProjectType::class, $projet);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            

            $toSlug = $form->getData()->getName();

            //Vérifier si le projet existe déjà
            $date = $form->getData()->getDeliveryDate();
            //En comparant la date et le nom du projet
            //Vérification si le nom de projet existe déja dans la base
            $existingProjects = $projectRepository->findBy(['name' => $toSlug]);
            $goodToGo = true;
            //Si oui comparaison des dates de livraison
            foreach ($existingProjects as $projeect) {
                //Si oui on bloque la validation
                if ($projeect->getDeliveryDate() == $date) {
                    $goodToGo = false;

                } //On valide
                else {
                    $goodToGo = true;
                }
            }

            if ($goodToGo == true){

                $projet->setSlug(strtolower($slugger->slug($toSlug)));

                //$projet->setCreatedAt(new \DateTime()); with LifecylceCallbacks
                //$projet->setUpdatedAt(new \DateTime()); with LifecylceCallbacks
                $projet->setCreatedBy($this->getUser());
                $projet->setUpdatedBy($this->getUser());


                $em->persist($projet);
                $em->flush($projet);

                //Envoi d'un mail de confirmation d'ajout de nouveau projet grace au EventSubscriber + Log de l'event
                $projectEvent = new ProjectSuccessEvent($projet);
                $dispatcher->dispatch($projectEvent,'project.success');

                $this->addFlash('success', $messageGenerator->getHappyMessage());

                // On redirige vers le projet
                return $this->redirectToRoute('project_show',[
                    'slug' => $projet->getSlug(),
                    'id' => $projet->getId()
                ]);

            }
            else{
                $this->addFlash('warning', 'Ce projet existe déjà');
            }



        }
        return $this->render('back/project/edit.html.twig', [
            // createView() permet de récupérer
            // la représentation HTML du form
            'form' => $form->createView(),
            'item' => null,
            'cat' => 'Ajout d\'un projet',
            'btnText' => 'Ajouter',
            'btnLabel' => 'bg-aqua_velvet',
            'ico' => 'plus'
            
        ]);


    }

    //Modification d'un projet
    /**
     * @Route("/{id}/editer",name="project_edit")
     * @isGranted("ROLE_EDITOR", message="Vous n'avez pas accès à cette section !")
     */
    public function projectEdit($id, ProjectRepository $projectRepository, Request $request, SluggerInterface $slugger, EntityManagerInterface $em, MessageGenerator $messageGenerator, EventDispatcherInterface $dispatcher)
    {
        $project = $projectRepository->find($id);

        if(!$project){
            //Gérer les erreurs de requêtes 
             throw $this->createNotFoundException("Ce projet n'existe pas !");
         }
           if($project->getApplicantRating()){
               $this->addFlash('warning', "Ce projet a été noté et ne peut donc plus être modifié !");
               // On redirige vers le projet
               return $this->redirectToRoute('project_show',[
                   'slug' => $project->getSlug(),
                   'id' => $project->getId(),
               ]);
           }


         $form = $this->createForm(ProjectType::class, $project);
         //$form->setData($equipment);

         $form->handleRequest($request);

         if ($form->isSubmitted() && $form->isValid()) {

            $toSlug = $form->getData()->getName();
            $status = $form->getData()->getStatus();
           
            if ($status == "Refusé") {
               $tasks = $project->getTasks();
               foreach ($tasks as $task) {
                   # code...tasks
                   $task->setStatus('Annulée');
               }
            }
            if ($status == "Annulé") {
                $tasks = $project->getTasks();
                foreach ($tasks as $task) {
                    # code...tasks
                    $task->setStatus('Annulée');
                }
             }
            if ($status == "Fait") {
                $tasks = $project->getTasks();
                foreach ($tasks as $task) {
                    # code...tasks
                    if($task->getStatus() != "Annulée"){
                        $task->setStatus('Faite');
                    }
                }
             }
            $project->setSlug(strtolower($slugger->slug($toSlug)));

            //$project->setUpdatedAt(new \DateTime());  with LifecylceCallbacks
            $project->setUpdatedBy($this->getUser());

            $em->flush();
            //Envoi d'un mail de confirmation de modification de projet grace au EventSubscriber + Log de l'event
            $projectEvent = new ProjectSuccessEvent($project);
            $dispatcher->dispatch($projectEvent,'project.updated');

            $this->addFlash('success', $messageGenerator->getHappyMessage());
            
            // On redirige vers le projet
            return $this->redirectToRoute('project_show',[
                'slug' => $project->getSlug(),
                'id' => $project->getId(),
            ]);
           
         }
        

        return $this->render('back/project/edit.html.twig', [
            'form' => $form->createView(),
            'item' => $project,
            'cat' => 'Modifier le projet',
            'btnText' => 'Modifier',
            'btnLabel' => 'bg-double_dragon_skin',
            'ico' => 'edit'
        
        ]);
    }

    //Modification d'un projet
    /**
     * @Route("/{id}/commentaire/ajout",name="project_comment_add")
     * @isGranted("ROLE_EDITOR", message="Vous n'avez pas accès à cette section !")
     */
    public function commentAdd($id,ProjectRepository $projectRepository, Request $request, SluggerInterface $slugger, MessageGenerator $messageGenerator, EventDispatcherInterface $dispatcher, EntityManagerInterface $em)
    {
        $comment = new Comment();
        $project = $projectRepository->find($id);

        if(!$project){
            //Gérer les erreurs de requêtes 
             throw $this->createNotFoundException("Ce projet n'existe pas !");
         }

        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            

            //$toSlug = $form->getData()->getTitle();
            //$comment->setSlug(strtolower($slugger->slug($toSlug)));

            //$projet->setCreatedAt(new \DateTime()); with LifecylceCallbacks
            $project->setUpdatedAt(new \DateTime()); 
            $comment->setCreatedBy($this->getUser());
            $comment->setProject($project);
           
            $project->setUpdatedBy($this->getUser());
            $em->persist($comment);
            $em->flush();
            //Envoi d'un mail de confirmation d'ajout de commentaire au projet grace au EventSubscriber  + Log de l'event
            $projectEvent = new ProjectSuccessEvent($project);
            $dispatcher->dispatch($projectEvent,'project.commented');
            
            $this->addFlash('success', $messageGenerator->getHappyMessage());
            
            // On redirige vers le projet
            return $this->redirectToRoute('project_show',[
                'slug' => $project->getSlug(),
                'id' => $project->getId(),
                '_fragment' => 'actu'
            ]);
        }
        return $this->render('back/comment/edit.html.twig', [
            // createView() permet de récupérer
            // la représentation HTML du form
            'form' => $form->createView(),
            'item' => $project,
            'cat' => 'Ajout d\'une actualité',
            'btnText' => 'Ajouter',
            'btnLabel' => 'bg-aqua_velvet',
            'ico' => 'plus'
            
        ]);

    }
    //Modification d'une actualité
    /**
     * @Route("/{pid}/commentaire/{id}/editer",name="project_comment_edit")
     */
    public function commentEdit($id, $pid, CommentRepository $commentRepository,ProjectRepository $projectRepository, Request $request, MessageGenerator $messageGenerator, EventDispatcherInterface $dispatcher, EntityManagerInterface $em)
    {
       $comment = $commentRepository->findOneBy(['id'=>$id]);
       $project = $projectRepository->find($pid);

       if(!$comment){
        //Gérer les erreurs de requêtes 
         throw $this->createNotFoundException("Cette actualité n'existe pas !");
        }
        //VOTER CommentVoter
        $this->denyAccessUnlessGranted('COMMENT_EDIT',$comment,'Vous ne pouvez pas modifier cette actualité');

        $form = $this->createForm(CommentType::class, $comment);
        //$form->setData($equipment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


           
            $em->flush();
            //Envoi d'un mail de confirmation de modification de commentaire au projet grace au EventSubscriber  + Log de l'event
            $projectEvent = new ProjectSuccessEvent($project);
            $dispatcher->dispatch($projectEvent,'projectComment.update');
            

            $this->addFlash('success', $messageGenerator->getHappyMessage());
            
            // On redirige vers le projet
            return $this->redirectToRoute('project_show',[
                'slug' => $project->getSlug(),
                'id' => $project->getId(),
                '_fragment' => 'actu'
            ]);
        
        }
    

        return $this->render('back/comment/edit.html.twig', [
            // createView() permet de récupérer
            // la représentation HTML du form
            'form' => $form->createView(),
            'item' => $project,
            'cat' => 'Modification de l\'actualité',
            'btnText' => 'Modifier',
            'btnLabel' => 'bg-aqua_velvet',
            'ico' => 'plus'
            
        ]);

    }
    //Suppression d'une actualité
    /**
     * @Route("/{id}/supprimer",name="comment_delete")
     */
    public function commentDelete($id, Request $request, CommentRepository $commentRepository, MessageGenerator $messageGenerator, EntityManagerInterface $em)
    {
        $comment = $commentRepository->findOneBy(['id'=>$id]);

        if(!$comment){
            //Gérer les erreurs de requêtes 
             throw $this->createNotFoundException("Cette actualité n'existe pas !");
         }
        $linkedProject = $comment->getProject();
        //VOTER CommentVoter
        $this->denyAccessUnlessGranted('COMMENT_DELETE',$comment,'Vous ne pouvez pas modifier cette actualité');

        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
        
          
            $em->remove($comment);
            
           $em->flush();

            $this->addFlash('success', $messageGenerator->getHappyMessage());
        }

    
        // On redirige vers le projet
        return $this->redirectToRoute('project_show',[
            'slug' => $linkedProject->getSlug(),
            'id' => $linkedProject->getId(),
            '_fragment' => 'actu'
        ]);
    }

    /**
     * @Route("/{pid}/document/{id}/telecharger", name="project_document_download")
    */
    public function downloadProjectDocument($pid, $id, Request $request, Document $document, FileManager $fileManager, ProjectRepository $projectRepository, DocumentRepository $documentRepository)
    {
        
        $valid = $documentRepository->findOneBy(['id' => $id,'project' => $pid]);
        if ($valid) {
            $filePath = $fileManager->download($document);

            if ($filePath) {
             return $this->file($filePath);
            }   
        }
        else{
             
             throw $this->createNotFoundException("Erreur de chargement du fichier !");
        }
        
      
    }
    
    /**
     * @Route("/{pid}/document/{id}/supprimer", name="project_document_remove", methods={"DELETE"})
     * @isGranted("ROLE_EDITOR", message="Vous n'avez pas accès à cette fonctionnalité !")
     */
    public function removeDocumentFromProject($pid,$id, Request $request, Document $document, FileManager $fileManager, ProjectRepository $projectRepository, EventDispatcherInterface $dispatcher, EntityManagerInterface $em)
    {

        $project = $projectRepository->findOneBy(['id' => $pid]);

        if ($this->isCsrfTokenValid('delete'.$document->getId(), $request->request->get('_token'))) {
          
            //Service from FileManager
            $fileManager->delete($document);
           

          
            $em->remove($document);
            $em->flush();
             //Envoi d'un mail de confirmation de modification de projet grace au EventSubscriber
             //$projectEvent = new ProjectSuccessEvent($project);
             //$dispatcher->dispatch($projectEvent,'project.updated');
        }

        return $this->redirectToRoute('project_show',[
            'slug' => $project->getSlug(),
            'id' => $project->getId()
        ]);
        
       
    }

    //Suppression d'une tâche
    /**
     * @Route("/{id}/supprimer/{slug}",name="project_delete")
     * @isGranted("ROLE_ADMIN", message="Votre rôle ne permet pas de supprimer ce projet !")
     */
    public function projectDelete($id, Request $request, Project $project, EventDispatcherInterface $dispatcher, DocumentRepository $documentRepository, FileManager $fileManager, LogEventRepository $logEventRepository, CommentRepository $commentRepository, EntityManagerInterface $em)
    {

        if ($this->isCsrfTokenValid('delete'.$project->getId(), $request->request->get('_token'))) {

            if ($project->getStatus() != "Annulé"){
                throw $this->createNotFoundException("Le statut du projet ne permet pas de le supprimé !");
            }
            //On récupère les documents liés
            $docs = $documentRepository->findBy(['project' => $id]);
            //si des documents existent alors on les supprime
            if($docs){
                foreach ($docs as $doc ){
                    $project->removeDocument($doc);
                    $em->remove($doc);
                    //Service from FileManager
                    $fileManager->delete($doc);
                }
            }
            //On récupère les logs liés
            $logs = $logEventRepository->findBy(['project' => $project]);
            //On supprime les logs liés
            foreach ($logs as $log){
                $project->removeLogEvent($log);
                $em->remove($log);
            }
            //On récupère les commentaires (actualités)
            $actus = $commentRepository->findBy(['project' => $project]);
            //si des actus existent alors on les supprime
            if($actus) {
                foreach ($actus as $act) {
                    $project->removeComment($act);
                    $em->remove($act);
                }
            }
            //on récupère les tâches liées
            $tasks = $project->getTasks();
           $errorMsg ="";
            foreach ($tasks as $task){
                if ($task){
                   $errorMsg = "Des tâches sont liées à ce projet, merci de les supprimer.";
                }
            }
            if ($errorMsg){
                $this->addFlash('warning', $errorMsg);
                //Redirection vers le projet
                return $this->redirectToRoute('project_show',['id' => $project->getId(), 'slug' => $project->getSlug()]);
            }
            else{
                $this->addFlash('success', 'Projet supprimé !');
                $em->remove($project);
                $em->flush();
            }




        }
        //Redirection vers le projet
        return $this->redirectToRoute('projects_list');

    }

}