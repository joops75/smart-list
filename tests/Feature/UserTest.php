<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;

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
}
