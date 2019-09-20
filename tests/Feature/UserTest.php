<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Project;
use App\Task;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**  @test */

    public function a_user_can_have_associated_projects()
    {
        $user = $this->login();

        $this->post('/project', [
            'title' => 'A New Project',
            'description' => 'An awesome project',
            'user_id' => $user->id
        ])->assertStatus(200);

        $this->assertEquals($user->projects()->first()->title, 'A New Project');
    }

    /**  @test */

    public function a_user_can_have_associated_events()
    {
        $this->withoutExceptionHandling();

        $user1 = $this->login();
        $project1 = factory(Project::class)->create(['user_id' => $user1->id]);
        $task1 = factory(Task::class)->create(['project_id' => $project1->id]);
        $this->put("/project/$project1->id", [
            'title' => 'A new title',
            'description' => 'Some edited description'
        ]);
        $this->put("/task/$task1->id", [
            'name' => 'A new name',
            'due_by' => now()->addMinutes(15)
        ]);
        $this->delete("/project/$project1->id");

        $this->assertCount(6, $user1->events()->get());
        $this->assertArraySubset(
            [$user1->events[0]->name, $user1->events[0]->model, $user1->events[0]->type],
            [$project1->title, 'Project', 'created']
        );
        $this->assertArraySubset(
            [$user1->events[1]->name, $user1->events[1]->model, $user1->events[1]->type],
            [$task1->name, 'Task', 'created']
        );
        $this->assertArraySubset(
            [$user1->events[2]->name, $user1->events[2]->model, $user1->events[2]->type],
            ['A new title', 'Project', 'updated']
        );
        $this->assertArraySubset(
            [$user1->events[3]->name, $user1->events[3]->model, $user1->events[3]->type],
            ['A new name', 'Task', 'updated']
        );
        $this->assertArraySubset(
            [$user1->events[4]->name, $user1->events[4]->model, $user1->events[4]->type],
            ['A new name', 'Task', 'deleted']
        );
        $this->assertArraySubset(
            [$user1->events[5]->name, $user1->events[5]->model, $user1->events[5]->type],
            ['A new title', 'Project', 'deleted']
        );

        // repeat but with a different user

        $user2 = $this->login();
        $project2 = factory(Project::class)->create(['user_id' => $user2->id]);
        $task2 = factory(Task::class)->create(['project_id' => $project2->id]);
        $this->put("/project/$project2->id", [
            'title' => 'A new title',
            'description' => 'Some edited description'
        ]);
        $this->put("/task/$task2->id", [
            'name' => 'A new name',
            'due_by' => now()->addMinutes(15)
        ]);
        $this->delete("/project/$project2->id");

        $this->assertCount(6, $user2->events()->get());
        $this->assertArraySubset(
            [$user2->events[0]->name, $user2->events[0]->model, $user2->events[0]->type],
            [$project2->title, 'Project', 'created']
        );
        $this->assertArraySubset(
            [$user2->events[1]->name, $user2->events[1]->model, $user2->events[1]->type],
            [$task2->name, 'Task', 'created']
        );
        $this->assertArraySubset(
            [$user2->events[2]->name, $user2->events[2]->model, $user2->events[2]->type],
            ['A new title', 'Project', 'updated']
        );
        $this->assertArraySubset(
            [$user2->events[3]->name, $user2->events[3]->model, $user2->events[3]->type],
            ['A new name', 'Task', 'updated']
        );
        $this->assertArraySubset(
            [$user2->events[4]->name, $user2->events[4]->model, $user2->events[4]->type],
            ['A new name', 'Task', 'deleted']
        );
        $this->assertArraySubset(
            [$user2->events[5]->name, $user2->events[5]->model, $user2->events[5]->type],
            ['A new title', 'Project', 'deleted']
        );
    }
}
