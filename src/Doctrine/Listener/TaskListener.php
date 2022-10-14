<?php

namespace App\Doctrine\Listener;

use App\Entity\Task;
use Doctrine\ORM\Event\LifecycleEventArgs;

class TaskListener
{
    // the entity listener methods receive two arguments:
    // the entity instance and the lifecycle event
    
    public function preUpdate(Task $task, LifecycleEventArgs $event)
    {
        // ... do something to notify the changes
      
    }
}