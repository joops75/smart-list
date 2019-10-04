<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EventController extends Controller
{
    public function getEvents(Request $request)
    {
        $getQuery = $request->query('get');
        $getType = $getQuery === 'tasks' ? 'tasks' : 'all';
        $projectId = $request->query('projectId');
        $skipQuery = $request->query('skip');
        $skipAmount = $skipQuery ? $skipQuery : 0;
        
        $events = $this->getEventsQuery($getType, $projectId)->latest()->skip($skipAmount)->limit(10)->get()->toArray();

        return response()->json($events, 200);
    }

    public function deleteEvents(Request $request) {
        $getQuery = $request->query('delete');
        $getType = $getQuery === 'tasks' ? 'tasks' : 'all';
        $projectId = $request->query('projectId');

        $this->getEventsQuery($getType, $projectId)->delete();

        return response()->json('ok', 200);
    }
}
