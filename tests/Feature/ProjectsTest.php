<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;

class ProjectsTest extends TestCase
{
    use RefreshDatabase;

    /**  @test */

    public function a_user_can_create_a_project()
    {
        $this->actingAs(factory(User::class)->create());

        $this->post('/project', [
            'title' => 'A New Project',
            'description' => 'An awesome project'
        ])->assertRedirect('/');

        $this->assertDatabaseHas('projects', [
            'title' => 'A New Project',
            'description' => 'An awesome project'
        ]);
    }

    /**  @test */
    
    public function a_user_cannot_create_a_project_if_title_empty()
    {
        $this->actingAs(factory(User::class)->create());
        
        $this->post('/project', [
            'title' => '',
            'description' => 'An awesome project'
        ]);

        $this->assertDatabaseMissing('projects', [
            'title' => '',
            'description' => 'An awesome project'
        ]);
    }

    /**  @test */
    
    public function a_user_cannot_create_a_project_if_description_empty()
    {
        $this->actingAs(factory(User::class)->create());
        
        $this->post('/project', [
            'title' => 'A New Project',
            'description' => ''
        ]);

        $this->assertDatabaseMissing('projects', [
            'title' => 'A New Project',
            'description' => ''
        ]);
    }

    /**  @test */
    
    public function a_user_cannot_create_a_project_if_not_logged_in()
    {
        // $this->withoutExceptionHandling();
        
        $this->post('/project', [
            'title' => 'A New Project',
            'description' => 'An awesome project'
        ]);

        $this->assertDatabaseMissing('projects', [
            'title' => 'A New Project',
            'description' => 'An awesome project'
        ]);
    }
}
