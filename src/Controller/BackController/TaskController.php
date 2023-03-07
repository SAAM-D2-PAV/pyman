<?php

namespace App\Controller\BackController;

use App\Repository\LogEventRepository;

use App\Service\PdfManager;
use DateTime;
use DateTimeZone;
use App\Entity\Task;
use App\Form\TaskType;
use App\Entity\Document;
use App\Entity\Equipment;
use App\Entity\TaskCategory;
use App\Form\UploadFileType;
use App\Service\FileManager;
use App\Form\TaskCategoryType;
use App\Event\TaskSuccessEvent;
use App\Form\TaskEquipmentType;
use App\Repository\DocumentRepository;
use App\Service\MessageGenerator;
use App\Repository\TaskRepository;
use App\Repository\ProjectRepository;
use App\Repository\EquipmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TaskCategoryRepository;
use Dompdf\Dompdf;
use Dompdf\Options;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
/**
 * @Route("/taches")
 * @isGranted("ROLE_VIEWER", message="Vous devez être connecté !")
 */
class TaskController extends AbstractController
{


    //LISTE DES TACHES
    /**
     * @Route("/toutes-les-taches/{date}", name="tasks_list")
     */
    public function taskList($date = "", TaskRepository $taskRepository, PaginatorInterface $paginatorInterface, Request $request)
    {
        $emptyList =false;
        $dateSelect = "";
        $selectedDate = "";
        //Pour menu trier par date
        $allDates = $taskRepository->getDates();
        $array = [];
        foreach ($allDates as $da){
            $d = $da['endDate']->format('Y');
            array_push($array,$d);
        }
       
        $uniqueDates = array_unique($array);
       
        //Vérification de sécurité
        foreach ($uniqueDates as $ud){
            if($ud == $date){
                $selectedDate = $date;
            }
        }
      
        $tasks = $taskRepository->getTaskOrderedByDateAscDql($selectedDate);
        if (!$tasks) {
            $emptyList = true;
         }
         //$tasksPaginated = $paginatorInterface->paginate($tasks, $request->query->getInt('page',1), 5000);
         return $this->render('back/task/tasks_list.html.twig',[
            'tasks' => $tasks,
            'emptyList' => $emptyList,
            'dates' => $uniqueDates,
            'selectedDate' => $selectedDate
        ]);
    }
    //LISTE DES TACHES A FAIRE
    /**
     * @Route("/taches-a-faire", name="todo_tasks_list")
     */
    public function todoTaskList(TaskRepository $taskRepository, PaginatorInterface $paginatorInterface, Request $request)
    {
        $emptyList =false;
        $tasks = $taskRepository->findBy([
            'status' => 'A faire'
        ]);
        if (!$tasks) {
            $emptyList = true;
         }
         //$tasksPaginated = $paginatorInterface->paginate($tasks, $request->query->getInt('page',1), 500);
         return $this->render('back/task/todo_tasks_list.html.twig',[
            'tasks' => $tasks,
            'emptyList' => $emptyList,
            'dates' => [],
        ]);
    }
    //LISTE DES TACHES A FAIRE
    /**
     * @Route("/taches-en-cours", name="inprogress_tasks_list")
     */
    public function inprogressTaskList(TaskRepository $taskRepository, PaginatorInterface $paginatorInterface, Request $request)
    {
        $emptyList =false;
        $tasks = $taskRepository->findBy([
            'status' => 'En cours'
        ]);
        if (!$tasks) {
            $emptyList = true;
         }
         //$tasksPaginated = $paginatorInterface->paginate($tasks, $request->query->getInt('page',1), 500);
         return $this->render('back/task/inprogress_tasks_list.html.twig',[
            'tasks' => $tasks,
            'emptyList' => $emptyList,
            'dates' => [],
        ]);
    }

    // LISTE DES CATEGORIES DE TACHES
    /**
     * @Route("/categories", name="task_categories_list")
     */
    public function taskCategories(TaskCategoryRepository $taskCategoryRepository)
    {
        $emptyList = false;
        $taskcategory = $taskCategoryRepository->findAll();
        if (!$taskcategory) {
            $emptyList = true;
         }
        /* if(!$taskcategory){
            //Gérer les erreurs de requêtes 
             throw $this->createNotFoundException("aucune tache à afficher !");
             
         } */

         return $this->render('back/task/task_categories.html.twig',[
             'categories' => $taskcategory,
             'emptyList' => $emptyList
         ]);

    }

