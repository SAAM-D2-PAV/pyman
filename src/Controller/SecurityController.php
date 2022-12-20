<?php

namespace App\Controller;


use DateTime;
use App\Entity\User;
use App\Form\UserType;
use App\Form\UserEditType;
use App\Event\TaskSuccessEvent;
use App\Form\ProjectSearchType;
use App\Repository\LogRepository;
use App\Service\MessageGenerator;
use App\Event\ProjectSuccessEvent;
use App\Repository\EquipmentRepository;
use App\Repository\LocationRepository;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Symfony\Component\Mime\Address;
use App\Repository\ProjectRepository;
use Psr\Container\ContainerInterface;
use App\Repository\LogEventRepository;
use App\Repository\TaskCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    
    /**
     * @Route("/connexion", name="app_login", priority=1)
     */
    public function login(AuthenticationUtils $authenticationUtils, Request $request): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('dashboard' );
        } 

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        
        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }
    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    // HOME DASHBOARD
    // voir src/EventSubscriber/BackNavContentSubscriber.php pour les éléments du menu
    /**
     * @Route("/tableau-de-bord", name="dashboard")
     * @isGranted("ROLE_VIEWER", message="Vous n'avez pas accès à cette section !")
     */
    public function dashboard(ProjectRepository $projectRepository, EquipmentRepository $equipmentRepository, LocationRepository $locationRepository, LogEventRepository $logEventRepository)
    {
        
        $projects = $projectRepository->getInProgressProjectOrderedByDateAsc();
        $equipments = $equipmentRepository->findAll();
        $locations = $locationRepository->findAll();

        $locationsCount = count($locations);
        $equipmentsCount = count($equipments);

        $logs = $logEventRepository->findLastFive();

        
        
        return $this->render("back/dashboard.html.twig",[
            'projects' => $projects,
            'equipmentsCount' => $equipmentsCount,
            'locationsCount' => $locationsCount,
            'logs' => $logs
           
        ]);
    }
    /**
     * @Route("/calendrier/{category}", name="calendar")
     * @isGranted("ROLE_VIEWER", message="Vous n'avez pas accès à cette section !")
     */
    public function deskPage($category = "",TaskRepository $taskRepository, TaskCategoryRepository $taskCategoryRepository)
    {
       

        if ($category) {

            $cat = $taskCategoryRepository->findOneBy(['slug' => $category]);
            if ($cat) {
              
                $events = $taskRepository->findBy(['category' => $cat]);
            }
            else{
                $events = $taskRepository->findAll();
            }
        }
        else{
            $events = $taskRepository->findAll();
        }
        $cats = $taskCategoryRepository->findAll();
        $evts = [];
        $color = "#341f97";
        $textColor = 'white';

        foreach ($events as  $event) {

            $taskCat = $event->getCategory()->getName();
            $status = $event->getStatus();
            $eventDesc = "";
            
            $color = $event->getCategory()->getColor();
            $textColor = $event->getCategory()->getTextColor();

            if ($status == "Annulée") {
                $color = "#ee5253";
                $eventDesc = "ANNULÉE";
            }

         
            $start = $event->getStartDate()->format('Y-m-d') .' '. $event->getStartHour()->format('H:i:s'); 
            $end = $event->getEndDate()->format('Y-m-d').' '.$event->getEndHour()->format('H:i:s');

            $allDay = false;

            if ($event->getStartDate()->format('Y-m-d') !== $event->getEndDate()->format('Y-m-d') ){
                $allDay = true;

                $end = date('Y-m-d H:i:s', strtotime($end. ' + 1 days'));

            }
            //$event->getCategory();
            $owners = [];
            
            foreach ($event->getOwners() as $owner) {
               $owners[] = $owner->getFirstname();
               
            }
            $comma_separated_owners = implode(", ", $owners);
        
            $evts[] = [
                'id' => $event->getId(),
                'start' => $start,
                'end' => $end,
				'allDay' => $allDay,
                'title' =>  $eventDesc.' '.$event->getName().' | '.$event->getProject()->getName().' | '.$comma_separated_owners,
                //'description' => $comma_separated_owners,
                'url' => '/taches/'.$event->getId().'/afficher',
                'backgroundColor' => $color,
                'textColor' => $textColor
            ];
        }

        $data = json_encode($evts);

       return $this->render("back/calendar.html.twig",[
        'data' =>  $data,
        'categories' => $cats
       ]);
    }

    //------------------------------------------------------------------------------
    // USER SECTION
    //------------------------------------------------------------------------------
    /**
     * @Route("/membres", name="users_list")
     * @isGranted("ROLE_ADMIN", message="Vous n'avez pas accès à cette section !")
     */
    public function usersList(UserRepository $userRepository)
    {
        $users = $userRepository->findAll();

        return $this->render('security/users_list.html.twig',[
            'users' => $users
        ]);
    }
    /**
     * @Route("/equipe/membre/{id}/afficher", name="user_show")
     * @isGranted("ROLE_ADMIN", message="Vous n'avez pas accès à cette section !")
     */
    public function userShow($id, UserRepository $userRepository, Request $request, MessageGenerator $messageGenerator, LogRepository $logRepository, EntityManagerInterface $em)
    {
        $user = $userRepository->findOneBy([
            'id' => $id
        ]);
        if (!$user) {
            throw $this->createNotFoundException("Ce profil n'existe pas !");
        }
        $createdAc = $logRepository->getUserlastLog();
        $form = $this->createForm(UserEditType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            
           
            $em->flush();

            $this->addFlash('success', $messageGenerator->getHappyMessage());
            
            

           
         }
        return $this->render('security/user_show.html.twig',[
            'user' => $user,
            'createdAc' => $createdAc,
            'form' => $form->createView(),
            'btnText' => 'Modifier',
            'btnLabel' => 'bg-double_dragon_skin',
            'ico' => 'edit'
        ]);
    }

    /**
     * @Route("/$NGwJKwq0/mDARLdUyDU5xOWu8N43qzGKNpaLu2I7t1FplrzdKB7Jm/register", name="user_register")
     */
    public function userRegister(Request $request, UserRepository $userRepository, UserPasswordEncoderInterface $passwordEncoder,MailerInterface $mailer, MessageGenerator $messageGenerator,EntityManagerInterface $em)
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('dashboard',[
                'id' => $this->getUser()->getId()
            ]);
        } 
        //nouvelle entité user
        $user = new User;
        $form = $this->createForm(UserType::class, $user);
        //Validation du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $emailTocheck = $form->getData()->getEmail();
            $emailIsExist = $userRepository->findOneBy(['email' => $emailTocheck ]);
            
            if ($emailIsExist) {
                $error = "Cet email existe déjà !";
                return $this->render('security/register.html.twig', [ 
                    'form' => $form->createView(),
                    'error' => $error,
                    'cat' => 'Créer un compte',
                    'btnText' => 'Créer',
                    'btnLabel' => 'bg-aqua_velvet',
                    'ico' => 'plus'

                ]);
            }
            $password = $form->getData()->getPassword();
            $encrypt = $passwordEncoder->encodePassword($user, $password);
            $user->setPassword($encrypt);
            $user->setRoles(['ROLE_NOTALLOW']);

            $em->persist($user);
            $em->flush($user);
            //https://symfony.com/doc/current/mailer.html
            $email = (new TemplatedEmail())
            ->from(new Address('audiovideo.ac@gdp-app.ovh', 'Pôle audiovisuel - PAV'))
            ->to($user->getEmail())
            ->subject('Bienvenue sur Pyman')
            ->htmlTemplate('mail/register_template.html.twig')
            ->context([
                'user_firstname' => $user->getFirstname()
            ])
            ;

            // $mailer->send($email);
            $this->addFlash('success', 'Vous pouvez à présent vous connecter');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/register.html.twig',[
            'error' => '',
            'form' => $form->createView(),
            'cat' => 'Créer un compte',
            'btnText' => 'Créer',
            'btnLabel' => 'bg-aqua_velvet',
            'ico' => 'plus'
        ]);
    }
    /**
     * @Route("/equipe/membre/{id}/supprimer", name="user_delete", methods={"DELETE"})
     *  @isGranted("ROLE_ADMIN", message="Vous n'avez pas accès à cette section !")
     */
    public function userDelete($id, Request $request, User $user, UserRepository $userRepository, ProjectRepository $projectRepository, TaskRepository $taskRepository, LogRepository $logRepository, EntityManagerInterface $em)
    {
        $userToDelete = $user;
        //VOTER TaskVoter
        $this->denyAccessUnlessGranted('USER_DELETE',$user,'Vous ne pouvez pas supprimer ce profil');

        $johnDoe = $userRepository->findOneBy([
            'email' => 'john.doe@nowhere.fr'
        ]);
        if (!$johnDoe) {
           $johnDoe =  $userRepository->createJohnDoe();
        }

        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
        
          
            
            $logRepository->deleteById($user->getId());

            $userProjects = $userToDelete->getProjects();
            foreach ($userProjects as $project){
                $project->setCreatedBy($johnDoe);
            }
            $userTasks = $userToDelete->getTasks();
            foreach ($userTasks as $task){
                $task->setCreatedBy($johnDoe);
            }
            $userTasksOwner = $userToDelete->getSubscribedTasks();
            foreach ($userTasksOwner as $task){
                $task->removeOwner($userToDelete);
            }
            $userDocs = $userToDelete->getDocuments();
            foreach ($userDocs as $doc){
                $doc->setUploadedBy($johnDoe);
            }
            $projectsUpdated = $projectRepository->findBy([
                'updatedBy' => $userToDelete
            ]);
            foreach ($projectsUpdated as $projectUpdated){
                $projectUpdated->setUpdatedBy($johnDoe);
            }
            $tasksUpdated = $taskRepository->findBy([
                'updatedBy' => $userToDelete
            ]);
            foreach ($tasksUpdated as $taskUpdated){
                $taskUpdated->setUpdatedBy($johnDoe);
            }

            $em->flush();
            if ($userToDelete == $this->getUser()) {
                $this->container->get('security.token_storage')->setToken(null);
                $em->remove($user);
                $em->flush();
                // Ceci ne fonctionne pas avec la création d'une nouvelle session !
                $this->addFlash('success', 'Votre compte a bien été supprimé !'); 
                return $this->redirectToRoute('app_logout');
            }
            
            $em->remove($user);
            $em->flush();
            
        }
        return $this->redirectToRoute('users_list');
    }

    /**
     * @Route("/mon-compte/{id}", name="user_account")
     * @isGranted("ROLE_VIEWER", message="Vous n'avez pas accès à cette section !")
     */
    public function userDashboard($id, UserRepository $userRepository, Request $request, MessageGenerator $messageGenerator, LogRepository $logRepository, EntityManagerInterface $em)
    {
        $user = $userRepository->findOneBy([
            'id' => $id
        ]);
        if (!$user) {
            return $this->redirectToRoute('dashboard');
        }
        if ($user == $this->getUser()) {

            $createdAc = $logRepository->getUserlastLog();
            $form = $this->createForm(UserEditType::class, $user);
            $form->handleRequest($request);
    
            if ($form->isSubmitted() && $form->isValid()) {
    
                
               
                $em->flush();
    
                $this->addFlash('success', $messageGenerator->getHappyMessage());
                
                
    
               
             }
            return $this->render('security/user_dashboard.html.twig',[
                'user' => $user,
                'createdAc' => $createdAc,
                'form' => $form->createView(),
                'btnText' => 'Modifier',
                'btnLabel' => 'bg-double_dragon_skin',
                'ico' => 'edit'
            ]);
        }
        
        return $this->redirectToRoute('dashboard');
       
    }

   
    //méthode appelée dans back.js
    /**
     * @Route("/ajaxCtl", name="ajaxCtl", methods={"GET"})
     * @isGranted("ROLE_VIEWER", message="Vous n'avez pas accès à cette section !")
     */
    public function ajaxCtl(Request $request, LogEventRepository $logEventRepository, TaskRepository $taskRepository, EntityManagerInterface $em, EventDispatcherInterface $dispatcher,EquipmentRepository $equipmentRepository)
    {

        $action = $request->query->get('action');
        $parameter = $request->query->get('parameter');



        if ($action === "log_event"){

            $logEvents = $logEventRepository->findLastFive();
            //On récupère le dernier élément de la requète
            $LastLogInArray = $logEvents[0];
            //On récupère le dernier élément stocké en session
            $lastLogInSession = $request->getSession()->get('lastLogId');
            //On compare les deux
            //Si oui alors pas de nouvelle entrée
            if($lastLogInSession){

                if ($LastLogInArray->getCreatedAt() == $lastLogInSession->getCreatedAt()){
                    $newLog = false;
                }
                //Sinon nouvelle entrée et stockage en session  de celle-ci
                else{
                    $request->getSession()->set('lastLogId',$LastLogInArray);
                    $newLog = true;
                }

            }
            else{
                $request->getSession()->set('lastLogId',$LastLogInArray);
                $newLog = true;
            }


            $result = [];
            $evts = [];

            foreach ($logEvents as  $logEvent) {

                $uri = $this->container->get('router')->getContext()->getBaseUrl();

                $logEventType = $logEvent->getType();
                //si le logEvent est un projet
                if ($logEvent->getProject() !== null){

                    $logEventId = $logEvent->getProject()->getId();
                    $logEventName = $logEvent->getProject()->getName();
                    $logEventSlug = $logEvent->getProject()->getSlug();
                    $uri = "/projets/".$logEventSlug."/".$logEventId;
                }
                //sinon une tache
                else{
                    $logEventId = $logEvent->getTask()->getId();
                    $logEventName = $logEvent->getTask()->getName();
                    $uri = "/taches/".$logEventId;
                }

                $logEventCreatedAt = $logEvent->getCreatedAt()->format('d-m-Y');
                $logEventCreatedBy = $logEvent->getCreatedBy()->getFirstname();

                $evts[] = [
                    'type' => $logEventType,
                    'logEventName' => $logEventName,
                    'logEventCreatedAt' => $logEventCreatedAt,
                    'logEventCreatedBy' => $logEventCreatedBy,
                    'url' => $uri."/afficher",

                ];
                $result = $evts;
            }
            $result[] = $newLog;
            $data = json_encode($result);
            return $this->json($data, 200,[],[]);

        }
        elseif ($action === "status_update") {
            # code...
            $result = $parameter;
            $data = json_decode($result);
            $task = $taskRepository->find($data);
            $project = $task->getProject();
            if(!$task){
                //Gérer les erreurs de requêtes 
                return $this->json(404);
            }
            else{

               switch ($task->getStatus()) {
                   case 'A faire':
                       # code...
                       $task->setStatus('En cours');

                       break;

                    case 'En cours':
                    # code...
                    $task->setStatus('Faite');

                        break;
                   

                   default:
                       # code...
                       break;
               }
               $project->setUpdatedAt(new \DateTime());
               $taskEvent = new TaskSuccessEvent($task);
               $dispatcher->dispatch($taskEvent,'task.updated');
               $projectEvent = new ProjectSuccessEvent($project);
               $dispatcher->dispatch($projectEvent,'project.updated');
               $em->flush();
               return $this->json($task->getStatus(), 200,[],[]);
            }
            
        }
        elseif ($action === 'addEq_ToTask'){


            $taskid = json_decode($parameter['tid']);
            $eid = json_decode($parameter['eid']);

            $task = $taskRepository->findOneBy([
                'id' => $taskid
            ]);
            //VOTER TaskVoter
            $this->denyAccessUnlessGranted('TASK_EDIT',$task,'Vous ne pouvez pas modifier cette tâche');

            if (!$task) {
                //Gérer les erreurs de requêtes
                throw $this->createNotFoundException("Cette tâche n'existe pas !");
            }
            else {
                $equipment = $equipmentRepository->find($eid);

                if ($equipment) {


                    //AJOUT DU MATERIEL A LA TACHE
                    $task->addEquipment($equipment);
                    $task->setUpdatedBy($this->getUser());
                    $task->getProject()->setUpdatedAt(new \DateTime());
                    $task->getProject()->setUpdatedBy($this->getUser());

                    $em->flush();

                    return $this->json($taskid, 200, [], []);

                }
                else{
                    //Gérer les erreurs de requêtes
                    throw $this->createNotFoundException("Problème de liaison avec la base de donnée !");
                }

            }
        }
        elseif ($action === 'RemEq_ToTask'){

            $taskid = json_decode($parameter['tid']);
            $eid = json_decode($parameter['eid']);

            $task = $taskRepository->findOneBy([
                'id' => $taskid
            ]);
            //VOTER TaskVoter
            $this->denyAccessUnlessGranted('TASK_EDIT',$task,'Vous ne pouvez pas modifier cette tâche');

            if (!$task) {
                //Gérer les erreurs de requêtes
                throw $this->createNotFoundException("Cette tâche n'existe pas !");
            }
            else {
                $equipment = $equipmentRepository->find($eid);

                if ($equipment) {


                    //SUPPRESSION DU MATERIEL A LA TACHE
                    $task->removeEquipment($equipment);
                    $task->setUpdatedBy($this->getUser());
                    $task->getProject()->setUpdatedAt(new \DateTime());
                    $task->getProject()->setUpdatedBy($this->getUser());

                    $em->flush();

                    return $this->json($taskid, 200, [], []);

                }
                else{
                    //Gérer les erreurs de requêtes
                    throw $this->createNotFoundException("Problème de liaison avec la base de donnée !");
                }

            }

        }
        else{
            return $this->json(404);
        }

    }

    //méthode d'affichage des logs
    /**
     * @Route("evenement/logs", name="logsEvent_show", methods={"GET", "POST"})
     * @isGranted("ROLE_ADMIN", message="Vous n'avez pas accès à cette section !")
     */
    public function logsEvent_show(Request $request, LogEventRepository $logEventRepository)
    {
        $logs = $logEventRepository->findLastFifty();
        return $this->render('security/log/log_show.html.twig',[
            "logs" => $logs
        ]);
    }
}
