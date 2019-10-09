<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function getProjectsQuery($getType) {
        $projectsQuery;
        if ($getType === 'completed') {
            $projects = auth()->user()->projects()->withCount([
                'tasks',
                'tasks as completed_tasks_count' => function (Builder $query) {
                    $query->where('completed', true);
                }
            ])->get();
            
            $requestededProjectedIds = [];
            foreach ($projects as $project) {
                if ($project->tasks_count && $project->tasks_count === $project->completed_tasks_count){
                    array_push($requestededProjectedIds, $project->id);
                }
            }

            $projectsQuery = DB::table('projects')->whereIn('id', $requestededProjectedIds);
        } else if ($getType === 'incomplete') {
            $userProjectIds = auth()->user()->projects->pluck('id');
            $requestededProjectedIds = DB::table('tasks')->whereIn('project_id', $userProjectIds)->where('completed', $getType === 'completed')->pluck('project_id');
            
            $projectsQuery = DB::table('projects')->whereIn('id', $requestededProjectedIds);
        } else if ($getType === 'empty') {
            $projects = auth()->user()->projects()->withCount([
                'tasks'
            ])->get();
            
            $requestededProjectedIds = [];
            foreach ($projects as $project) {
                if (!$project->tasks_count){
                    array_push($requestededProjectedIds, $project->id);
                }
            }

            $projectsQuery = DB::table('projects')->whereIn('id', $requestededProjectedIds);
        } else {
            $projectsQuery = auth()->user()->projects();
        }

        return $projectsQuery;
    }

    public function getEventsQuery($getType, $projectId) {
        $eventsQuery;
        if ($getType === 'tasks') {
            $eventsQuery = auth()->user()->events()->where('project_id', $projectId)->whereNotNull('task_id');
        } else {
            $eventsQuery = auth()->user()->events();
        }

        return $eventsQuery;
    }
}
