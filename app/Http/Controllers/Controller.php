<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function getProjects($getType) {
        $projects;
        if ($getType === 'completed') {
            $projects = Auth()->user()->projects()->withCount([
                'tasks',
                'tasks as completed_tasks_count' => function (Builder $query) {
                    $query->where('completed', true);
                }
            ])->orderBy('id', 'desc')->get();
            
            $requestededProjectedIds = [];
            foreach ($projects as $project) {
                if ($project->tasks_count && $project->tasks_count === $project->completed_tasks_count){
                    array_push($requestededProjectedIds, $project->id);
                }
            }

            $projects = DB::table('projects')->whereIn('id', $requestededProjectedIds)->orderBy('id', 'desc')->get();
        } else if ($getType === 'incomplete') {
            $userProjectIds = auth()->user()->projects->pluck('id');
            $requestededProjectedIds = DB::table('tasks')->whereIn('project_id', $userProjectIds)->where('completed', $getType === 'completed')->pluck('project_id');
            
            $projects = DB::table('projects')->whereIn('id', $requestededProjectedIds)->orderBy('id', 'desc')->get();
        } else if ($getType === 'empty') {
            $projects = Auth()->user()->projects()->withCount([
                'tasks'
            ])->orderBy('id', 'desc')->get();
            
            $requestededProjectedIds = [];
            foreach ($projects as $project) {
                if (!$project->tasks_count){
                    array_push($requestededProjectedIds, $project->id);
                }
            }

            $projects = DB::table('projects')->whereIn('id', $requestededProjectedIds)->orderBy('id', 'desc')->get();
        } else {
            $projects = auth()->user()->projects()->orderBy('id', 'desc')->get();
        }

        return $projects;
    }
}
