<?php

namespace App\Observers;

use App\Task;
use App\Event;

class TaskObserver
{
    /**
     * Handle the task "created" event.
     *
     * @param  \App\Task  $task
     * @return void
     */
    public function created(Task $task)
    {
        Event::create([
            'model' => 'Task',
            'model_id' => $task->id,
            'type' => 'created',
            'name' => $task->name
        ]);
    }

    /**
     * Handle the task "updated" event.
     *
     * @param  \App\Task  $task
     * @return void
     */
    public function updated(Task $task)
    {
        Event::create([
            'model' => 'Task',
            'model_id' => $task->id,
            'type' => 'updated',
            'name' => $task->name
        ]);
    }

    /**
     * Handle the task "deleted" event.
     *
     * @param  \App\Task  $task
     * @return void
     */
    public function deleted(Task $task)
    {
        Event::create([
            'model' => 'Task',
            'model_id' => $task->id,
            'type' => 'deleted',
            'name' => $task->name
        ]);
    }

    /**
     * Handle the task "restored" event.
     *
     * @param  \App\Task  $task
     * @return void
     */
    public function restored(Task $task)
    {
        //
    }

    /**
     * Handle the task "force deleted" event.
     *
     * @param  \App\Task  $task
     * @return void
     */
    public function forceDeleted(Task $task)
    {
        //
    }
}
