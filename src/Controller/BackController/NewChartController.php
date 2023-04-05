<?php

namespace App\Controller\BackController;

use App\Form\ChartType;
use App\Repository\ProjectRepository;
use App\Repository\TaskCategoryRepository;
use App\Repository\TaskRepository;
use DateInterval;
use Symfony\UX\Chartjs\Model\Chart;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
//CHART BUNDLES
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route('/stats')]
class NewChartController extends AbstractController
{
    #[Route('/show', name: 'stats_show')]
    public function index(Request $request, ChartBuilderInterface $chartBuilder, TaskRepository $taskRepository, ProjectRepository $projectRepository, TaskCategoryRepository $taskCategoryRepository): Response
    {
        $form = $this->createForm(ChartType::class);

        //Comportement par défaut affichage des données sur l'année en cours
        $date = new \DateTime();
        //Pour message d'information si année 2021 selectionnée voir template chart.html.twig
        $year = $date->format('Y');
    
        //stats du 1er janvier de l'année en cours ...
        $startDate = new \DateTime($year.'-01-01');
        //Pour affichage des stats en année-1
        $lastYearStartDate = new \DateTime($year.'-01-01');
        $lastYearStartDate->sub(new DateInterval('P1Y'));// soustraction d'une année à la date
        $lastYearStartDate->format('Y-m-d');

        //... à aujourd'hui 
        $endDate = new \DateTime();// création d'un objet DateTime pour la date à modifier
        $endDate->format('Y-m-d');
        //Pour affichage des stats en année-1
        $lastYearEndDate = new \DateTime();
        $lastYearEndDate->sub(new DateInterval('P1Y'));// soustraction d'une année à la date
        $lastYearEndDate->format('Y-m-d');

        $taskStatus = 'Faite';
        $projectStatus = 'Fait';
        //Si formulaire validé alors nouvelle requete avec les données envoyées
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // On récupère les données envoyées par le formulaire
            $data = $form->getData();
            // Date de début de stats
            $startDate = $data['startDate'];
            $lastYearStartDate = clone $startDate; // Créer une copie de la date pour éviter la modification
            $lastYearStartDate->sub(new DateInterval('P1Y')); // Soustraction d'une année à la date
            $year = $startDate->format('Y');
            // Date de fin de stats
            $endDate = $data['endDate'];
            $lastYearEndDate = clone $endDate; // Créer une copie de la date pour éviter la modification
            $lastYearEndDate->sub(new DateInterval('P1Y')); // Soustraction d'une année à la date
            
        }
    
        //ON récupère les taches terminées sur la période selectionnée (défaut ou via le formulaire)
        $doneTasksPerPeriod = $taskRepository->findByTaskByStatusAndDate($taskStatus,$startDate, $endDate);

        //ON récupère les taches terminées sur la période selectionnée - 1 année (défaut ou via le formulaire)
        $doneTasksPerPeriodMinusOne = $taskRepository->findByTaskByStatusAndDate($taskStatus,$lastYearStartDate, $lastYearEndDate);
        
         //ON récupère les projets terminés sur la période selectionnée (défaut ou via le formulaire)
        $doneProjectsPerPeriod = $projectRepository->findProjectByStatusAndDate($projectStatus,$startDate, $endDate);
  

        $allDoneTasks = [];
        $allDoneTasksMinusOne = [];
        $allDoneTasksCounter = 0;
        $allDoneTasksMinusOneCounter =0;
        $allTasksPerCategory = [];
        $allDoneProjectsCounter = 0;
        $allDoneProjectsPerDirection = [];
        $allLocations = [];
        $allStreaming = [];

        //Infos projets réalisés
        foreach ($doneProjectsPerPeriod  as $project) {
            //compteur de projets
            $allDoneProjectsCounter ++;
            $dirName = $project->getRequestBy()->getDepartment();
            $allDoneProjectsPerDirection[] = $dirName;
        }

