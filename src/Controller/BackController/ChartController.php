<?php

namespace App\Controller\BackController;

use App\Entity\Project;
use App\Form\ChartType;
use App\Repository\ProjectRepository;
use App\Repository\TaskRepository;
use DateTime;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/stats")
 * @isGranted("ROLE_VIEWER", message="Connexion impossible !")
 */
class ChartController extends AbstractController
{

     //STATS
    /**
     * @Route("/show_old", name="stats_show_old")
     */
    public function show(Request $request, TaskRepository $taskRepository, ProjectRepository $projectRepository, PaginatorInterface $paginatorInterface)
    {

        $form = $this->createForm(ChartType::class);
        
        $date = new \DateTime();
        $year = $date->format('Y');
        $startDate = new \DateTime($year.'-01-01');
        $endDate = new \DateTime($year.'-12-31');
        $status = 'Faite';


        //Si formulaire validé alors nouvelle requete avec les données envoyées
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();

            $startDate = $data['startDate'];
            $endDate = $data['endDate'];
            
            $year = $startDate->format('Y');

        }
        $nbDoneTask = $taskRepository->findByTaskByStatusAndDate($status,$startDate, $endDate );
        
        // --> Nombre de tâches totales par mois + graph
      
        $taskperMonthArray = [];
        $taskperMonthArray[0] = 0; //janvier
        $taskperMonthArray[1] = 0; //février
        $taskperMonthArray[2] = 0; //mars
        $taskperMonthArray[3] = 0; //avril
        $taskperMonthArray[4] = 0; //mai
        $taskperMonthArray[5] = 0; //juin
        $taskperMonthArray[6] = 0; //juillet
        $taskperMonthArray[7] = 0; //août
        $taskperMonthArray[8] = 0; //septembre
        $taskperMonthArray[9] = 0; //octobre
        $taskperMonthArray[10] = 0; //novembre
        $taskperMonthArray[11] = 0; //décembre

       foreach ($nbDoneTask as $task){

            $month = $task->getStartDate()->format('m');

           if ($month == 1){
               $taskperMonthArray[0] ++;
           }if ($month == 2){
               $taskperMonthArray[1] ++;
           }if ($month == 3){
               $taskperMonthArray[2] ++;
           }if ($month == 4){
               $taskperMonthArray[3] ++;
           }if ($month == 5){
               $taskperMonthArray[4] ++;
           }if ($month == 6){
               $taskperMonthArray[5] ++;
           }if ($month == 7){
               $taskperMonthArray[6] ++;
           }if ($month == 8){
               $taskperMonthArray[7] ++;
           }if ($month == 9){
               $taskperMonthArray[8] ++;
           }if ($month == 10){
               $taskperMonthArray[9] ++;
           }if ($month == 11){
               $taskperMonthArray[10] ++;
           }
            if ($month == 12){
                $taskperMonthArray[11] ++;
            }
       }
       //Récupération de tous les projets terminés
       //Bug avec cette méthode !!!!
        //$nbDoneProject = $projectRepository->findProjectByStatusAndDate('Fait',$year.'-01-01', $year.'-12-31');
        $nbAllDoneProject = $projectRepository->findBy(
            ['status' => 'Fait']

        );
      
        $nbDoneProject = [];
        //Selection des projets correspondants aux dates
        foreach($nbAllDoneProject as $project){
            
        
            $dateProj = $project->getDeliveryDate();
            if ($dateProj >= $startDate and $dateProj <= $endDate) {
               
                $nbDoneProject[] = $project;
               
            }
           
        }
       
        // Lieux les plus utilisés sur l'année
       $locationArray = [];
        foreach ($nbDoneTask as $location) {
            array_push($locationArray,$location->getLocation()->getLocated().' / '.$location->getLocation()->getName());
        }
        $locations = array_count_values($locationArray);

        //Nombre de projets par année
        $projects = count($nbDoneProject);
        //Nombre de tâches par année
        $tasks = count($nbDoneTask);

        // --> Projet, direction, catégorie par année
        $projetsPaginated = $paginatorInterface->paginate($nbDoneProject, $request->query->getInt('page',1), 500);
        // --> Nombre de projets par direction graphique 1
        $array = [];
         foreach ($nbDoneProject as $proj){

            $dirName = $proj->getRequestBy()->getDepartment();
            array_push($array,$dirName);

         }
         //resultat :
        //  array:5 [▼
        //     0 => "SAAM"
        //     1 => "Delcom 7"
        //     2 => "DGESCO"
        //     3 => "Ministère des sports"
        //     4 => "SAAM"
        // ]
        //Compte les valeurs identiques
        $array2 = array_count_values($array);
        //  tableau type
        //  array:4 [▼
        //     "SAAM" => 2
        //     "Delcom 7" => 1
        //     "DGESCO" => 1
        //     "Ministère des sports" => 1
        //]
        $prjByDirArray = $array2;
        $camLabels2 = array_keys($array2);
        $data2 = [];
        foreach($array2 as $key => $val){
            array_push($data2,$val);
        }
        // --> Nombre de tâches par catégorie/projet/direction ... GROS TABLEAU
        $allEndedTasks = $taskRepository->getTasksForStats($startDate->format('Y-m-d'), $endDate->format('Y-m-d'));

        $array3 = [];
        foreach ($allEndedTasks as $task){

           array_push($array3,$task['taskCat']);

        }
        //resultat
            /*array:10 [▼
              0 => "Interview / témoignage / capsule vidéo"
              1 => "Captation multi-caméras"
              2 => "Captation multi-caméras"
              3 => "Captation multi-caméras"
              4 => "Captation multi-caméras"
              5 => "Installation"
              6 => "Post-production vidéo"
              7 => "Photo"
              8 => "Installation"
              9 => "Installation"
            ]*/
        //Compte les valeurs identiques
        $array4 = array_count_values($array3);

        //resultats
        /*array:5 [▼
          "Interview / témoignage / capsule vidéo" => 1
          "Captation multi-caméras" => 4
          "Installation" => 3
          "Post-production vidéo" => 1
          "Photo" => 1
        ]*/
        $taskByCatArray = $array4;
        $pieLabels3 = array_keys($array4);
        $data3 = [];
        foreach($array4 as $key => $val){
            array_push($data3,$val);
        }

        // --> Nombre de streaming 2021 ou période donnée

        // --> Direction en abscisse type de prestations en ordonnée

        // 3 encodage json
        $data = json_encode($taskperMonthArray);
        $label_2 = json_encode($camLabels2);
        $data_2 = json_encode($data2);

        $label_3 = json_encode($pieLabels3);
        $data_3 = json_encode($data3);

        return $this->render('back/chart/chart_show.html.twig',[
            'form' => $form->createView(),
            'btnText' => 'Filtrer',
            'btnLabel' => 'bg-aqua_velvet',
            'ico' => 'filter',
            'year' => $year,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'data' => $data,
            'data_2' => $data_2,
            'label_2' => $label_2,
            'data_3' => $data_3,
            'label_3' => $label_3,
            'prjByDirArray' =>$prjByDirArray,
            'locations' => $locations,
            'projectsCount' => $projects,
            'taskCat' => $pieLabels3,
            'tasksCount' => $tasks,
            'projects' => $projetsPaginated,
            'allEndedTasks' => $allEndedTasks
        ]);
    }




}