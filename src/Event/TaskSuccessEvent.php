<?php

namespace App\Event;

use App\Entity\Task;
use Symfony\Contracts\EventDispatcher\Event;

class TaskSuccessEvent extends Event
{
    private $task;

    public function __construct(Task $task) {
        $this->task = $task;
    }
    public function getTask() : Task
    {
        return $this->task;
    }
}