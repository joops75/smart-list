<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\User;
use App\Project;

// use command "php artisan serve --env dusk.local" and "php artisan dusk --filter <testName>" when running browser tests

class ProjectCreateTest extends DuskTestCase
{
    use DatabaseMigrations;
    
    /**  @test */
    
    public function projects_can_be_seen_after_creation()
    {
        $user = factory(User::class)->create();

        $project1 = factory(Project::class)->create([ 'user_id' => $user->id ]);
        $project2 = factory(Project::class)->create([ 'user_id' => $user->id ]);
        $project3 = factory(Project::class)->create([ 'user_id' => 999999 ]);

        $this->browse(function ($browser) use ($user, $project1, $project2, $project3) {
            $browser->visit('/login')
                    ->type('email', $user->email)
                    ->type('password', 'password')
                    ->press('Login')
                    ->assertPathIs('/home')
                    ->visit('/project')
                    ->assertSee($project1->title)
                    ->assertSee($project2->title)
                    ->assertDontSee($project3->title);
        });
    }
}