    //AFFICHER UNE TACHE
    /**
     * @Route("/{id}/afficher", name="task_show")
     */
    public function taskShow($id, TaskRepository $taskRepository, Request $request, FileManager $fileManager, EventDispatcherInterface $dispatcher, EntityManagerInterface $em)
    {
        $task = $taskRepository->findOneBy([
            'id' => $id
        ]);
        if(!$task){
            throw $this->createNotFoundException("Cette tâche n'existe pas !");
        }
        
        $form = $this->createForm(UploadFileType::class);
        $form->handleRequest($request);

        if ($this->isGranted("TASK_EDIT", $task)) {
            
            if ($form->isSubmitted() && $form->isValid()) {
                //gestion du chargement de fichier via //FileManager service 
                $document = $form->get('uploadName')->getData();
                
                
                if ($document != null) {
                    $FileName = $fileManager->upload($document);
    
                    $document = new Document;
                    $document->setUploadName($FileName);
                    //$document->setProject($project);
                    $document->setTask($task);
                    $document->setUploadedBy($this->getUser());
    
                   $em->persist($document);
    
                    //$projet->setCreatedAt(new \DateTime()); with LifecylceCallbacks
                    //$project->setUpdatedAt(new \DateTime()); with LifecylceCallbacks
                    //$projet->setCreatedBy($this->getUser());
                    $task->setUpdatedBy($this->getUser());
    
                    $em->persist($task);
                    $em->flush();

                    //Envoi d'un mail de confirmation d'ajout de document à la tâche grace au EventSubscriber + Log de l'event
                    $taskEvent = new TaskSuccessEvent($task);
                    $dispatcher->dispatch($taskEvent,'taskDocument.upload');
                 }
    
                return $this->redirectToRoute('task_show',[
                    'id' => $id
                ]);
            }
        }


        return $this->render('back/task/task_show.html.twig',[
            'task' => $task,
            'form' => $form->createView(),
            'btnText' => 'Ajouter un document',
            'btnLabel' => 'bg-aqua_velvet',
            'ico' => 'plus'
        ]);
    }

    //Export pdf de la tache
    /**
     * @Route ("/{id}/pdf", name="task_as_pdf")
     */
    public function pdfTaskGenerator(Task $task, PdfManager $pdf){

        $html = $this->render('pdf/task_as_pdf.html.twig', ['task' => $task]);

        $pdf->showPdf($html);

        
    }

    //*******************************************************
    //*******************************************************
    // SECTION ADMIN

