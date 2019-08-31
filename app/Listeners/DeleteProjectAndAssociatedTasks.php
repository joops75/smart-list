<?php

namespace App\Listeners;

use App\Events\ProjectDeleted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Task;

class DeleteProjectAndAssociatedTasks
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ProjectDeleted  $event
     * @return void
     */
    public function handle(ProjectDeleted $event)
    {
        Task::where('project_id', '=', $event->project->id)->delete();
    }
}
