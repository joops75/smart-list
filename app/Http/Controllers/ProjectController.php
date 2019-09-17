<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Project;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $getQuery = $request->query('get');
        $getType = $getQuery !== 'completed' && $getQuery !== 'incomplete' && $getQuery !== 'empty' ? 'all' : $getQuery;
        
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
        
        return view('projects')->withProjects($projects)->withGetType($getType);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $project = $this->validate($request, [
            'title' => 'required',
            'description' => 'required'
        ]);
        
        Project::create(array_merge($project, ['user_id' => auth()->user()->id]));

        return response()->json('ok', 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Project $project)
    {
        $getQuery = $request->query('get');
        $getType = $getQuery !== 'completed' && $getQuery !== 'incomplete' ? 'all' : $getQuery;
        
        if ($getType === 'all') {
            return view('project')->withGetType($getType)
                                    ->withProject($project)
                                    ->withTasks($project->tasks()
                                    ->orderBy('due_by', 'asc')
                                    ->get());
        }

        return view('project')->withGetType($getType)
                                ->withProject($project)
                                ->withTasks($project->tasks()
                                ->where('completed', $getType === 'completed')
                                ->orderBy('due_by', 'asc')
                                ->get());
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Project $project)
    {
        $this->validate($request, [
            'title' => 'required',
            'description' => 'required'
        ]);

        $project->update([ 'title' => request('title'), 'description' => request('description')]);

        return response()->json('ok', 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Project::destroy($id);

        return response()->json('ok', 200);
    }
}
