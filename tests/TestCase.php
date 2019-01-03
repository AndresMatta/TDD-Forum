<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Emulates a log in to the application.
     *
     * @param App\User | null $user
     * @return $this.
     */
    public function signIn($user = null)
    {
        $user = $user ?: create('App\User');

        $this->be($user);

        return $this;
    }
}
