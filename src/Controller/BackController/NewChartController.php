<?php

namespace App\Controller\BackController;

use App\Form\ChartType;
use App\Repository\ProjectRepository;
use App\Repository\TaskCategoryRepository;
use App\Repository\TaskRepository;
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
    #[Route('/2', name: 'app_new_chart')]
    public function index(Request $request, ChartBuilderInterface $chartBuilder, TaskRepository $taskRepository, ProjectRepository $projectRepository, TaskCategoryRepository $taskCategoryRepository): Response
    {
        $form = $this->createForm(ChartType::class);

        //Comportement par défaut affichage des données sur l'année en cours
        $date = new \DateTime();
        //Pour message d'information si année 2021 selectionnée voir template chart.html.twig
        $year = $date->format('Y');
        //stats du 1er janvier de l'année en cours ...
        $startDate = new \DateTime($year.'-01-01');
        //... à aujourd'hui 
        $endDate = new \DateTime();
        $endDate->format('Y-m-d');
        $status = 'Faite';
        //Si formulaire validé alors nouvelle requete avec les données envoyées
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //On récupère les données envoyées par le formulaire
            $data = $form->getData();
            //Date de dénbut de stats
            $startDate = $data['startDate'];
            //Date de fin de stats
            $endDate = $data['endDate'];
           
        }
        //ON récupère les taches terminées sur la période selectionnée (défaut ou via le formulaire)
        $doneTasksPerPeriod = $taskRepository->findByTaskByStatusAndDate($status,$startDate, $endDate);
         //ON récupère les projets terminés sur la période selectionnée (défaut ou via le formulaire)
        $doneProjectsPerPeriod = $projectRepository->findProjectByStatusAndDate($status,$startDate, $endDate);
       

        $allDoneTasks = [];
        $allDoneTasksCounter = 0;
        $allDoneProjectsCounter = 0;
        $allDoneProjectsPerDirection = [];

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
        // 2 .
        //Ici on compte les valeurs identiques du tableau $monthLabels 
        $taskPerMonth =  array_count_values($allDoneTasks);
        /* $taskPerMonth est de la forme :
        /*array:7 [▼
            "2021 06" => 2
            "2021 07" => 1
        ]*/
        //Ici on scinde le tableau en deux sous tableaux de données (labels et data) pour alimenter le chart builder
        $tasksPerMonthLabels = array_keys($taskPerMonth);

        $labelsTostring = [];
        /*array:1 [▼
            0 => "2023 02"
        ]*/
        //Ici on modifier les valeurs de $labels pour l'affichage de 2023 02 -> Feb 2023
        foreach ($tasksPerMonthLabels as $value) {
            $explodedValue = explode(' ',$value);
            /*array:2 [▼
                0 => "2022"
                1 => "01"
            ]*/
            $date = $explodedValue[0].'-'.$explodedValue[1].'-01';
            /*"2022-01-01"*/
            $month = new \DateTime($date);
            /*DateTime @1640995200 {#18 ▼
                date: 2022-01-01 00:00:00.0 UTC (+00:00)
            }*/
            $label = $month->format('M Y');
            /*Jan 2022*/
            $labelsTostring[] = $label;
        }
        $counter = array_values($taskPerMonth);
        /*array:1 [▼
            0 => 1
        ]*/
        //Enfin
        // On appelle le chart builder et on l'instancie avec les données ci dessus
        $chart01 = $chartBuilder->createChart(Chart::TYPE_BAR);

        $chart01->setData([
            'labels' =>  $labelsTostring,
            'datasets' => [
                [
                    'label' => 'Tâches par mois',
                    'backgroundColor'=> [
						'rgba(255, 227, 227, 0.9)',
						'rgba(0, 232, 252, 0.9)',
						'rgba(170, 255, 229, 0.9)',
						'rgba(157, 117, 203, 0.9)',
						'rgba(255, 99, 132, 0.9)',
						'rgba(54, 162, 235, 0.9)',
						'rgba(255, 206, 86, 0.9)',
						'rgba(75, 192, 192, 0.9)',
						'rgba(153, 102, 255, 0.9)',
						'rgba(255, 159, 64, 0.9)',
						'rgba(249, 110, 70,0.9)',
						'rgba(249, 200, 70, 0.9)',
					],
					'borderColor' => [
						'rgba(255, 227, 227)',
						'rgba(0, 232, 252)',
						'rgba(170, 255, 229)',
						'rgba(157, 117, 203)',
						'rgba(255, 99, 132)',
						'rgba(54, 162, 235)',
						'rgba(255, 206, 86)',
						'rgba(75, 192, 192)',
						'rgba(153, 102, 255)',
						'rgba(255, 159, 64)',
						'rgba(249, 110, 70)',
						'rgba(249, 200, 70)',
					],
					'borderWidth'=> 1,
                    'data' => $counter,
                ],
            ],
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
         $chart02 = $chartBuilder->createChart(Chart::TYPE_PIE);

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

         // FIN DU DEUXIEME GRAPH PROJETS PAR DIRECTIONS
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
            'chart02' => $chart02,
            'projectsCount' => $allDoneProjectsCounter,
            'tasksCount' =>  $allDoneTasksCounter,
            'projects' => $doneProjectsPerPeriod,
            'taskCat' => $taskCategories,
        ]);
    }
}
