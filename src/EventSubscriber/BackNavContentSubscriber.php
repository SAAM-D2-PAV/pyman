<?php

namespace App\EventSubscriber;

use Twig\Environment;
use App\Repository\EquipmentTypeRepository;
use App\Repository\EquipmentCategoryRepository;
use App\Repository\ProjectCategoryRepository;
use App\Repository\ProjectRepository;
use App\Repository\TaskCategoryRepository;
use App\Repository\TaskRepository;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

//Service de récupération des éléments du menu -> matériel type et catégorie
//src/EventSubscriber/BackNavContentSubscriber.php
//Ce subscriber ajoute des données globales à twig 
class BackNavContentSubscriber implements EventSubscriberInterface
{
    private $twig;
    private $equipmentTypeRepository;
    private $equipmentCategoryRepository;
    private $taskRepository;
    private $taskCategoryRepository;
    private $projectCategoryRepository;

    public function __construct(Environment $twig,EquipmentTypeRepository $equipmentTypeRepository, EquipmentCategoryRepository $equipmentCategoryRepository, TaskRepository $taskRepository, TaskCategoryRepository $taskCategoryRepository, ProjectCategoryRepository $projectCategoryRepository)
    {
        $this->twig = $twig;
        $this->equipmentTypeRepository = $equipmentTypeRepository;
        $this->equipmentCategoryRepository = $equipmentCategoryRepository; 
        $this->taskRepository = $taskRepository;
        $this->taskCategoryRepository = $taskCategoryRepository;
        $this->projectCategoryRepository = $projectCategoryRepository;
    }



    public function onKernelController(ControllerEvent $event)
    {
        $equipmentType = $this->equipmentTypeRepository->findAll();
        $equipmentCategory = $this->equipmentCategoryRepository->findAll();
        $tasks = $this->taskRepository->findAll();
        $taskCategories = $this->taskCategoryRepository->findAll();
        $projectCategories = $this->projectCategoryRepository->findAll();
          

        $this->twig->addGlobal('equipmentType',$equipmentType);
        $this->twig->addGlobal('equipmentCategory',$equipmentCategory);
        $this->twig->addGlobal('ttasks',$tasks);
        $this->twig->addGlobal('taskCategories', $taskCategories);
        $this->twig->addGlobal('projectCategories', $projectCategories);
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.controller' => 'onKernelController',
        ];
    }
}
