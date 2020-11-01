<?php


namespace App\Manager;


use App\Entity\Project;
use App\Entity\Task;

class TaskManager
{
    /**
     * @param Project $project
     * @param Task|null $task
     * @return Task
     */
    public function createFor(Project $project, ?Task $task = null): Task
    {
        $task = $task ?? new Task();
        $project->addTask($task);
        return $task;
    }

}