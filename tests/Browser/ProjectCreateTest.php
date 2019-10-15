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

        $this->browse(function ($browser) use ($user) {
            $browser->visit('/login')
                    ->type('email', $user->email)
                    ->type('password', 'password')
                    ->press('Login')
                    ->assertPathIs('/project')
                    ->press('Create Project')
                    ->whenAvailable('#createProjectModal', function ($modal) {
                        $modal->type('#projectTitle', 'An awesome project')
                                ->type('#projectDescription', 'Some info here')
                                ->press('Create Project');
                    })
                    ->waitForReload()
                    ->assertSee('An awesome project');
        });
    }
}
