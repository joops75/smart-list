<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EventController extends Controller
{
    public function getEvents(Request $request)
    {
        $getQuery = $request->query('get');
        $getType = $getQuery !== 'tasks' ? 'all' : $getQuery;
        $projectId = $request->query('projectId');
        $skipQuery = $request->query('skip');
        $skipAmount = $skipQuery ? $skipQuery : 0;
        
        $events = $this->getEventsQuery($getType, $projectId)->skip($skipAmount)->limit(10)->get()->toArray();

        return response()->json($events, 200);
    }
}
