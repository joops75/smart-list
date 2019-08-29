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
            'user_id' => $user->id
        ])->assertRedirect('/project');

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
            'user_id' => $user->id
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
            'user_id' => $user->id
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
        ]);

        $this->assertDatabaseMissing('projects', [
            'title' => 'A New Project',
            'description' => 'An awesome project'
        ]);
    }

    /**  @test */
    
    public function a_project_has_an_associated_user()
    {
        $this->withoutExceptionHandling();

        $user = $this->login();

        $this->post('/project', [
            'title' => 'A New Project',
            'description' => 'An awesome project',
            'user_id' => $user->id
        ]);

        $this->assertEquals(Project::find(1)->user->id, $user->id);

        $this->post('/project', [
            'title' => 'A Second Project',
            'description' => 'A good project',
            'user_id' => $user->id
        ]);

        $this->assertEquals(Project::find(2)->user->id, $user->id);
    }
}
