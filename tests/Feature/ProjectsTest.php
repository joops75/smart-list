<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;
use App\Project;
use App\Task;

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
        
        $task2 = [
            'project_id' => 99999,
            'name' => 'Another Task',
            'due_by' => now()->addMinutes(15)
        ];
        $this->post('/task', $task2);

        $this->get('/project/' . $project1->id)
            ->assertStatus(200)
            ->assertSee($project1->title)
            ->assertSee('A New Task')
            ->assertDontSee('Another Task');
    }

    /**  @test */
    
    public function a_user_can_edit_a_project()
    {
        $user = $this->login();

        $project = factory(Project::class)->create(['user_id' => 1]);

        $this->assertDatabaseHas('projects', $project->toArray());

        $editedProject = [
            'title' => 'An Edited Title',
            'description' => 'An Edited Description'
        ];

        $this->put("/project/$project->id", $editedProject);

        $this->assertDatabaseMissing('projects', $project->toArray());

        $this->assertDatabaseHas('projects', $editedProject);
    }

    /**  @test */
    
    public function a_user_can_get_a_project_along_with_only_incomplete_tasks()
    {
        $user = $this->login();

        $project = factory(Project::class)->create(['user_id' => 1]);

        $task1 = factory(Task::class)->create(['project_id' => $project->id]);
        $task2 = factory(Task::class)->create(['project_id' => $project->id, 'completed' => true]);
        $task3 = factory(Task::class)->create(['project_id' => 99999]);

        $this->get("/project/$project->id?get=incomplete")
                ->assertSee($task1->name)
                ->assertDontSee($task2->name)
                ->assertDontSee($task3->name);        
    }

    /**  @test */
    
    public function a_user_can_get_a_project_along_with_only_completed_tasks()
    {
        $user = $this->login();

        $project = factory(Project::class)->create(['user_id' => 1]);

        $task1 = factory(Task::class)->create(['project_id' => $project->id]);
        $task2 = factory(Task::class)->create(['project_id' => $project->id, 'completed' => true]);
        $task3 = factory(Task::class)->create(['project_id' => 99999]);

        $this->get("/project/$project->id?get=completed")
                ->assertDontSee($task1->name)
                ->assertSee($task2->name)
                ->assertDontSee($task3->name);        
    }

    /**  @test */
    
    public function a_user_can_simultaneously_delete_all_the_completed_tasks_of_a_project()
    {
        $user = $this->login();

        $project = factory(Project::class)->create(['user_id' => 1]);

        $task1 = factory(Task::class)->create(['project_id' => $project->id]);
        $task2 = factory(Task::class)->create(['project_id' => $project->id, 'completed' => true]);
        $task3 = factory(Task::class)->create(['project_id' => $project->id]);
        $task4 = factory(Task::class)->create(['project_id' => $project->id, 'completed' => true]);

        $this->delete("/task/0?deleteAllCompletedTasksOfAssociatedProject=true&projectId=$task1->project_id");

        $this->assertDatabaseHas('tasks', $task1->toArray());
        $this->assertDatabaseMissing('tasks', $task2->toArray());
        $this->assertDatabaseHas('tasks', $task3->toArray());
        $this->assertDatabaseMissing('tasks', $task4->toArray());
    }

    /**  @test */
    
    public function a_user_can_fetch_only_the_projects_with_all_completed_tasks_or_some_incomplete_tasks_or_no_tasks()
    {
        $this->withoutExceptionHandling();
        $user = $this->login();

        $project1 = factory(Project::class)->create(['user_id' => $user->id]);
        factory(Task::class)->create(['project_id' => $project1->id]);
        factory(Task::class)->create(['project_id' => $project1->id]);

        $project2 = factory(Project::class)->create(['user_id' => $user->id]);
        factory(Task::class)->create(['project_id' => $project2->id]);
        factory(Task::class)->create(['project_id' => $project2->id, 'completed' => true]);

        $project3 = factory(Project::class)->create(['user_id' => $user->id]);
        factory(Task::class)->create(['project_id' => $project3->id, 'completed' => true]);
        factory(Task::class)->create(['project_id' => $project3->id, 'completed' => true]);

        $project4 = factory(Project::class)->create(['user_id' => 99999]);
        factory(Task::class)->create(['project_id' => $project4->id, 'completed' => true]);
        factory(Task::class)->create(['project_id' => $project4->id]);

        $project5 = factory(Project::class)->create(['user_id' => 99999]);
        factory(Task::class)->create(['project_id' => $project5->id, 'completed' => true]);
        factory(Task::class)->create(['project_id' => $project5->id, 'completed' => true]);

        $project6 = factory(Project::class)->create(['user_id' => 99999]);
        factory(Task::class)->create(['project_id' => $project6->id]);
        factory(Task::class)->create(['project_id' => $project6->id]);

        $project7 = factory(Project::class)->create(['user_id' => $user->id]);

        $project8 = factory(Project::class)->create(['user_id' => 99999]);

        $this->get("/project?get=completed")
                ->assertDontSee($project1->title)
                ->assertDontSee($project2->title)
                ->assertSee($project3->title)
                ->assertDontSee($project4->title)
                ->assertDontSee($project5->title)
                ->assertDontSee($project6->title)
                ->assertDontSee($project7->title)
                ->assertDontSee($project8->title);

        $this->get("/project?get=incomplete")
                ->assertSee($project1->title)
                ->assertSee($project2->title)
                ->assertDontSee($project3->title)
                ->assertDontSee($project4->title)
                ->assertDontSee($project5->title)
                ->assertDontSee($project6->title)
                ->assertDontSee($project7->title)
                ->assertDontSee($project8->title);

        $this->get("/project?get=empty")
                ->assertDontSee($project1->title)
                ->assertDontSee($project2->title)
                ->assertDontSee($project3->title)
                ->assertDontSee($project4->title)
                ->assertDontSee($project5->title)
                ->assertDontSee($project6->title)
                ->assertSee($project7->title)
                ->assertDontSee($project8->title);
    }

    /**  @test */
    
    public function a_user_can_simultaneously_delete_multiple_projects_with_varying_completeness_along_with_any_associated_tasks()
    {
        $this->withoutExceptionHandling();
        $user = $this->login();

        // delete all projects with all tasks completed

        $project1 = factory(Project::class)->create(['user_id' => $user->id]);
        $task1 = factory(Task::class)->create(['project_id' => $project1->id, 'completed' => true]);
        $task2 = factory(Task::class)->create(['project_id' => $project1->id, 'completed' => true]);

        $project2 = factory(Project::class)->create(['user_id' => $user->id]);
        $task3 = factory(Task::class)->create(['project_id' => $project2->id, 'completed' => true]);
        $task4 = factory(Task::class)->create(['project_id' => $project2->id, 'completed' => true]);

        $project3 = factory(Project::class)->create(['user_id' => $user->id]);
        $task5 = factory(Task::class)->create(['project_id' => $project3->id, 'completed' => true]);
        $task6 = factory(Task::class)->create(['project_id' => $project3->id]);

        $project4 = factory(Project::class)->create(['user_id' => $user->id]);
        $task7 = factory(Task::class)->create(['project_id' => $project4->id, 'completed' => true]);
        $task8 = factory(Task::class)->create(['project_id' => $project4->id]);

        $project5 = factory(Project::class)->create(['user_id' => $user->id]);
        $task9 = factory(Task::class)->create(['project_id' => $project5->id]);
        $task10 = factory(Task::class)->create(['project_id' => $project5->id]);

        $project6 = factory(Project::class)->create(['user_id' => $user->id]);
        $task11 = factory(Task::class)->create(['project_id' => $project6->id]);
        $task12 = factory(Task::class)->create(['project_id' => $project6->id]);

        $project7 = factory(Project::class)->create(['user_id' => $user->id]);

        $project8 = factory(Project::class)->create(['user_id' => $user->id]);

        $this->delete('/project/0?get=completed')
                ->assertStatus(200);

        $this->assertDatabaseMissing('projects', $project1->toArray());
        $this->assertDatabaseMissing('projects', $project2->toArray());
        $this->assertDatabaseHas('projects', $project3->toArray());
        $this->assertDatabaseHas('projects', $project4->toArray());
        $this->assertDatabaseHas('projects', $project5->toArray());
        $this->assertDatabaseHas('projects', $project6->toArray());
        $this->assertDatabaseHas('projects', $project7->toArray());
        $this->assertDatabaseHas('projects', $project8->toArray());
        
        $this->assertDatabaseMissing('tasks', $task1->toArray());
        $this->assertDatabaseMissing('tasks', $task2->toArray());
        $this->assertDatabaseMissing('tasks', $task3->toArray());
        $this->assertDatabaseMissing('tasks', $task4->toArray());
        $this->assertDatabaseHas('tasks', $task5->toArray());
        $this->assertDatabaseHas('tasks', $task6->toArray());
        $this->assertDatabaseHas('tasks', $task7->toArray());
        $this->assertDatabaseHas('tasks', $task8->toArray());
        $this->assertDatabaseHas('tasks', $task9->toArray());
        $this->assertDatabaseHas('tasks', $task10->toArray());
        $this->assertDatabaseHas('tasks', $task11->toArray());
        $this->assertDatabaseHas('tasks', $task12->toArray());

        // delete all incomplete projects

        $project1 = factory(Project::class)->create(['user_id' => $user->id]);
        $task1 = factory(Task::class)->create(['project_id' => $project1->id, 'completed' => true]);
        $task2 = factory(Task::class)->create(['project_id' => $project1->id, 'completed' => true]);

        $project2 = factory(Project::class)->create(['user_id' => $user->id]);
        $task3 = factory(Task::class)->create(['project_id' => $project2->id, 'completed' => true]);
        $task4 = factory(Task::class)->create(['project_id' => $project2->id, 'completed' => true]);

        $this->delete('/project/0?get=incomplete')
                ->assertStatus(200);

        $this->assertDatabaseHas('projects', $project1->toArray());
        $this->assertDatabaseHas('projects', $project2->toArray());
        $this->assertDatabaseMissing('projects', $project3->toArray());
        $this->assertDatabaseMissing('projects', $project4->toArray());
        $this->assertDatabaseMissing('projects', $project5->toArray());
        $this->assertDatabaseMissing('projects', $project6->toArray());
        $this->assertDatabaseHas('projects', $project7->toArray());
        $this->assertDatabaseHas('projects', $project8->toArray());
        
        $this->assertDatabaseHas('tasks', $task1->toArray());
        $this->assertDatabaseHas('tasks', $task2->toArray());
        $this->assertDatabaseHas('tasks', $task3->toArray());
        $this->assertDatabaseHas('tasks', $task4->toArray());
        $this->assertDatabaseMissing('tasks', $task5->toArray());
        $this->assertDatabaseMissing('tasks', $task6->toArray());
        $this->assertDatabaseMissing('tasks', $task7->toArray());
        $this->assertDatabaseMissing('tasks', $task8->toArray());
        $this->assertDatabaseMissing('tasks', $task9->toArray());
        $this->assertDatabaseMissing('tasks', $task10->toArray());
        $this->assertDatabaseMissing('tasks', $task11->toArray());
        $this->assertDatabaseMissing('tasks', $task12->toArray());

        // delete all empty projects

        $project3 = factory(Project::class)->create(['user_id' => $user->id]);
        $task5 = factory(Task::class)->create(['project_id' => $project3->id, 'completed' => true]);
        $task6 = factory(Task::class)->create(['project_id' => $project3->id]);

        $project4 = factory(Project::class)->create(['user_id' => $user->id]);
        $task7 = factory(Task::class)->create(['project_id' => $project4->id, 'completed' => true]);
        $task8 = factory(Task::class)->create(['project_id' => $project4->id]);

        $project5 = factory(Project::class)->create(['user_id' => $user->id]);
        $task9 = factory(Task::class)->create(['project_id' => $project5->id]);
        $task10 = factory(Task::class)->create(['project_id' => $project5->id]);

        $project6 = factory(Project::class)->create(['user_id' => $user->id]);
        $task11 = factory(Task::class)->create(['project_id' => $project6->id]);
        $task12 = factory(Task::class)->create(['project_id' => $project6->id]);

        $this->delete('/project/0?get=empty')
                ->assertStatus(200);

        $this->assertDatabaseHas('projects', $project1->toArray());
        $this->assertDatabaseHas('projects', $project2->toArray());
        $this->assertDatabaseHas('projects', $project3->toArray());
        $this->assertDatabaseHas('projects', $project4->toArray());
        $this->assertDatabaseHas('projects', $project5->toArray());
        $this->assertDatabaseHas('projects', $project6->toArray());
        $this->assertDatabaseMissing('projects', $project7->toArray());
        $this->assertDatabaseMissing('projects', $project8->toArray());
        
        $this->assertDatabaseHas('tasks', $task1->toArray());
        $this->assertDatabaseHas('tasks', $task2->toArray());
        $this->assertDatabaseHas('tasks', $task3->toArray());
        $this->assertDatabaseHas('tasks', $task4->toArray());
        $this->assertDatabaseHas('tasks', $task5->toArray());
        $this->assertDatabaseHas('tasks', $task6->toArray());
        $this->assertDatabaseHas('tasks', $task7->toArray());
        $this->assertDatabaseHas('tasks', $task8->toArray());
        $this->assertDatabaseHas('tasks', $task9->toArray());
        $this->assertDatabaseHas('tasks', $task10->toArray());
        $this->assertDatabaseHas('tasks', $task11->toArray());
        $this->assertDatabaseHas('tasks', $task12->toArray());

        // delete all projects

        $project7 = factory(Project::class)->create(['user_id' => $user->id]);

        $project8 = factory(Project::class)->create(['user_id' => $user->id]);

        $this->delete('/project/0?get=all')
                ->assertStatus(200);

        $this->assertDatabaseMissing('projects', $project1->toArray());
        $this->assertDatabaseMissing('projects', $project2->toArray());
        $this->assertDatabaseMissing('projects', $project3->toArray());
        $this->assertDatabaseMissing('projects', $project4->toArray());
        $this->assertDatabaseMissing('projects', $project5->toArray());
        $this->assertDatabaseMissing('projects', $project6->toArray());
        $this->assertDatabaseMissing('projects', $project7->toArray());
        $this->assertDatabaseMissing('projects', $project8->toArray());
        
        $this->assertDatabaseMissing('tasks', $task1->toArray());
        $this->assertDatabaseMissing('tasks', $task2->toArray());
        $this->assertDatabaseMissing('tasks', $task3->toArray());
        $this->assertDatabaseMissing('tasks', $task4->toArray());
        $this->assertDatabaseMissing('tasks', $task5->toArray());
        $this->assertDatabaseMissing('tasks', $task6->toArray());
        $this->assertDatabaseMissing('tasks', $task7->toArray());
        $this->assertDatabaseMissing('tasks', $task8->toArray());
        $this->assertDatabaseMissing('tasks', $task9->toArray());
        $this->assertDatabaseMissing('tasks', $task10->toArray());
        $this->assertDatabaseMissing('tasks', $task11->toArray());
        $this->assertDatabaseMissing('tasks', $task12->toArray());
    }
}
