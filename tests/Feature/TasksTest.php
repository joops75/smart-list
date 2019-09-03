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

    /**  @test */
    
    public function a_guest_cannot_create_a_task()
    {
        $task = [
            'project_id' => 1,
            'name' => 'A New Task',
            'due_by' => now()->addMinutes(5)
        ];

        $this->post('/task', $task)
            ->assertRedirect('/login');

        $this->assertDatabaseMissing('tasks', $task);
    }

    /**  @test */
    
    public function a_user_can_delete_a_task()
    {
        $user = $this->login();

        $task1 = [
            'project_id' => 1,
            'name' => 'A New Task',
            'due_by' => now()->addMinutes(5)
        ];

        $this->post('/task', $task1);

        $task2 = [
            'project_id' => 1,
            'name' => 'A Second Task',
            'due_by' => now()->addMinutes(15)
        ];

        $this->post('/task', $task2);

        $this->delete('/task/1');

        $this->assertDatabaseMissing('tasks', $task1);

        $this->assertDatabaseHas('tasks', $task2);
    }
}
