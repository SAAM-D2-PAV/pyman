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

/*  La méthode login() est une action contrôleur qui est définie avec une annotation @Route qui indique que cette action répond à l'URL "/connexion" et a pour nom de route "app_login". La priorité de la route est définie à 1, ce qui signifie que si plusieurs routes répondent à une même URL, celle-ci aura la priorité. 

Cette méthode prend deux paramètres : $authenticationUtils et $request. $authenticationUtils est une instance de la classe AuthenticationUtils qui est fournie par le composant Security de Symfony et qui permet de récupérer les erreurs d'authentification et le dernier nom d'utilisateur saisi. $request est une instance de la classe Request qui contient les données de la requête HTTP qui a déclenché cette action.

La méthode commence par vérifier si l'utilisateur est déjà connecté en appelant $this->getUser(), qui retourne l'utilisateur connecté ou null s'il n'y en a pas. Si un utilisateur est connecté, l'action redirige l'utilisateur vers la page dashboard avec return $this->redirectToRoute('dashboard');.

Ensuite, la méthode utilise l'instance $authenticationUtils pour récupérer l'erreur d'authentification avec $error = $authenticationUtils->getLastAuthenticationError(); et le dernier nom d'utilisateur saisi avec $lastUsername = $authenticationUtils->getLastUsername();.

Enfin, la méthode rend la vue Twig "security/login.html.twig" avec les variables $last_username et $error passées au tableau de variables. Ces variables sont utilisées pour afficher le dernier nom d'utilisateur saisi et l'erreur d'authentification éventuelle dans la vue Twig.
*/

    /**
     * @Route("/connexion", name="app_login", priority=1)
     */
    public function login(AuthenticationUtils $authenticationUtils, Request $request): Response
    {
        // Si l'utilisateur est déjà connecté, il est redirigé vers la page dashboard
        if ($this->getUser()) {
            return $this->redirectToRoute('dashboard');
        }

        // récupération de l'erreur de connexion s'il y en a une
        $error = $authenticationUtils->getLastAuthenticationError();
        // récupération du dernier nom d'utilisateur saisi
        $lastUsername = $authenticationUtils->getLastUsername();

        // rendu de la vue Twig de la page de connexion avec le dernier nom d'utilisateur saisi et l'erreur s'il y en a une
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



        return $this->render("back/dashboard.html.twig", [
            'projects' => $projects,
            'equipmentsCount' => $equipmentsCount,
            'locationsCount' => $locationsCount,
            'logs' => $logs

        ]);
    }
    /**
     * @Route("/calendrier", name="calendar")
     * @isGranted("ROLE_VIEWER", message="Vous n'avez pas accès à cette section !")
     */
    public function deskPage(Request $request, TaskRepository $taskRepository, TaskCategoryRepository $taskCategoryRepository, LocationRepository $locationRepository)
    {
        //Cette méthode nous sert à l'affichage de la vue avec les eventuelles checkbox les données sont envoyées via le ajaxCtl 
        //ajaxCtl() 
        //Pour affichage par catégories 
        $tasksCats = $taskCategoryRepository->findAll();
        //Pour affichage par ministère
        $ministriesCats = $locationRepository->getMinistries();

        //Tabelau d'entrées du calendrier = vide
        $evts = [];
        //Tabelau pour envoi à twig => input checked
        $checked = [];
        $color = "#341f97";
        $textColor = 'white';

        //Récupération des catégories si envoyées par la request
        $categories = $request->query->get('categories');
        if ($categories) {
            //Pour checked les input (catégories) sélectionnées template twig
            foreach ($categories as $category) {
                array_push($checked, $category);
            }
        }
        //Récupération des ministères si envoyées par la request
        $ministry = $request->query->get('ministry');
        if ($ministry) {
            //Pour checked les input (catégories) sélectionnées template twig
            array_push($checked, $ministry);
        }

    
        return $this->render("back/calendar.html.twig", [
            'ministries' => $ministriesCats,
            'checked' => $checked,
            'categories' => $tasksCats
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

        return $this->render('security/users_list.html.twig', [
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
        return $this->render('security/user_show.html.twig', [
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
    public function userRegister(Request $request, UserRepository $userRepository, UserPasswordEncoderInterface $passwordEncoder, MailerInterface $mailer, MessageGenerator $messageGenerator, EntityManagerInterface $em)
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('dashboard', [
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
            $emailIsExist = $userRepository->findOneBy(['email' => $emailTocheck]);

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
                ]);

            // $mailer->send($email);
            $this->addFlash('success', 'Vous pouvez à présent vous connecter');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/register.html.twig', [
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
        $this->denyAccessUnlessGranted('USER_DELETE', $user, 'Vous ne pouvez pas supprimer ce profil');

        $johnDoe = $userRepository->findOneBy([
            'email' => 'john.doe@nowhere.fr'
        ]);
        if (!$johnDoe) {
            $johnDoe =  $userRepository->createJohnDoe();
        }

        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {



            $logRepository->deleteById($user->getId());

            $userProjects = $userToDelete->getProjects();
            foreach ($userProjects as $project) {
                $project->setCreatedBy($johnDoe);
            }
            $userTasks = $userToDelete->getTasks();
            foreach ($userTasks as $task) {
                $task->setCreatedBy($johnDoe);
            }
            $userTasksOwner = $userToDelete->getSubscribedTasks();
            foreach ($userTasksOwner as $task) {
                $task->removeOwner($userToDelete);
            }
            $userDocs = $userToDelete->getDocuments();
            foreach ($userDocs as $doc) {
                $doc->setUploadedBy($johnDoe);
            }
            $projectsUpdated = $projectRepository->findBy([
                'updatedBy' => $userToDelete
            ]);
            foreach ($projectsUpdated as $projectUpdated) {
                $projectUpdated->setUpdatedBy($johnDoe);
            }
            $tasksUpdated = $taskRepository->findBy([
                'updatedBy' => $userToDelete
            ]);
            foreach ($tasksUpdated as $taskUpdated) {
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
            return $this->render('security/user_dashboard.html.twig', [
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
    public function ajaxCtl(Request $request, LogEventRepository $logEventRepository, TaskRepository $taskRepository, EntityManagerInterface $em, EventDispatcherInterface $dispatcher, EquipmentRepository $equipmentRepository, TaskCategoryRepository $taskCategoryRepository, LocationRepository $locationRepository)
    {

        $action = $request->query->get('action');
        $parameter = $request->query->get('parameter');
        
        
        if ($action === "log_event") {

            $logEvents = $logEventRepository->findLastFive();
            //On récupère le dernier élément de la requète
            $LastLogInArray = $logEvents[0];
            //On récupère le dernier élément stocké en session
            $lastLogInSession = $request->getSession()->get('lastLogId');
            //On compare les deux
            //Si oui alors pas de nouvelle entrée
            if ($lastLogInSession) {

                if ($LastLogInArray->getCreatedAt() == $lastLogInSession->getCreatedAt()) {
                    $newLog = false;
                }
                //Sinon nouvelle entrée et stockage en session  de celle-ci
                else {
                    $request->getSession()->set('lastLogId', $LastLogInArray);
                    $newLog = true;
                }
            } else {
                $request->getSession()->set('lastLogId', $LastLogInArray);
                $newLog = true;
            }


            $result = [];
            $evts = [];

            foreach ($logEvents as  $logEvent) {

                $uri = $this->container->get('router')->getContext()->getBaseUrl();

                $logEventType = $logEvent->getType();
                //si le logEvent est un projet
                if ($logEvent->getProject() !== null) {

                    $logEventId = $logEvent->getProject()->getId();
                    $logEventName = $logEvent->getProject()->getName();
                    $logEventSlug = $logEvent->getProject()->getSlug();
                    $uri = "/projets/" . $logEventSlug . "/" . $logEventId;
                }
                //sinon une tache
                else {
                    $logEventId = $logEvent->getTask()->getId();
                    $logEventName = $logEvent->getTask()->getName();
                    $uri = "/taches/" . $logEventId;
                }

                $logEventCreatedAt = $logEvent->getCreatedAt()->format('d-m-Y');
                $logEventCreatedBy = $logEvent->getCreatedBy()->getFirstname();

                $evts[] = [
                    'type' => $logEventType,
                    'logEventName' => $logEventName,
                    'logEventCreatedAt' => $logEventCreatedAt,
                    'logEventCreatedBy' => $logEventCreatedBy,
                    'url' => $uri . "/afficher",

                ];
                $result = $evts;
            }
            $result[] = $newLog;
            $data = json_encode($result);
            return $this->json($data, 200, [], []);
        } elseif ($action === "status_update") {
            # code...
            $result = $parameter;
            $data = json_decode($result);
            $task = $taskRepository->find($data);
            $project = $task->getProject();
            if (!$task) {
                //Gérer les erreurs de requêtes 
                return $this->json(404);
            } else {

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
                $dispatcher->dispatch($taskEvent, 'task.updated');
                $projectEvent = new ProjectSuccessEvent($project);
                $dispatcher->dispatch($projectEvent, 'project.updated');
                $em->flush();
                return $this->json($task->getStatus(), 200, [], []);
            }
        } elseif ($action === 'addEq_ToTask') {


            $taskid = json_decode($parameter['tid']);
            $eid = json_decode($parameter['eid']);

            $task = $taskRepository->findOneBy([
                'id' => $taskid
            ]);
            //VOTER TaskVoter
            $this->denyAccessUnlessGranted('TASK_EDIT', $task, 'Vous ne pouvez pas modifier cette tâche');

            if (!$task) {
                //Gérer les erreurs de requêtes
                throw $this->createNotFoundException("Cette tâche n'existe pas !");
            } else {
                $equipment = $equipmentRepository->find($eid);

                if ($equipment) {


                    //AJOUT DU MATERIEL A LA TACHE
                    $task->addEquipment($equipment);
                    $task->setUpdatedBy($this->getUser());
                    $task->getProject()->setUpdatedAt(new \DateTime());
                    $task->getProject()->setUpdatedBy($this->getUser());

                    $em->flush();

                    return $this->json($taskid, 200, [], []);
                } else {
                    //Gérer les erreurs de requêtes
                    throw $this->createNotFoundException("Problème de liaison avec la base de donnée !");
                }
            }
        } elseif ($action === 'RemEq_ToTask') {

            $taskid = json_decode($parameter['tid']);
            $eid = json_decode($parameter['eid']);

            $task = $taskRepository->findOneBy([
                'id' => $taskid
            ]);
            //VOTER TaskVoter
            $this->denyAccessUnlessGranted('TASK_EDIT', $task, 'Vous ne pouvez pas modifier cette tâche');

            if (!$task) {
                //Gérer les erreurs de requêtes
                throw $this->createNotFoundException("Cette tâche n'existe pas !");
            } else {
                $equipment = $equipmentRepository->find($eid);

                if ($equipment) {


                    //SUPPRESSION DU MATERIEL A LA TACHE
                    $task->removeEquipment($equipment);
                    $task->setUpdatedBy($this->getUser());
                    $task->getProject()->setUpdatedAt(new \DateTime());
                    $task->getProject()->setUpdatedBy($this->getUser());

                    $em->flush();

                    return $this->json($taskid, 200, [], []);
                } else {
                    //Gérer les erreurs de requêtes
                    throw $this->createNotFoundException("Problème de liaison avec la base de donnée !");
                }
            }
        } elseif ($action === 'calendarUpdater') {
            /*
                Récupérer certaines données de la base de données, en particulier :

                Toutes les catégories de tâches.
                Tous les ministères.

                Créer un tableau vide appelé $evts pour stocker les événements qui seront affichés sur le calendrier.

                Vérifier s'il y a des paramètres transmis par la requête HTTP (la variable $parameter n'est pas vide).

                S'il y a des paramètres, les décoder du format JSON et les assigner aux variables $categories et $ministry.

                Si les deux variables $categories et $ministry sont définies, cela signifie que l'utilisateur souhaite voir les événements filtrés par catégorie et ministère. Le code récupère toutes les tâches qui correspondent aux catégories et au ministère sélectionnés et crée un événement pour chacune d'elles, les ajoutant au tableau $evts.

                Si seule la variable $categories est définie, cela signifie que l'utilisateur souhaite voir les événements filtrés par catégorie uniquement. Le code récupère toutes les tâches qui correspondent aux catégories sélectionnées et crée un événement pour chacune d'elles, les ajoutant au tableau $evts.

                Pour chaque événement, le code récupère certaines informations de la base de données et crée un tableau avec les clés suivantes :

                id : l'identifiant de l'événement.
                start : la date et l'heure de début de l'événement.
                end : la date et l'heure de fin de l'événement.
                allDay : une valeur booléenne indiquant si l'événement dure toute la journée ou non.
                title : le titre de l'événement, comprenant le nom de la tâche, le nom du projet et les noms des propriétaires.
                url : l'URL où l'utilisateur peut voir plus de détails sur l'événement.
                backgroundColor : la couleur de fond de l'événement, en fonction de sa catégorie.
                textColor : la couleur du texte de l'événement, en fonction de sa catégorie.   
            */
            //$start = $request->query->get('start');
            //$end = $request->query->get('end');
            
            //Pour affichage par catégories 
            $tasksCats = $taskCategoryRepository->findAll();
            //Pour affichage par ministère
            $ministriesCats = $locationRepository->getMinistries();

            //Tabelau d'entrées du calendrier = vide
            $evts = [];
           
            $color = "#341f97";
            $textColor = 'white';
            

           if($parameter){
                //On décode les données get de l'url
                $parameters = json_decode($parameter);
                //Initialisation du tableau de catégories et de la variable ministry
                $categories = [];
                $ministry = null;

                
                //On vérifie si des catégories ont été passées en paramètres 
                if (array_key_exists(0, $parameters)) {
                    //Récupération des catégories si envoyées par la request
                    $categories =  $parameters[0];
                    
                }
                //On vérifie si un ministère a été passé en paramètres 
                if (array_key_exists(1, $parameters)) {
                    //Récupération des ministères si envoyé par la request
                    $ministry =  $parameters[1];
                }
                
                //Si parameter et catégories && ministères
                if ($categories and $ministry) {
                    //Tableau vide
                    $tasksByCatAndMinistry = [];
                    //On récupère toutes les taches par catégories et ministères
                
                    foreach ($categories as $category) {
                    
                        $tasksByCatAndMinistry[] = $taskRepository->getTasksByCatAndMinistry($ministry, $category);
                    }

                    //Et pour chaque tache trouvée on ajoute une entrée au calendrier $evts[]
                    foreach ($tasksByCatAndMinistry as $events) {

                        foreach ($events as $event) {
                            //statut de la tache
                            $status = $event->getStatus();
                            $eventDesc = "";

                            $color = $event->getCategory()->getColor();
                            $textColor = $event->getCategory()->getTextColor();

                            if ($status == "Annulée") {
                                $color = "#ee5253";
                                $eventDesc = "ANNULÉE";
                            }


                            $start = $event->getStartDate()->format('Y-m-d') . ' ' . $event->getStartHour()->format('H:i:s');
                            $end = $event->getEndDate()->format('Y-m-d') . ' ' . $event->getEndHour()->format('H:i:s');

                            $allDay = false;

                            if ($event->getStartDate()->format('Y-m-d') !== $event->getEndDate()->format('Y-m-d')) {
                                $allDay = true;

                                $end = date('Y-m-d H:i:s', strtotime($end . ' + 1 days'));
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
                                'title' =>  $eventDesc . ' ' . $event->getName() . ' | ' . $event->getProject()->getName() . ' | ' . $comma_separated_owners,
                                //'description' => $comma_separated_owners,
                                'url' => '/taches/' . $event->getId() . '/afficher',
                                'backgroundColor' => $color,
                                'textColor' => $textColor
                            ];
                        }
                    }
                }
                //Si parameter et catégories
                elseif ($categories) {
                    foreach ($categories as $category) {
                       

                        //Vérification de la catégorie
                        $cat = $taskCategoryRepository->findOneBy(['id' => $category]);
                        //Si elle existe on cherche toutes les tâches existantes de cette catégorie

                        if ($cat) {

                            $tasksByThisCategory = $taskRepository->findBy(['category' => $cat]);
                            //Et pour chaque tache trouvée on ajoute une entrée au calendrier $evts[]

                            foreach ($tasksByThisCategory as $event) {

                                //statut de la tache
                                $status = $event->getStatus();
                                $eventDesc = "";

                                $color = $event->getCategory()->getColor();
                                $textColor = $event->getCategory()->getTextColor();

                                if ($status == "Annulée") {
                                    $color = "#ee5253";
                                    $eventDesc = "ANNULÉE";
                                }


                                $start = $event->getStartDate()->format('Y-m-d') . ' ' . $event->getStartHour()->format('H:i:s');
                                $end = $event->getEndDate()->format('Y-m-d') . ' ' . $event->getEndHour()->format('H:i:s');

                                $allDay = false;

                                if ($event->getStartDate()->format('Y-m-d') !== $event->getEndDate()->format('Y-m-d')) {
                                    $allDay = true;

                                    $end = date('Y-m-d H:i:s', strtotime($end . ' + 1 days'));
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
                                    'title' =>  $eventDesc . ' ' . $event->getName() . ' | ' . $event->getProject()->getName() . ' | ' . $comma_separated_owners,
                                    //'description' => $comma_separated_owners,
                                    'url' => '/taches/' . $event->getId() . '/afficher',
                                    'backgroundColor' => $color,
                                    'textColor' => $textColor
                                ];
                            }
                        }
                    }
                }
                //Si parameter et ministères
                elseif ($ministry) {
                    //Vérification et récupération des lieux appartenant à ce ministère
                    $locations = $locationRepository->findBy(['ministry' => $ministry]);
                    //Vérification des tâches existantes pour ce ministère
                    foreach ($locations as $location) {

                        $tasksByLocations = $taskRepository->findBy(['location' => $location]);
                        //Et pour chaque tache trouvée on ajoute une entrée au calendrier $evts[]
                        foreach ($tasksByLocations as $event) {

                            //statut de la tache
                            $status = $event->getStatus();
                            $eventDesc = "";

                            $color = $event->getCategory()->getColor();
                            $textColor = $event->getCategory()->getTextColor();

                            if ($status == "Annulée") {
                                $color = "#ee5253";
                                $eventDesc = "ANNULÉE";
                            }


                            $start = $event->getStartDate()->format('Y-m-d') . ' ' . $event->getStartHour()->format('H:i:s');
                            $end = $event->getEndDate()->format('Y-m-d') . ' ' . $event->getEndHour()->format('H:i:s');

                            $allDay = false;

                            if ($event->getStartDate()->format('Y-m-d') !== $event->getEndDate()->format('Y-m-d')) {
                                $allDay = true;

                                $end = date('Y-m-d H:i:s', strtotime($end . ' + 1 days'));
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
                                'title' =>  $eventDesc . ' ' . $event->getName() . ' | ' . $event->getProject()->getName() . ' | ' . $comma_separated_owners,
                                //'description' => $comma_separated_owners,
                                'url' => '/taches/' . $event->getId() . '/afficher',
                                'backgroundColor' => $color,
                                'textColor' => $textColor
                            ];
                        }
                    }
                } 
                //Si parameter seulement
                else {
                    
                    $events = $taskRepository->findAll();
    
                    foreach ($events as $event) {
                        //statut de la tache
                        $status = $event->getStatus();
                        $eventDesc = "";
    
                        $color = $event->getCategory()->getColor();
                        $textColor = $event->getCategory()->getTextColor();
    
                        if ($status == "Annulée") {
                            $color = "#ee5253";
                            $eventDesc = "ANNULÉE";
                        }
    
    
                        $start = $event->getStartDate()->format('Y-m-d') . ' ' . $event->getStartHour()->format('H:i:s');
                        $end = $event->getEndDate()->format('Y-m-d') . ' ' . $event->getEndHour()->format('H:i:s');
    
                        $allDay = false;
    
                        if ($event->getStartDate()->format('Y-m-d') !== $event->getEndDate()->format('Y-m-d')) {
                            $allDay = true;
    
                            $end = date('Y-m-d H:i:s', strtotime($end . ' + 1 days'));
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
                            'title' =>  $eventDesc . ' ' . $event->getName() . ' | ' . $event->getProject()->getName() . ' | ' . $comma_separated_owners,
                            //'description' => $comma_separated_owners,
                            'url' => '/taches/' . $event->getId() . '/afficher',
                            'backgroundColor' => $color,
                            'textColor' => $textColor
                        ];
                    }
                }

           }
            else {
               
                $events = $taskRepository->findAll();

                foreach ($events as $event) {
                    //statut de la tache
                    $status = $event->getStatus();
                    $eventDesc = "";

                    $color = $event->getCategory()->getColor();
                    $textColor = $event->getCategory()->getTextColor();

                    if ($status == "Annulée") {
                        $color = "#ee5253";
                        $eventDesc = "ANNULÉE";
                    }


                    $start = $event->getStartDate()->format('Y-m-d') . ' ' . $event->getStartHour()->format('H:i:s');
                    $end = $event->getEndDate()->format('Y-m-d') . ' ' . $event->getEndHour()->format('H:i:s');

                    $allDay = false;

                    if ($event->getStartDate()->format('Y-m-d') !== $event->getEndDate()->format('Y-m-d')) {
                        $allDay = true;

                        $end = date('Y-m-d H:i:s', strtotime($end . ' + 1 days'));
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
                        'title' =>  $eventDesc . ' ' . $event->getName() . ' | ' . $event->getProject()->getName() . ' | ' . $comma_separated_owners,
                        //'description' => $comma_separated_owners,
                        'url' => '/taches/' . $event->getId() . '/afficher',
                        'backgroundColor' => $color,
                        'textColor' => $textColor
                    ];
                }
            }
            //Encodage du tableau au format json pour envoi vers twig
            //$data = json_encode($evts);
            return $this->json($evts, 200, [], []);
        } 
        else {
            return $this->json(500);
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
        return $this->render('security/log/log_show.html.twig', [
            "logs" => $logs
        ]);
    }
}
