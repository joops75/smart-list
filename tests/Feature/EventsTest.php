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
        $this->withoutExceptionHandling();

        $this->login();

        $project = [
            'user_id' => 1,
            'title' => 'A new project',
            'description' => 'Some description'
        ];

        $this->post('/project', $project);

        $editedProject = [
            'title' => 'An edited project title',
            'description' => 'Some edited description'
        ];

        $this->put('/project/1', $editedProject);

        $task = [
            'project_id' => 1,
            'name' => 'A new task',
            'due_by' => now()->addMinutes(5)
        ];

        $this->post('/task', $task);

        $editedTask = [
            'name' => 'An edited task name',
            'name' => 'An edited task',
            'due_by' => now()->addMinutes(15)
        ];

        $this->put('/task/1', $editedTask);

        $this->delete('/task/1')
                ->assertStatus(200);

        $this->delete('/project/1')
                ->assertStatus(200);
        
        $this->assertDatabaseHas('events', [
            'model' => 'Project',
            'model_id' => 1,
            'type' => 'created',
            'name' => $project['title']
        ]);
        
        $this->assertDatabaseHas('events', [
            'model' => 'Project',
            'model_id' => 1,
            'type' => 'updated',
            'name' => $editedProject['title']
        ]);
        
        $this->assertDatabaseHas('events', [
            'model' => 'Task',
            'model_id' => 1,
            'type' => 'created',
            'name' => $task['name']
        ]);
        
        $this->assertDatabaseHas('events', [
            'model' => 'Task',
            'model_id' => 1,
            'type' => 'updated',
            'name' => $editedTask['name']
        ]);
        
        $this->assertDatabaseHas('events', [
            'model' => 'Task',
            'model_id' => 1,
            'type' => 'deleted',
            'name' => $editedTask['name']
        ]);
        
        $this->assertDatabaseHas('events', [
            'model' => 'Project',
            'model_id' => 1,
            'type' => 'deleted',
            'name' => $editedProject['title']
        ]);
    }

    /** @test */

    public function multiple_project_and_task_events_are_recorded_when_projects_are_deleted_en_masse()
    {
        $this->withoutExceptionHandling();

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
            'model' => 'Project',
            'model_id' => $project1['id'],
            'type' => 'deleted',
            'name' => $project1['title']
        ]);
        
        $this->assertDatabaseHas('events', [
            'model' => 'Project',
            'model_id' => $project2['id'],
            'type' => 'deleted',
            'name' => $project2['title']
        ]);
        
        $this->assertDatabaseHas('events', [
            'model' => 'Task',
            'model_id' => $project1Task1['id'],
            'type' => 'deleted',
            'name' => $project1Task1['name']
        ]);
        
        $this->assertDatabaseHas('events', [
            'model' => 'Task',
            'model_id' => $project1Task2['id'],
            'type' => 'deleted',
            'name' => $project1Task2['name']
        ]);
        
        $this->assertDatabaseHas('events', [
            'model' => 'Task',
            'model_id' => $project2Task1['id'],
            'type' => 'deleted',
            'name' => $project2Task1['name']
        ]);
        
        $this->assertDatabaseHas('events', [
            'model' => 'Task',
            'model_id' => $project2Task2['id'],
            'type' => 'deleted',
            'name' => $project2Task2['name']
        ]);
    }

    /** @test */

    public function multiple_task_events_are_recorded_when_completed_tasks_are_deleted_en_masse()
    {
        $this->withoutExceptionHandling();

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
            'model' => 'Task',
            'model_id' => $projectTask1->id,
            'type' => 'deleted',
            'name' => $projectTask1->name
        ]);
        
        $this->assertDatabaseHas('events', [
            'model' => 'Task',
            'model_id' => $projectTask2['id'],
            'type' => 'deleted',
            'name' => $projectTask2['name']
        ]);
    }
}
