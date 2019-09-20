<?php

namespace App\Observers\Helpers;

use App\Event;

trait EventModelMethods
{
    public function createEvent($modelType, $modelId, $eventType, $name) {
        Event::create([
            'user_id' => auth()->user()->id,
            'model' => $modelType,
            'model_id' => $modelId,
            'type' => $eventType,
            'name' => $name
        ]);
    }
}
