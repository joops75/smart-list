<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function login() {
        $user = factory(\App\User::class)->create();
        $this->actingAs($user);

        return $user;
    }
}
