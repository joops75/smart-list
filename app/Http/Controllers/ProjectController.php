<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Project;

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
        
        $projects = $this->getProjectsQuery($getType)->get();
        
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
    public function destroy(Request $request, $id)
    {
        $getQuery = $request->query('get');
        $getType = $getQuery !== 'completed' && $getQuery !== 'incomplete' && $getQuery !== 'empty' && $getQuery !== 'all' ? 'single' : $getQuery;

        if ($getType === 'single') {
            Project::destroy($id);
        } else {
            $projectIds = $this->getProjectsQuery($getType)->pluck('id');
            Project::destroy($projectIds);
        }

        return response()->json('ok', 200);
    }
}
