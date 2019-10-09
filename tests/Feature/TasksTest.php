<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Task;
use App\User;
use App\Project;

class TasksTest extends TestCase
{
    use RefreshDatabase;

    /**  @test */
    
    public function a_user_can_create_a_task()
    {
        $user = $this->login();
        $project = factory(Project::class)->create(['user_id' => $user->id]);

        $task = [
            'project_id' => $project->id,
            'name' => 'A New Task',
            'due_by' => now()->addMinutes(5)
        ];

        $this->post('/task', $task)
                ->assertStatus(200);

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
        $project = factory(Project::class)->create(['user_id' => $user->id]);

        $task1 = [
            'project_id' => $project->id,
            'name' => 'A New Task',
            'due_by' => now()->addMinutes(5)
        ];

        $this->post('/task', $task1)
                ->assertStatus(200);

        $task2 = [
            'project_id' => $project->id,
            'name' => 'A Second Task',
            'due_by' => now()->addMinutes(15)
        ];

        $this->post('/task', $task2)
                ->assertStatus(200);

        $this->delete('/task/1')
                ->assertStatus(200);

        $this->assertDatabaseMissing('tasks', $task1);

        $this->assertDatabaseHas('tasks', $task2);
    }

    /**  @test */
    
    public function a_user_can_edit_a_task()
    {
        $user = $this->login();
        $project = factory(Project::class)->create(['user_id' => $user->id]);
        $task = factory(Task::class)->create(['project_id' => $project->id]);

        $this->assertDatabaseHas('tasks', $task->toArray());

        $editedtask = [
            'name' => 'An Edited Name',
            'due_by' => now()->addMinutes(5),
            'completed' => true
        ];

        $this->put("/task/$task->id", $editedtask)
                ->assertStatus(200);

        $this->assertDatabaseMissing('tasks', $task->toArray());

        $this->assertDatabaseHas('tasks', $editedtask);
    }

    /** @test */

    public function a_user_cannot_access_another_users_task_http_routes() {
        $user1 = factory(User::class)->create();
        $user2 = $this->login();

        $project1 = factory(Project::class)->create(['user_id' => $user1->id]);

        $task1 = factory(Task::class)->create(['project_id' => $project1->id]);

        // store route
        $task2 = [
            'project_id' => $project1->id,
            'name' => 'A Second Task',
            'due_by' => now()->addMinutes(15)
        ];

        $this->post('/task', $task2)
                ->assertStatus(403);

        // update route
        $editedtask = [
            'name' => 'An Edited Name',
            'due_by' => now()->addMinutes(5),
            'completed' => true
        ];

        $this->put("/task/$task1->id", $editedtask)
                ->assertStatus(403);

        // destroy route
        $this->delete("/task/$task1->id")
                ->assertStatus(403);

        $this->delete("/task/0?deleteAllCompletedTasksOfAssociatedProject=true&projectId=$task1->project_id")
                ->assertStatus(403);
    }
}
