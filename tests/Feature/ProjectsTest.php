<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;
use App\Project;

class ProjectsTest extends TestCase
{
    use RefreshDatabase;

    /**  @test */

    public function a_user_can_create_a_project()
    {
        $user = $this->login();

        $this->post('/project', [
            'title' => 'A New Project',
            'description' => 'An awesome project',
            
        ])->assertStatus(200);

        $this->assertDatabaseHas('projects', [
            'title' => 'A New Project',
            'description' => 'An awesome project'
        ]);
    }

    /**  @test */
    
    public function a_user_cannot_create_a_project_if_title_empty()
    {
        $user = $this->login();
        
        $this->post('/project', [
            'title' => '',
            'description' => 'An awesome project',
            
        ]);

        $this->assertDatabaseMissing('projects', [
            'title' => '',
            'description' => 'An awesome project'
        ]);
    }

    /**  @test */
    
    public function a_user_cannot_create_a_project_if_description_empty()
    {
        $user = $this->login();
        
        $this->post('/project', [
            'title' => 'A New Project',
            'description' => '',
            
        ]);

        $this->assertDatabaseMissing('projects', [
            'title' => 'A New Project',
            'description' => ''
        ]);
    }

    /**  @test */
    
    public function a_user_cannot_create_a_project_if_not_logged_in()
    {        
        $this->post('/project', [
            'title' => 'A New Project',
            'description' => 'An awesome project'
        ])->assertRedirect('/login');

        $this->assertDatabaseMissing('projects', [
            'title' => 'A New Project',
            'description' => 'An awesome project'
        ]);
    }

    /**  @test */
    
    public function a_project_has_an_associated_user()
    {
        $user = $this->login();

        $this->post('/project', [
            'title' => 'A New Project',
            'description' => 'An awesome project',
            
        ]);

        $this->assertEquals(Project::find(1)->user->id, $user->id);

        $this->post('/project', [
            'title' => 'A Second Project',
            'description' => 'A good project',
            
        ]);

        $this->assertEquals(Project::find(2)->user->id, $user->id);
    }

    /**  @test */
    
    public function a_project_can_have_associated_tasks()
    {
        $user = $this->login();

        $project = factory(Project::class)->create([
            'user_id' => $user->id
        ]);
        
        $task = [
            'project_id' => $project->id,
            'name' => 'A New Task',
            'due_by' => now()->addMinutes(5)
        ];
        $this->post('/task', $task);
        
        $this->assertEquals($project->tasks()->first()->name, $task['name']);
        $this->assertEquals($user->projects()->first()->tasks()->first()->name, $task['name']);
    }

    /**  @test */
    
    public function a_user_can_delete_a_project_and_all_associated_tasks_simultaneously()
    {
        $user = $this->login();

        $project1 = factory(Project::class)->create([
            'user_id' => $user->id
        ]);
        
        $task1 = [
            'project_id' => $project1->id,
            'name' => 'A New Task',
            'due_by' => now()->addMinutes(5)
        ];
        $this->post('/task', $task1);
        
        $task2 = [
            'project_id' => $project1->id,
            'name' => 'A Second Task',
            'due_by' => now()->addMinutes(15)
        ];
        $this->post('/task', $task2);

        $project2 = factory(Project::class)->create([
            'user_id' => $user->id
        ]);
        
        $task3 = [
            'project_id' => $project2->id,
            'name' => 'A Third Task',
            'due_by' => now()->addMinutes(25)
        ];
        $this->post('/task', $task3);
        
        $this->delete('/project/' . $project1->id);

        $this->assertDatabaseMissing('projects', ['title' => $project1->title]);
        $this->assertDatabaseMissing('tasks', $task1);
        $this->assertDatabaseMissing('tasks', $task2);

        $this->assertDatabaseHas('projects', ['title' => $project2->title]);
        $this->assertDatabaseHas('tasks', $task3);
    }

    /**  @test */
    
    public function a_project_and_tasks_are_sent_to_the_show_page_when_viewing_a_single_project()
    {
        $this->withoutExceptionHandling();
        $user = $this->login();

        $project1 = factory(Project::class)->create([
            'user_id' => $user->id
        ]);
        
        $task1 = [
            'project_id' => $project1->id,
            'name' => 'A New Task',
            'due_by' => now()->addMinutes(5)
        ];
        $this->post('/task', $task1);

        $this->get('/project/' . $project1->id)
            ->assertStatus(200)
            ->assertSee($project1->title)
            ->assertSee('A New Task');
    }
}
