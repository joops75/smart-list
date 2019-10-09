<?php

namespace App\Policies;

use App\User;
use App\Project;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectPolicy
{
    /**
     * Determine whether the user can interact with the project.
     *
     * @param  \App\User  $user
     * @param  \App\Project  $project
     * @return mixed
     */
    public function interact(User $user, Project $project)
    {
        return $user->id == $project->user_id;
    }
}
