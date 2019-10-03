<?php

namespace App\Observers\Helpers;

use App\Event;

trait EventModelMethods
{
    public function createEvent($projectId, $taskId, $eventType, $name) {
        Event::create([
            'user_id' => auth()->user()->id,
            'project_id' => $projectId,
            'task_id' => $taskId,
            'type' => $eventType,
            'name' => $name
        ]);
    }
}
