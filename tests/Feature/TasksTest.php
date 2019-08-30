<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TasksTest extends TestCase
{
    use RefreshDatabase;

    /**  @test */
    
    public function a_user_can_create_a_task()
    {
        $user = $this->login();

        $task = [
            'project_id' => 1,
            'name' => 'A New Task',
            'due_by' => now()->addMinutes(5)
        ];

        $this->post('/task', $task);

        $this->assertDatabaseHas('tasks', $task);
    }
}
