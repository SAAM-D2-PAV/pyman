<?php

namespace App\Controller\BackController;

use App\Form\ChartType;
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
    public function index(Request $request, ChartBuilderInterface $chartBuilder, TaskRepository $taskRepository): Response
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

        $doneTasksPerPeriod = $taskRepository->findByTaskByStatusAndDate($status,$startDate, $endDate );
        //Labels
        $monthLabels = [];
        //Data (tasks)
        $taskPerMonth = [];

        foreach ($doneTasksPerPeriod as $task){
            $month = $task->getStartDate()->format('M');
            $years = $task->getStartDate()->format('Y');
            //Variables de mémorisation de la date au format Jan 22
            $date = $month.' '.$years;
            //Insertion de cette date dans le tableau
            $monthLabels[] = $date;     
        }
        // 1 .
        /* $monthLabels est de la forme :
        array:6 [▼
            0 => "Jan 2022"
            1 => "Jan 2022"
            2 => "Jan 2022"
            3 => "Apr 2022"
            4 => "Sep 2022"
            5 => "Oct 2022"
        ] */
        // 2.
        //On garde les valeurs d'entrées uniques
        $monthLabelsUniques = array_unique($monthLabels);
        /* $monthLabelsUniques est de la forme :
        array:4 [▼
            0 => "Jan 2022"
            3 => "Apr 2022"
            4 => "Sep 2022"
            5 => "Oct 2022"
        ] */
        // 3 .
        //On garde seulement les valeurs en supprimant les clés 
       $monthLabelsUniquesValues = array_values($monthLabelsUniques);
       /* $monthLabelsUniquesValues est de la forme :
        array:4 [▼
            "Jan 2022"
            "Apr 2022"
            "Sep 2022"
            "Oct 2022"
         ] */
        // 4 .
        //Ici on compte les valeurs identiques du tableau $monthLabels 
        $taskPerMonth =  array_count_values($monthLabels);
        /* $taskPerMonth est de la forme :
        array:4 [▼
            "Jan 2022" => 3
            "Apr 2022" => 1
            "Sep 2022" => 1
            "Oct 2022" => 1
        ] */
        //ou
        /*array:7 [▼
            "Sep 2021" => 4
            "Aug 2021" => 2
            "Jan 2022" => 3
            "Mar 2023" => 2
            "Apr 2022" => 1
            "Sep 2022" => 1
            "Oct 2022" => 1
        ]*/
        ////////////
        $taskPerMonthSorted[] = sort($taskPerMonth,SORT_STRING);
        ////////////
        //Et enfin on garde seulement les valeurs
        $taskPerMonthValue = array_values($taskPerMonth);
       
        $chart = $chartBuilder->createChart(Chart::TYPE_LINE);

        $chart->setData([
            'labels' => ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
            'datasets' => [
                [
                    'label' => 'Nombre de tâches terminées par mois',
                    'backgroundColor' => 'rgb(255, 99, 132)',
                    'borderColor' => 'rgb(255, 99, 132)',
                    'data' => $taskPerMonthValue,
                ],
            ],
        ]);

        $chart->setOptions([
            'scales' => [
                'y' => [
                    'suggestedMin' => 0,
                    'suggestedMax' => 100,
                ],
            ],
        ]);

        return $this->render('back/new_chart/chart.html.twig', [
            'btnText' => 'Filtrer',
            'btnLabel' => 'bg-aqua_velvet',
            'ico' => 'filter',
            'year' => $year,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'form' => $form->createView(),
            'chart' => $chart
        ]);
    }
}
