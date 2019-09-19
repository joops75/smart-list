<?php

namespace App\Observers;

use App\Project;
use App\Task;
use App\Event;

class ProjectObserver
{
    /**
     * Handle the project "created" event.
     *
     * @param  \App\Project  $project
     * @return void
     */
    public function created(Project $project)
    {
        Event::create([
            'model' => 'Project',
            'model_id' => $project->id,
            'type' => 'created',
            'name' => $project->title
        ]);
    }

    /**
     * Handle the project "updated" event.
     *
     * @param  \App\Project  $project
     * @return void
     */
    public function updated(Project $project)
    {
        Event::create([
            'model' => 'Project',
            'model_id' => $project->id,
            'type' => 'updated',
            'name' => $project->title
        ]);
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

        Event::create([
            'model' => 'Project',
            'model_id' => $project->id,
            'type' => 'deleted',
            'name' => $project->title
        ]);
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
