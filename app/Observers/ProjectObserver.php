<?php

namespace App\Observers;

use App\Project;
use App\Task;
use App\Observers\helpers\EventModelMethods;

class ProjectObserver
{
    use EventModelMethods;
    
    /**
     * Handle the project "created" event.
     *
     * @param  \App\Project  $project
     * @return void
     */
    public function created(Project $project)
    {
        $this->createEvent($project->id, null, 'created', $project->title);
    }

    /**
     * Handle the project "updated" event.
     *
     * @param  \App\Project  $project
     * @return void
     */
    public function updated(Project $project)
    {
        $this->createEvent($project->id, null, 'updated', $project->title);
    }

    /**
     * Handle the project "deleted" event.
     *
     * @param  \App\Project  $project
     * @return void
     */
    public function deleted(Project $project)
    {
        $taskIds = $project->tasks()->pluck('id');
        Task::destroy($taskIds);

        $this->createEvent($project->id, null, 'deleted', $project->title);
    }

    /**
     * Handle the project "restored" event.
     *
     * @param  \App\Project  $project
     * @return void
     */
    public function restored(Project $project)
    {
        //
    }

    /**
     * Handle the project "force deleted" event.
     *
     * @param  \App\Project  $project
     * @return void
     */
    public function forceDeleted(Project $project)
    {
        //
    }
}
