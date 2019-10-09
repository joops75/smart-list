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
        $user1 = $this->login();
        $project1 = factory(Project::class)->create(['user_id' => $user1->id]);
        $task1 = factory(Task::class)->create(['project_id' => $project1->id]);
        $this->put("/project/$project1->id", [
            'title' => 'A new title',
            'description' => 'Some edited description'
        ])->assertStatus(200);
        $this->put("/task/$task1->id", [
            'name' => 'A new name',
            'due_by' => now()->addMinutes(15)
        ])->assertStatus(200);
        $this->delete("/project/$project1->id")->assertStatus(200);

        $this->assertCount(6, $user1->events()->get());
        $this->assertArraySubset(
            [$user1->events[0]->name, $user1->events[0]->project_id, $user1->events[0]->task_id, $user1->events[0]->type],
            [$project1->title, $project1->id, null, 'created']
        );
        $this->assertArraySubset(
            [$user1->events[1]->name, $user1->events[1]->project_id, $user1->events[1]->task_id, $user1->events[1]->type],
            [$task1->name, $project1->id, $task1->id, 'created']
        );
        $this->assertArraySubset(
            [$user1->events[2]->name, $user1->events[2]->project_id, $user1->events[2]->task_id, $user1->events[2]->type],
            ['A new title', $project1->id, null, 'updated']
        );
        $this->assertArraySubset(
            [$user1->events[3]->name, $user1->events[3]->project_id, $user1->events[3]->task_id, $user1->events[3]->type],
            ['A new name', $project1->id, $task1->id, 'updated']
        );
        $this->assertArraySubset(
            [$user1->events[4]->name, $user1->events[4]->project_id, $user1->events[4]->task_id, $user1->events[4]->type],
            ['A new name', $project1->id, $task1->id, 'deleted']
        );
        $this->assertArraySubset(
            [$user1->events[5]->name, $user1->events[5]->project_id, $user1->events[5]->task_id, $user1->events[5]->type],
            ['A new title', $project1->id, null, 'deleted']
        );

        // repeat but with a different user

        $user2 = $this->login();
        $project2 = factory(Project::class)->create(['user_id' => $user2->id]);
        $task2 = factory(Task::class)->create(['project_id' => $project2->id]);
        $this->put("/project/$project2->id", [
            'title' => 'A new title',
            'description' => 'Some edited description'
        ])->assertStatus(200);
        $this->put("/task/$task2->id", [
            'name' => 'A new name',
            'due_by' => now()->addMinutes(15)
        ])->assertStatus(200);
        $this->delete("/project/$project2->id")->assertStatus(200);

        $this->assertCount(6, $user2->events()->get());
        $this->assertArraySubset(
            [$user2->events[0]->name, $user2->events[0]->project_id, $user2->events[0]->task_id, $user2->events[0]->type],
            [$project2->title, $project2->id, null, 'created']
        );
        $this->assertArraySubset(
            [$user2->events[1]->name, $user2->events[1]->project_id, $user2->events[1]->task_id, $user2->events[1]->type],
            [$task2->name, $project2->id, $task2->id, 'created']
        );
        $this->assertArraySubset(
            [$user2->events[2]->name, $user2->events[2]->project_id, $user2->events[2]->task_id, $user2->events[2]->type],
            ['A new title', $project2->id, null, 'updated']
        );
        $this->assertArraySubset(
            [$user2->events[3]->name, $user2->events[3]->project_id, $user2->events[3]->task_id, $user2->events[3]->type],
            ['A new name', $project2->id, $task2->id, 'updated']
        );
        $this->assertArraySubset(
            [$user2->events[4]->name, $user2->events[4]->project_id, $user2->events[4]->task_id, $user2->events[4]->type],
            ['A new name', $project2->id, $task2->id, 'deleted']
        );
        $this->assertArraySubset(
            [$user2->events[5]->name, $user2->events[5]->project_id, $user2->events[5]->task_id, $user2->events[5]->type],
            ['A new title', $project2->id, null, 'deleted']
        );
    }
}
