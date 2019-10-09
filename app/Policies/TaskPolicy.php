<?php

namespace App\Policies;

use App\User;
use App\Task;
use App\Project;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaskPolicy
{
    /**
     * Determine whether the user can interact with the task.
     *
     * @param  \App\User  $user
     * @param  \App\Task  $task
     * @return mixed
     */
    public function interact(User $user, Task $task)
    {
        $project = Project::find($task->project_id);
        return $user->id == $project->user_id;
    }
}