        //Infos tâches réalisés
        foreach ($doneTasksPerPeriod as $task){
            //******************/
            //PREMIER GRAPH TACHES PAR MOIS (TTES TACHES CONFONDUES)
            //Variables de mémorisation de la date au format 2022 01
            $date = $task->getEndDate()->format('Y m');
            //Insertion de cette date dans le tableau
            $allDoneTasks[] = $date; 
            //Compteur de tâches
            $allDoneTasksCounter ++;
            /*****************/
           //Tableau des lieux de prestations
            $location = $task->getLocation()->getMinistry().' ('.$task->getLocation()->getLocated().')';
            $allLocations[] = $location;//Voir graph sur les lieux de prestations
            /*****************/
            //Tableau des catégories
            $category = $task->getCategory()->getName();//Voir graph sur les taches pour chaque catégorie
            $allTasksPerCategory[] = $category;

        }
        //Infos tâches réalisés moins 1 année
        foreach ($doneTasksPerPeriodMinusOne as $task){
            //******************/
            //PREMIER GRAPH TACHES PAR MOIS (TTES TACHES CONFONDUES)
            //Variables de mémorisation de la date au format 2022 01
            $date = $task->getEndDate()->format('Y m');
            //Insertion de cette date dans le tableau
            $allDoneTasksMinusOne[] = $date; 
            //Compteur de tâches
            $allDoneTasksMinusOneCounter ++;    
            //On récupère tous les streaming réalisés
            $task->getStream() == 1 ? $allStreaming[] = $task : "";   
        }
        //******************/
        //PREMIER GRAPH TACHES PAR MOIS (TTES TACHES CONFONDUES)
        // 1 .
        //Récupération des catégories de tâches pour affichage 
        $taskCategories = $taskCategoryRepository->findAll();
        //On trie les valeurs du tableau (dates) par ordre croissant
        
        sort($allDoneTasks);
        /*array:738 [▼
            0 => "2021 06"
            1 => "2021 06"
            2 => "2021 07"
        */
        sort($allDoneTasksMinusOne);

        // 2 .
        //étape de formatage d'un nouveau tableau
        //Ici on compte les valeurs identiques du tableau $monthLabels 
        $taskPerMonth =  array_count_values($allDoneTasks);
        /*"2022 01" => 32
        "2022 02" => 38
        "2022 03" => 87
        "2022 04" => 42
        "2022 05" => 28
        "2022 06" => 62
        "2022 07" => 45
        "2022 08" => 17
        "2022 09" => 59
        "2022 10" => 77
        "2022 11" => 77
        "2022 12" => 52
        "2023 02" => 1*/
       
        //Ici on scinde le tableau en deux sous tableaux de données (labels et data) pour alimenter le chart builder
        $tasksPerMonthLabels = array_keys($taskPerMonth);
        $counter = array_values($taskPerMonth);
      
       //Identique pour année -1
        $taskPerMonthMinusOne = array_count_values($allDoneTasksMinusOne);

        $tasksPerMonthMinusOneLabels = array_keys($taskPerMonthMinusOne);
        $counterMinusOne = array_values($taskPerMonthMinusOne);
       
        //Enfin
        // On appelle le chart builder et on l'instancie avec les données ci dessus
        $chart01 = $chartBuilder->createChart(Chart::TYPE_LINE);
        $chart01->setData([
            'labels' => $tasksPerMonthLabels,
            'datasets' => [
                [
                    'label' => 'Tâches par mois pour '.$year,
                    'borderColor' => 'rgba(54, 162, 235)',
                    'backgroundColor' => 'rgba(54, 162, 235)',
                    'borderWidth' => 2,
                    'data' => $counter,
                ],
            ]
        ]);
        $chart01B = $chartBuilder->createChart(Chart::TYPE_LINE);
        $chart01B->setData([
            'labels' => $tasksPerMonthMinusOneLabels,
            'datasets' => [
                [
                    'label' => 'Tâches par mois pour '.$year-1,
                    'borderColor' => 'rgba(255, 141, 126)',
                    'backgroundColor' => 'rgba(255, 141, 126)',
                    'borderWidth' => 2,
                    'data' => $counterMinusOne,
                ],
            ]
        ]);
         // FIN DU PREMIER GRAPH TACHES PAR MOIS (TTES TACHES CONFONDUES)
         //******************/
        //******************/
        //DEUXIEME GRAPH PROJETS PAR DIRECTIONS
        //$allDoneProjectsPerDirection
        /*
        0 => "Service de l'action administrative et des moyens (SAAM)"
        1 => "Délégation à la communication (DELCOM)"
        2 => "direction générale de l'enseignement scolaire - DGESCO"
        3 => "Ministère des Sports et des Jeux Olympiqueset Paralympiques"
        4 => "Délégation à la communication (DELCOM)"
        5 => "Délégation à la communication (DELCOM)"
        6 => "Direction Générale de l’Enseignement Supérieur et de l’Insertion Professionnelle (DGESIP)"
        7 => "Direction générale de l'enseignement scolaire (DGESCO)"
        8 => "Direction générale de l’enseignement scolaire (DGESCO)"
        */
        $projectsPerDirection = array_count_values($allDoneProjectsPerDirection);
        /*
        "Service de l'action administrative et des moyens (SAAM)" => 1
        "Délégation à la communication (DELCOM)" => 3
        "Direction générale de l'enseignement scolaire (DGESCO)" => 3
        "Ministère des Sports et des Jeux Olympiqueset Paralympiques" => 1
        "Direction générale de l’enseignement Supérieur et de l’Insertion Professionnelle (DGESIP)" => 1
        */
        //Ici on scinde le tableau en deux sous tableaux de données (labels et data) pour alimenter le chart builder
        $ProjectPerDirectionLabels = array_keys($projectsPerDirection);
        $projectPerDirectionData = array_values($projectsPerDirection );