    //Ajout d"une tâche à un projet 
    /**
     * @Route("/project/{id}/ajouter", name="task_project_add")
     * @isGranted("ROLE_EDITOR", message="Vous n'avez pas accès à cette section !")
     */
    public function addTaskToProject($id, Request $request, TaskRepository  $taskRepository, MessageGenerator $messageGenerator, SluggerInterface $slugger, ProjectRepository $projectRepository,  EventDispatcherInterface $dispatcher, EntityManagerInterface $em)
    {
        $task = new Task;
        $project = $projectRepository->find($id);

        if (!$project) {
            throw $this->createNotFoundException("Ce projet n'existe pas !");
        }
        $projectStatus = $project->getStatus();

        if ($projectStatus == "Refusé" OR $projectStatus == "Annulé" OR $projectStatus == "Fait") {
            # code...
            throw $this->createNotFoundException("Le statut du projet ne permet pas l'ajout d'une tâche !");
        }
        $task->setProject($project);

        $form = $this->createForm(TaskType::class, $task);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //Vérifier si la tâche existe déjà
            $toSlug = $form->getData()->getName();
            $taskStartDate = $form->getData()->getStartDate();
            $taskEndDate = $form->getData()->getEndDate();
            //En comparant la date et le nom de la tâche
            //Vérification si le nom de la tâche existe déja dans la base
            $existingTasks = $taskRepository->findBy(['name' => $toSlug]);
            $goodToGo = true;

            //Si oui comparaison des dates de livraison
            foreach ($existingTasks as $taask){
                //Si oui on bloque la validation
                if ($taask->getStartDate() == $taskStartDate AND $taask->getEndDate() == $taskEndDate AND $project = $taask->getProject()){
                    $goodToGo = false;

                }
                //On valide
                else{
                    $goodToGo = true;
                }
            }
            if ($goodToGo == true){
                //$task->setCreatedAt(new \DateTime()); with LifecylceCallbacks
                $task->setUpdatedAt(new \DateTime());
                $task->setCreatedBy($this->getUser());
                $task->setUpdatedBy($this->getUser());

                $project->setUpdatedBy($this->getUser());
                $project->setUpdatedAt(new \DateTime());

                $task->setProject($projectRepository->find($id));

                $task->setSlug(strtolower($slugger->slug($toSlug)));

               
                $em->persist($task);
                $em->persist($project);


                $em->flush();

                //send mail to subscriber user
                //Envoi d'un mail de confirmation d'ajout de tache grace au EventSubscriber + Log de l'event

                $taskEvent = new TaskSuccessEvent($task);
                $dispatcher->dispatch($taskEvent,'task.success');

                $this->addFlash('success', $messageGenerator->getHappyMessage());

                // On redirige vers la tache
                return $this->redirectToRoute('task_show',[
                    'id' => $task->getId()
                ]);
            }
            else{
                $this->addFlash('warning', 'Cette tâche existe déjà');
            }

        }
        return $this->render('back/task/edit_task.html.twig', [
            // createView() permet de récupérer
            // la représentation HTML du form
            'form' => $form->createView(),
            'item' => $project,
            'addTasktoProject' => true,
            'cat' => 'Ajout d\'une tâche ',
            'project' => $task->getProject(),
            'btnText' => 'Ajouter',
            'btnLabel' => 'bg-aqua_velvet',
            'ico' => 'plus'
        ]);

    }

    //Ajout d'une tâche
    /**
     * @Route("/creer", name="task_create")
     * @isGranted("ROLE_EDITOR", message="Vous n'avez pas accès à cette section !")
     */
    public function taskCreate(Request $request, TaskRepository $taskRepository, SluggerInterface $slugger, MessageGenerator $messageGenerator, EventDispatcherInterface $dispatcher, EntityManagerInterface $em)
    {
        $task = new Task;
        $form = $this->createForm(TaskType::class, $task);


        $startDate = $request->get('start');
        $endDate = $request->get('end');

        if ($startDate) {
            $sD = new \DateTime($startDate);
            $form->get('startDate')->setData($sD);
            $form->get('startHour')->setData($sD);
           
        }
        if ($endDate) {
            $eD = new \DateTime($endDate);
            $form->get('endDate')->setData($eD);
            $form->get('endHour')->setData($eD);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

           

            $linkedProject = $form->getData()->getProject();

            //Vérifier si la tâche existe déjà
            $toSlug = $form->getData()->getName();
            //$taskStartDate = $form->getData()->getStartDate();
            //$taskEndDate = $form->getData()->getEndDate();
            //En comparant la date et le nom de la tâche
            //Vérification si le nom de la tâche existe déja dans la base
            //$existingTasks = $taskRepository->findBy(['name' => $toSlug]);
            $goodToGo = true;

            // //Si oui comparaison des dates de livraison
            // foreach ($existingTasks as $taask){
            //     //Si oui on bloque la validation
            //     if ($taask->getStartDate() == $taskStartDate AND $taask->getEndDate() == $taskEndDate AND $linkedProject == $taask->getProject()){
            //         $goodToGo = false;
            //         dd($taask->getProject());

            //     }
            //     //On valide
            //     else{
            //         $goodToGo = true;
            //     }
            // }
            if ($goodToGo == true){

                $linkedProject->setUpdatedBy($this->getUser());
                $linkedProject->setUpdatedAt(new \DateTime());

                //$task->setCreatedAt(new \DateTime()); with LifecylceCallbacks
                $task->setUpdatedAt(new \DateTime());
                $task->setCreatedBy($this->getUser());
                $task->setUpdatedBy($this->getUser());
                $task->setSlug(strtolower($slugger->slug($toSlug)));


                $em->persist($task);
                $em->flush($task);

                //send mail to subscriber user
                //Envoi d'un mail de confirmation d'ajout de tache grace au EventSubscriber + Log de l'event
                $taskEvent = new TaskSuccessEvent($task);
                $dispatcher->dispatch($taskEvent,'task.success');

                $this->addFlash('success', $messageGenerator->getHappyMessage());

                // On redirige vers la tache
                return $this->redirectToRoute('task_show',[
                    'id' => $task->getId()
                ]);

            }
            // else{
            //     $this->addFlash('danger', 'Sur le projet '.$linkedProject->getName().', une tâche portant le même nom existe déjà sur ce créneau horaire !');
            // }
        }
        return $this->render('back/task/edit_task.html.twig', [
            // createView() permet de récupérer
            // la représentation HTML du form
            'form' => $form->createView(),
            'cat' => 'Ajout d\'une tâche',
            'item' => null,
            'btnText' => 'Créer',
            'btnLabel' => 'bg-aqua_velvet',
            'ico' => 'plus'
        ]);

    }

    //Modification d'une tâche
    /**
     * @Route("/{id}/editer",name="task_edit")
     * @isGranted("ROLE_OWNER", message="Votre rôle ne permet pas de modifier cette tâche !")
     */
    public function taskEdit($id, TaskRepository $taskRepository, Request $request, SluggerInterface $slugger, EntityManagerInterface $em, MessageGenerator $messageGenerator, EventDispatcherInterface $dispatcher)
    {
        $task = $taskRepository->find($id);
        

        if(!$task){
            //Gérer les erreurs de requêtes 
             throw $this->createNotFoundException("Cette tâche n'existe pas !");
         }
         //VOTER TaskVoter
         $this->denyAccessUnlessGranted('TASK_EDIT',$task,'Vous ne pouvez pas modifier cette tâche');
         $projectStatus = $task->getProject()->getStatus();

         if ($projectStatus == "Refusé" OR $projectStatus == "Annulé" OR $projectStatus == "Fait") {
             # code...
             throw $this->createNotFoundException("Le statut du projet ne permet pas la modification de cette tâche !");
         }
         $form = $this->createForm(TaskType::class, $task);
         //$form->setData($equipment);

         $form->handleRequest($request);
        

         if ($form->isSubmitted() && $form->isValid()) {

    
            $linkedProject = $form->getData()->getProject();
            $linkedProject->setUpdatedBy($this->getUser());
            $linkedProject->setUpdatedAt(new \DateTime());

            //$task->setUpdatedAt(new \DateTime()); with LifecylceCallbacks
            $task->setUpdatedBy($this->getUser());
            $toSlug = $form->getData()->getName();
            $task->setSlug(strtolower($slugger->slug($toSlug)));

            $em->flush();
            //send mail to subscriber user
            //Envoi d'un mail de modification de tache grace au EventSubscriber + Log de l'event
            $taskEvent = new TaskSuccessEvent($task);
            $dispatcher->dispatch($taskEvent,'task.updated');

           

            $this->addFlash('success', $messageGenerator->getHappyMessage());
            
            // On redirige vers la tache
            return $this->redirectToRoute('task_show',[
                'id' => $task->getId()
            ]);
          
           
         }
        

        return $this->render('back/task/edit_task.html.twig', [
            'form' => $form->createView(),
            'item' => $task,
            'addTasktoProject' => true,
            'project' => $task->getProject(),
            'cat' => 'Modifier la tâche',
            'btnText' => 'Modifier',
            'btnLabel' => 'bg-double_dragon_skin',
            'ico' => 'edit'
        
        ]);
    }
    //Suppression d'une tâche
    /**
     * @Route("/{id}/supprimer",name="task_delete")
     * @isGranted("ROLE_ADMIN", message="Votre rôle ne permet pas de supprimer cette tâche !")
     */
    public function taskDelete($id, Request $request, Task $task, EventDispatcherInterface $dispatcher, DocumentRepository $documentRepository, FileManager $fileManager, LogEventRepository $logEventRepository, EntityManagerInterface $em)
    {

        if ($this->isCsrfTokenValid('delete'.$task->getId(), $request->request->get('_token'))) {
        


            //Récupération du projet lié
            $linkedProj = $task->getProject();
            //Récupération des owners de la tache
            $owners = $task->getOwners();
            //On récupère les documents liés
            $docs = $documentRepository->findBy(['task' => $id]);
            //On récupère les logs liés
            $logs = $logEventRepository->findBy(['task' => $task]);
            //On supprime les logs liés
            foreach ($logs as $log){
                $task->removeLogEvent($log);
                $em->remove($log);
            }
            //si des documents existent alors on les supprime
            if($docs){
                foreach ($docs as $doc ){
                    $task->removeDocument($doc);
                    $em->remove($doc);
                    //Service from FileManager
                    $fileManager->delete($doc);
                }
            }
            //si des owners existent alors on les supprime
            foreach ($owners as $owner){
                $owner->removeTask($task);
            }

            $em->remove($task);
            $em->flush();
            

        }
        //Redirection vers le projet
        return $this->redirectToRoute('project_show',[
            'slug' => $linkedProj->getSlug(),
            'id' => $linkedProj->getId()
        ]);
        
    }

    //Ajout d'une catégorie de tâche
    /**
     * @Route("/categorie/creer", name = "task_category_create")
     * @isGranted("ROLE_EDITOR", message="Vous n'avez pas accès à cette section !")
     */
    public function taskCategoryCreate(Request $request, EntityManagerInterface $em, SluggerInterface $slugger, MessageGenerator $messageGenerator)
    {
        $taskCategory = new TaskCategory;

        $form = $this->createForm(TaskCategoryType::class, $taskCategory);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
         
            
            $toSlug = $form->getData()->getName();

            $taskCategory->setSlug(strtolower($slugger->slug($toSlug)));

            $em->persist($taskCategory);
            $em->flush($taskCategory);
            
            $this->addFlash('success', $messageGenerator->getHappyMessage());
            
            // On redirige vers la liste
            return $this->redirectToRoute('task_categories_list');
        }

        return $this->render('back/task/edit_task_category.html.twig', [
            // createView() permet de récupérer
            // la représentation HTML du form
            'form' => $form->createView(),
            'cat' => 'Ajout d\'une catégorie de tâche',
            'btnText' => 'Ajouter',
            'btnLabel' => 'bg-aqua_velvet',
            'ico' => 'plus'
        ]);
    }

    

    //Modification de catégorie
    /**
     * @Route("/categorie/{id}/editer",name="task_category_edit")
     * @isGranted("ROLE_EDITOR", message="Vous n'avez pas accès à cette section !")
     */
    public function taskCatEdit($id, TaskCategoryRepository $taskCategoryRepository, Request $request, SluggerInterface $slugger, EntityManagerInterface $em, MessageGenerator $messageGenerator)
    {
        $taskCat = $taskCategoryRepository->find($id);


        if(!$taskCat){
            //Gérer les erreurs de requêtes 
             throw $this->createNotFoundException("Cette catégorie n'existe pas !");
         }

         $form = $this->createForm(TaskCategoryType::class, $taskCat);
         //$form->setData($equipment);

         $form->handleRequest($request);

         if ($form->isSubmitted() && $form->isValid()) {


            $toSlug = $form->getData()->getName();

            $taskCat->setSlug(strtolower($slugger->slug($toSlug)));

            $em->flush();

            $this->addFlash('success', $messageGenerator->getHappyMessage());
            
            // On redirige vers la liste
            return $this->redirectToRoute('task_categories_list');
           
         }
        

        return $this->render('back/task/edit_task_category.html.twig', [
            'form' => $form->createView(),
            'item' => $taskCat,
            'cat' => 'Modifier la catégorie de tâche',
            'btnText' => 'Modifier',
            'btnLabel' => 'bg-double_dragon_skin',
            'ico' => 'edit'
        
        ]);
    }

    /**
     * @Route("/{id}/ajouter/equipement", name="equipment_to_task_show")
     * @isGranted("ROLE_OWNER", message="Votre rôle ne permet pas de modifier cette tâche !")
     */
    public function equipmentToTaskShow($id, TaskRepository $taskRepository, EquipmentRepository $equipmentRepository, Request $request, MessageGenerator $messageGenerator)
    {
        
        $task = $taskRepository->findOneBy([
            'id' => $id
        ]);

        //VOTER TaskVoter
        $this->denyAccessUnlessGranted('TASK_EDIT',$task,'Vous ne pouvez pas modifier cette tâche');

       $emptyList = false;
       if (!$task) {
        $emptyList = true;
     }

       $equipmentList = $equipmentRepository->findAll();

       

        return $this->render('back/task/task_equipment_add.html.twig', [
            //'form' => $form->createView(),
          
            'equipmentList' => $equipmentList,
            'task' => $task,
            'cat' => 'Ajout de matériel à la tâche '.$task->getName() ,
            'btnText' => 'Ajouter',
            'btnLabel' => 'bg-aqua_velvet',
            'ico' => 'plus'
        
        ]);

    }
    //REMPLACÉ PAR AJAXCaller /ajaxCtl dans SecurityController
    /**
     * @Route("/{tid}/ajouter/equipement/{id}", name="task_equipment_add")
     * @isGranted("ROLE_OWNER", message="Votre rôle ne permet pas de modifier cette tâche !")
     */
    public function addEquipmentToTask($tid, $id, TaskRepository $taskRepository, EquipmentRepository $equipmentRepository, Request $request, EntityManagerInterface $em)
    {
        $task = $taskRepository->findOneBy([
            'id' => $tid
        ]);
        //VOTER TaskVoter
        $this->denyAccessUnlessGranted('TASK_EDIT',$task,'Vous ne pouvez pas modifier cette tâche');
       
       if (!$task) {
            //Gérer les erreurs de requêtes 
             throw $this->createNotFoundException("Cette tâche n'existe pas !");
        }
        else{   
           $equipment = $equipmentRepository->find($id);

           if ($equipment) {

                
               //AJOUT DU MATERIEL A LA TACHE
               $task->addEquipment($equipment);
               $task->setUpdatedBy($this->getUser());
               $task->getProject()->setUpdatedAt(new \DateTime());
               $task->getProject()->setUpdatedBy($this->getUser());
              
               $em->flush();

               
            
               // redirect to a route with parameters
                return $this->redirectToRoute('equipment_to_task_show', [
                   
                    'equipmentList' => $equipmentRepository->findAll(),
                    'id' => $task->getId(),
                    'cat' => 'Ajout de matériel à la tâche '.$task->getName() ,
                    'btnText' => 'Ajouter',
                    'btnLabel' => 'bg-aqua_velvet',
                    'ico' => 'plus'
                    ]);

           }
           else{
                //Gérer les erreurs de requêtes 
                throw $this->createNotFoundException("Problème de liaison avec la base de donnée !");
           }
        }
    }
    /**
     * @Route("/{tid}/retirer/equipement/{id}", name="task_equipment_remove")
     * @isGranted("ROLE_OWNER", message="Votre rôle ne permet pas de modifier cette tâche !")
     */
    public function RemoveEquipmentFromTask($tid, $id, TaskRepository $taskRepository, EquipmentRepository $equipmentRepository, Request $request, EntityManagerInterface $em)
    {
        $task = $taskRepository->findOneBy([
            'id' => $tid
        ]);
        //VOTER TaskVoter
        $this->denyAccessUnlessGranted('TASK_EDIT',$task,'Vous ne pouvez pas modifier cette tâche');
       
       if (!$task) {
            //Gérer les erreurs de requêtes 
             throw $this->createNotFoundException("Cette tâche n'existe pas !");
        }
        else{   
           $equipment = $equipmentRepository->find($id);

           if ($equipment) {
               //SUPPRESSION DU MATERIEL A LA TACHE
               $task->removeEquipment($equipment);
               $task->setUpdatedBy($this->getUser());
               $task->getProject()->setUpdatedAt(new \DateTime());
               $task->getProject()->setUpdatedBy($this->getUser());
             
               $em->flush();
            
               // redirect to a route with parameters
                return $this->redirectToRoute('equipment_to_task_show', [
                   
                    'equipmentList' => $equipmentRepository->findAll(),
                    'id' => $task->getId(),
                    'cat' => 'Ajout de matériel à la tâche '.$task->getName() ,
                    'btnText' => 'Ajouter',
                    'btnLabel' => 'bg-aqua_velvet',
                    'ico' => 'plus'
                    ]);

           }
           else{
                //Gérer les erreurs de requêtes 
                throw $this->createNotFoundException("Problème de liaison avec la base de donnée !");
           }
        }
    
    }

    /**
     * @Route("/{tid}/document/{id}/download", name="task_document_download")
    */
    public function downloadTaskDocument($tid,$id, Request $request, Document $document, FileManager $fileManager, DocumentRepository $documentRepository, TaskRepository $taskRepository)
    {

        $valid = $documentRepository->findOneBy(['id' => $id,'task' => $tid]);
        if ($valid) {
            $filePath = $fileManager->download($document);

            if ($filePath) {
             return $this->file($filePath);
            }   
        }
        else{
             
             throw $this->createNotFoundException("Erreur de chargement du fichier !");
        }
       $filePath = $fileManager->download($document);

       if ($filePath) {
        return $this->file($filePath);
       }   
    }
    
    /**
     * @Route("/{tid}/document/{id}/supprimer", name="task_document_remove", methods={"DELETE"})
     * @isGranted("ROLE_EDITOR", message="Vous n'avez pas accès à cette fonctionnalité !")
     */
    public function removeDocumentFromTask($tid, Request $request, Document $document, FileManager $fileManager, EntityManagerInterface $em)
    {
        if ($this->isCsrfTokenValid('delete'.$document->getId(), $request->request->get('_token'))) {
          
            //Service from FileManager
            $fileManager->delete($document);

        
            $em->remove($document);
            $em->flush();
        }

        return $this->redirectToRoute('task_show',[
            'id' => $tid
        ]);
        
       
    }
}