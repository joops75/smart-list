<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\User;

// use command "php artisan serve --env dusk.local" and "php artisan dusk --filter <testName>" when running browser tests

class ProjectIndexTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**  @test */

    public function a_message_is_displayed_on_project_page_if_no_projects_have_been_created()
    {
        $user = factory(User::class)->create();

        $this->browse(function ($browser) use ($user) {
            $browser->visit('/login')
                    ->type('email', $user->email)
                    ->type('password', 'password')
                    ->press('Login')
                    ->assertPathIs('/home')
                    ->visit('/project')
                    ->assertSee('No projects saved.');
        });
    }
}