         // On appelle le chart builder et on l'instancie avec les données ci dessus
         $chart02 = $chartBuilder->createChart(Chart::TYPE_BAR);

         $chart02->setData([
             'labels' =>  $ProjectPerDirectionLabels,
             'datasets' => [
                 [
                     'label' => 'Projets par direction',
                     'backgroundColor'=> [
                        'rgba(255, 99, 132)',
                        'rgba(54, 162, 235)',
                        'rgba(255, 206, 86)',
                        'rgba(75, 192, 192)',
                        'rgba(153, 102, 255)',
                        'rgba(255, 159, 64)',
                        'rgba(125, 78, 91)',
                        'rgba(255, 111, 76)',
                        'rgba(0, 172, 140)',
                        'rgba(72, 77, 122)',
                        'rgba(253, 207, 65)',
                        'rgba(162, 104, 89)',
                        'rgba(70, 105, 100)',
                        'rgba(87, 102, 190)',
                        'rgba(149, 139, 98)',
                        'rgba(255, 141, 126)',
                        'rgba(208, 138, 119)',
                        'rgba(162, 104, 89)',
                        'rgba(255, 139, 99)',
                        'rgba(145, 174, 79)',
                     ],
                     'borderWidth'=> 1,
                     'data' => $projectPerDirectionData,
                    ],  
             ],
         ]);
         $chart02->setOptions([
            
            'plugins' => [
                'legend' => [
                    'position' => 'top'
                ]
            ]
        ]);

         // FIN DU DEUXIEME GRAPH PROJETS PAR DIRECTIONS
         //******************/
         //TROISIEME GRAPH LIEUX DE PRESTATIONS

