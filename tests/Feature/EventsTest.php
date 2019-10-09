<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Project;
use App\Task;

class EventsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */

    public function an_event_is_recorded_when_a_project_or_task_is_created_edited_or_deleted()
    {
        $user = $this->login();

        $project = [
            'title' => 'A new project',
            'description' => 'Some description'
        ];

        $this->post('/project', $project)
                ->assertStatus(200);

        $editedProject = [
            'title' => 'An edited project title',
            'description' => 'Some edited description'
        ];

        $this->put('/project/1', $editedProject)
                ->assertStatus(200);

        $task = [
            'project_id' => 1,
            'name' => 'A new task',
            'due_by' => now()->addMinutes(5)
        ];

        $this->post('/task', $task)
                ->assertStatus(200);

        $editedTask = [
            'name' => 'An edited task name',
            'name' => 'An edited task',
            'due_by' => now()->addMinutes(15)
        ];

        $this->put('/task/1', $editedTask)
                ->assertStatus(200);

        $this->delete('/task/1')
                ->assertStatus(200);

        $this->delete('/project/1')
                ->assertStatus(200);
        
        $this->assertDatabaseHas('events', [
            'project_id' => 1,
            'task_id' => null,
            'type' => 'created',
            'name' => $project['title']
        ]);
        
        $this->assertDatabaseHas('events', [
            'project_id' => 1,
            'task_id' => null,
            'type' => 'updated',
            'name' => $editedProject['title']
        ]);
        
        $this->assertDatabaseHas('events', [
            'project_id' => 1,
            'task_id' => 1,
            'type' => 'created',
            'name' => $task['name']
        ]);
        
        $this->assertDatabaseHas('events', [
            'project_id' => 1,
            'task_id' => 1,
            'type' => 'updated',
            'name' => $editedTask['name']
        ]);
        
        $this->assertDatabaseHas('events', [
            'project_id' => 1,
            'task_id' => 1,
            'type' => 'deleted',
            'name' => $editedTask['name']
        ]);
        
        $this->assertDatabaseHas('events', [
            'project_id' => 1,
            'task_id' => null,
            'type' => 'deleted',
            'name' => $editedProject['title']
        ]);
    }

    /** @test */

    public function multiple_project_and_task_events_are_recorded_when_projects_are_deleted_en_masse()
    {
        $user = $this->login();

        $projects = factory(Project::class, 2)->create(['user_id' => $user->id]);
        $project1 = $projects[0]->toArray();
        $project2 = $projects[1]->toArray();

        $project1Tasks = factory(Task::class, 2)->create(['project_id' => $project1['id']]);
        $project1Task1 = $project1Tasks[0]->toArray();
        $project1Task2 = $project1Tasks[1]->toArray();

        $project2Tasks = factory(Task::class, 2)->create(['project_id' => $project2['id']]);
        $project2Task1 = $project2Tasks[0]->toArray();
        $project2Task2 = $project2Tasks[1]->toArray();

        $this->delete('/project/0?get=all')
                ->assertStatus(200);
        
        $this->assertDatabaseHas('events', [
            'project_id' => $project1['id'],
            'task_id' => null,
            'type' => 'deleted',
            'name' => $project1['title']
        ]);
        
        $this->assertDatabaseHas('events', [
            'project_id' => $project2['id'],
            'task_id' => null,
            'type' => 'deleted',
            'name' => $project2['title']
        ]);
        
        $this->assertDatabaseHas('events', [
            'project_id' => $project1['id'],
            'task_id' => $project1Task1['id'],
            'type' => 'deleted',
            'name' => $project1Task1['name']
        ]);
        
        $this->assertDatabaseHas('events', [
            'project_id' => $project1['id'],
            'task_id' => $project1Task2['id'],
            'type' => 'deleted',
            'name' => $project1Task2['name']
        ]);
        
        $this->assertDatabaseHas('events', [
            'project_id' => $project2['id'],
            'task_id' => $project2Task1['id'],
            'type' => 'deleted',
            'name' => $project2Task1['name']
        ]);
        
        $this->assertDatabaseHas('events', [
            'project_id' => $project2['id'],
            'task_id' => $project2Task2['id'],
            'type' => 'deleted',
            'name' => $project2Task2['name']
        ]);
    }

    /** @test */

    public function multiple_task_events_are_recorded_when_completed_tasks_are_deleted_en_masse()
    {
        $user = $this->login();

        $project = factory(Project::class)->create(['user_id' => $user->id]);

        $projectTasks = factory(Task::class, 2)->create([
            'project_id' => $project->id,
            'completed' => true
        ]);
        $projectTask1 = $projectTasks[0];
        $projectTask2 = $projectTasks[1];

        $this->delete("/task/0?deleteAllCompletedTasksOfAssociatedProject=true&projectId=$projectTask1->project_id")
                ->assertStatus(200);
        
        $this->assertDatabaseHas('events', [
            'project_id' => $project->id,
            'task_id' => $projectTask1->id,
            'type' => 'deleted',
            'name' => $projectTask1->name
        ]);
        
        $this->assertDatabaseHas('events', [
            'project_id' => $project->id,
            'task_id' => $projectTask2->id,
            'type' => 'deleted',
            'name' => $projectTask2['name']
        ]);
    }

    /** @test */

    public function the_latest_10_events_for_a_user_can_be_retrieved_from_the_database() {
        $user = $this->login();

        $projects = factory(Project::class, 5)->create(['user_id' => $user->id]);
        $tasks = factory(Task::class, 6)->create(['project_id' => 1]);

        $this->get('/event')
                ->assertStatus(200)
                ->assertJsonCount(10, 0); // 10 items at key '0'
    }

    /** @test */

    public function any_block_of_10_events_for_a_user_can_be_retrieved_from_the_database() {
        $user = $this->login();

        factory(Project::class, 5)->create(['user_id' => $user->id]);
        factory(Task::class, 6)->create(['project_id' => 1]);

        $this->get('/event?skip=10')
                ->assertStatus(200)
                ->assertJsonCount(1, 0);
                
        factory(Task::class, 10)->create(['project_id' => 1]);

        $this->get('/event?skip=10')
                ->assertStatus(200)
                ->assertJsonCount(10, 0);
    }

    /** @test */

    public function the_latest_10_task_events_for_a_project_can_be_retrieved_from_the_database() {
        $user = $this->login();

        factory(Project::class, 5)->create(['user_id' => $user->id]);
        factory(Task::class, 6)->create(['project_id' => 1]);

        $this->get('/event?get=tasks&projectId=1')
                ->assertStatus(200)
                ->assertJsonCount(6, 0); // 6 items at key '0'

        factory(Task::class, 5)->create(['project_id' => 1]);

        $this->get('/event?get=tasks&projectId=1')
                ->assertStatus(200)
                ->assertJsonCount(10, 0); // 10 items at key '0'
    }

    /** @test */

    public function any_block_of_10_task_events_for_a_project_can_be_retrieved_from_the_database() {
        $user = $this->login();

        factory(Project::class, 5)->create(['user_id' => $user->id]);
        factory(Task::class, 15)->create(['project_id' => 1]);

        $this->get('/event?get=tasks&projectId=1&skip=10')
                ->assertStatus(200)
                ->assertJsonCount(5, 0);
                
        factory(Task::class, 6)->create(['project_id' => 1]);

        $this->get('/event?get=tasks&projectId=1&skip=10')
                ->assertStatus(200)
                ->assertJsonCount(10, 0);
    }

    /** @test */

    public function events_for_a_deleted_task_are_still_displayed() {
        $user = $this->login();

        factory(Project::class, 3)->create(['user_id' => $user->id]);
        factory(Task::class, 3)->create(['project_id' => 1]);

        $this->delete('/task/1')
                ->assertStatus(200);

        $this->get('/event?get=tasks&projectId=1')
                ->assertStatus(200)
                ->assertJsonCount(4, 0);
    }

    /** @test */

    public function a_user_can_delete_all_their_events() {
        $user1 = $this->login();

        $project1 = factory(Project::class)->create(['user_id' => $user1->id]);
        $task1 = factory(Task::class)->create(['project_id' => $project1->id]);

        $user2 = $this->login();

        $project2 = factory(Project::class)->create(['user_id' => $user2->id]);
        $task2 = factory(Task::class)->create(['project_id' => $project2->id]);

        $project1event = [
            'user_id' => $user1->id,
            'project_id' => $project1->id,
            'task_id' => null,
            'type' => 'created',
            'name' => $project1->title
        ];

        $task1event = [
            'user_id' => $user1->id,
            'project_id' => $project1->id,
            'task_id' => $task1->id,
            'type' => 'created',
            'name' => $task1->name
        ];

        $project2event = [
            'user_id' => $user2->id,
            'project_id' => $project2->id,
            'task_id' => null,
            'type' => 'created',
            'name' => $project2->title
        ];

        $task2event = [
            'user_id' => $user2->id,
            'project_id' => $project2->id,
            'task_id' => $task2->id,
            'type' => 'created',
            'name' => $task2->name
        ];

        $this->assertDatabaseHas('events', $project1event);
        $this->assertDatabaseHas('events', $task1event);
        $this->assertDatabaseHas('events', $project2event);
        $this->assertDatabaseHas('events', $task2event);

        $this->delete('/event')
                ->assertStatus(200);

        $this->assertDatabaseHas('events', $project1event);
        $this->assertDatabaseHas('events', $task1event);
        $this->assertDatabaseMissing('events', $project2event);
        $this->assertDatabaseMissing('events', $task2event);
    }

    /** @test */

    public function a_user_can_delete_just_their_task_events_of_one_project() {
        $user1 = $this->login();

        $project1 = factory(Project::class)->create(['user_id' => $user1->id]);
        $task1 = factory(Task::class)->create(['project_id' => $project1->id]);

        $user2 = $this->login();

        $project2 = factory(Project::class)->create(['user_id' => $user2->id]);
        $task2 = factory(Task::class)->create(['project_id' => $project2->id]);
        $task3 = factory(Task::class)->create(['project_id' => $project2->id]);

        $project1event = [
            'user_id' => $user1->id,
            'project_id' => $project1->id,
            'task_id' => null,
            'type' => 'created',
            'name' => $project1->title
        ];

        $task1event = [
            'user_id' => $user1->id,
            'project_id' => $project1->id,
            'task_id' => $task1->id,
            'type' => 'created',
            'name' => $task1->name
        ];

        $project2event = [
            'user_id' => $user2->id,
            'project_id' => $project2->id,
            'task_id' => null,
            'type' => 'created',
            'name' => $project2->title
        ];

        $task2event = [
            'user_id' => $user2->id,
            'project_id' => $project2->id,
            'task_id' => $task2->id,
            'type' => 'created',
            'name' => $task2->name
        ];

        $task3event = [
            'user_id' => $user2->id,
            'project_id' => $project2->id,
            'task_id' => $task3->id,
            'type' => 'created',
            'name' => $task3->name
        ];

        $this->assertDatabaseHas('events', $project1event);
        $this->assertDatabaseHas('events', $task1event);
        $this->assertDatabaseHas('events', $project2event);
        $this->assertDatabaseHas('events', $task2event);
        $this->assertDatabaseHas('events', $task3event);

        $this->delete("/event?delete=tasks&projectId=$project2->id")
                ->assertStatus(200);

        $this->assertDatabaseHas('events', $project1event);
        $this->assertDatabaseHas('events', $task1event);
        $this->assertDatabaseHas('events', $project2event);
        $this->assertDatabaseMissing('events', $task2event);
        $this->assertDatabaseMissing('events', $task3event);
    }
}
