<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Task;
use App\Project;

class TaskController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $task = $this->validate($request, [
            'project_id' => 'required',
            'name' => 'required',
            'due_by' => 'required'
        ]);
        
        $projectId = $request->input('project_id');
        $this->authorize('interact', Project::find($projectId));
        
        Task::create(request()->all());

        return response()->json('ok', 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Task $task)
    {
        $this->authorize('interact', Project::find($task->project_id));

        $task->update($request->all());

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
        if ($request->query('deleteAllCompletedTasksOfAssociatedProject')) {
            $projectId = $request->query('projectId');
            $this->authorize('interact', Project::find($projectId));
            $ids = Task::where('project_id', $projectId)->where('completed', true)->pluck('id');
            Task::destroy($ids);
        } else {
            $this->authorize('interact', Task::find($id));
            Task::destroy($id);
        }

        return response()->json('ok', 200);
    }
}