         $tasksPerLocation = array_count_values($allLocations);
         /*array:14 [▼
            "MESR (DESCARTES)" => 23
            "MSJOP (...)" => 10
            "MENJ (99 GRENELLE)" => 56
            "MENJ (REGNAULT)" => 49
            "MSJOP (AVENUE DE FRANCE)" => 1
            " (MEN - GRENELLE)" => 56
            "MENJ (110 GRENELLE)" => 317
            " (MESRI - DESCARTES)" => 2
            " (AUTRE)" => 16
            "MENJ (DUTOT)" => 13
            "MENJ (ÉCOLE / COLLÈGE / LYCÉE)" => 29
            "LIEU EXTERNE (AUTRE)" => 10
            "MENJ (107 GRENELLE)" => 33
            "LIEU EXTERNE (ÉCOLE / COLLÈGE / LYCÉE)" => 5
            ]*/
         //Ici on scinde le tableau en deux sous tableaux de données (labels et data) pour alimenter le chart builder
        $tasksPerLocationLabels = array_keys($tasksPerLocation);
        $tasksPerLocationData = array_values($tasksPerLocation);
        // On appelle le chart builder et on l'instancie avec les données ci dessus
        $chart03 = $chartBuilder->createChart(Chart::TYPE_BAR);
        $chart03->setData([
            'labels' =>  $tasksPerLocationLabels,
            'datasets' => [
                [
                    'label' => 'Nombre de prestations par lieu',
                    'backgroundColor'=> [
                       'rgba(255, 99, 132)',
                       'rgba(54, 162, 235)',
                       'rgba(255, 206, 86)',
                       'rgba(75, 192, 192)',
                       'rgba(153, 102, 255)',
                       'rgba(255, 159, 64)',
                       'rgba(125, 78, 91)',
                       'rgba(255, 111, 76)',
                       'rgba(0, 172, 140)',
                       'rgba(72, 77, 122)',
                       'rgba(253, 207, 65)',
                       'rgba(162, 104, 89)',
                       'rgba(70, 105, 100)',
                       'rgba(87, 102, 190)',
                       'rgba(149, 139, 98)',
                       'rgba(255, 141, 126)',
                       'rgba(208, 138, 119)',
                       'rgba(162, 104, 89)',
                       'rgba(255, 139, 99)',
                       'rgba(145, 174, 79)',
                    ],
                    'borderWidth'=> 1,
                    'data' => $tasksPerLocationData,
                ],
            ],
        ]);
        $chart03->setOptions([
            
            'plugins' => [
                'legend' => [
                    'position' => 'top'
                ]
            ]
        ]);
        // FIN DU TROISIEME GRAPH LIEUX DE PRESTATIONS
         //******************/
         //QUATRIEME GRAPH TACHES PAR CATEGORIE    
         $tasksPerCategory = array_count_values($allTasksPerCategory);
        /*
        "Reportage / documentaire" => 6
        "Administrative" => 12
        "Post-production vidéo" => 72
        "Captation live (mono ou multi-caméras)" => 49
        "Installation" => 50
        "Repérage technique" => 14
        "Interview / témoignage / capsule vidéo" => 50
        "Sonorisation" => 288
        "Photo" => 13
        "Sonorisation et enregistrement audio" => 61
        "Intervention technique" => 1
        */
         $tasksPerCategoryLabels = array_keys($tasksPerCategory);
         $tasksPerCategoryData = array_values($tasksPerCategory);
         // On appelle le chart builder et on l'instancie avec les données ci dessus
        $chart04 = $chartBuilder->createChart(Chart::TYPE_DOUGHNUT);
        $chart04->setData([
            'labels' =>  $tasksPerCategoryLabels,
            'datasets' => [
                [
                    'label' => 'Nombre de prestations pour chaque catégorie',
                    'backgroundColor'=> [
                       'rgba(255, 99, 132)',
                       'rgba(54, 162, 235)',
                       'rgba(255, 206, 86)',
                       'rgba(75, 192, 192)',
                       'rgba(153, 102, 255)',
                       'rgba(255, 159, 64)',
                       'rgba(125, 78, 91)',
                       'rgba(255, 111, 76)',
                       'rgba(0, 172, 140)',
                       'rgba(72, 77, 122)',
                       'rgba(253, 207, 65)',
                       'rgba(162, 104, 89)',
                       'rgba(70, 105, 100)',
                       'rgba(87, 102, 190)',
                       'rgba(149, 139, 98)',
                       'rgba(255, 141, 126)',
                       'rgba(208, 138, 119)',
                       'rgba(162, 104, 89)',
                       'rgba(255, 139, 99)',
                       'rgba(145, 174, 79)',
                    ],
                    'borderWidth'=> 1,
                    'data' => $tasksPerCategoryData,
                ],
            ],
        ]);
        $chart04->setOptions([
            
            'plugins' => [
                'legend' => [
                    'position' => 'right',
                ]
            ]
        ]);

         // FIN DU QUATRIEME GRAPH TACHES PAR CATEGORIE  
         //******************/
        //CINQUIEME GRAPH STREAMING
        sort($allStreaming);

        foreach ($allStreaming as $stream) {
            $date = $stream->getStartDate()->format('Y m');
            $allStreamingPerDate[] = $date; 
        }

        $allStreamingPerMonth = array_count_values($allStreamingPerDate);

        $chart05 = $chartBuilder->createChart(Chart::TYPE_LINE);
        $chart05->setData([
            'labels' => array_keys($allStreamingPerMonth),
            'datasets' => [
                [
                    'label' => 'Streaming pour '.$year,
                    'borderColor' => 'rgba(255, 0, 15)',
                    'backgroundColor' => 'rgba(255, 0, 15, .5)',
                    'borderWidth' => 2,
                    'pointStyle' => 'circle',
                    'pointRadius' => 10,
                    'pointHoverRadius' => 15,
                    'data' => array_values($allStreamingPerMonth),
                ],
            ]
        ]);

         //******************/
        return $this->render('back/new_chart/chart.html.twig', [
            'btnText' => 'Filtrer',
            'btnLabel' => 'bg-aqua_velvet',
            'ico' => 'filter',
            'year' => $year,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'form' => $form->createView(),
            'chart01' => $chart01,
            'chart01B' => $chart01B,
            'chart02' => $chart02,
            'chart03' => $chart03,
            'chart04' => $chart04,
            'chart05' => $chart05,
            'projectsCount' => $allDoneProjectsCounter,
            'tasksCount' =>  $allDoneTasksCounter,
            'tasksCountMinusOne' =>  $allDoneTasksMinusOneCounter,
            'projects' => $doneProjectsPerPeriod,
            'taskCat' => $taskCategories,
            'tasks' => $doneTasksPerPeriod,
            'allStreaming' => $allStreaming
        ]);
    }
}
