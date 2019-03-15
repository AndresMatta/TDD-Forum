<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LockThreadsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function non_administrador_may_not_lock_threads()
    {
        $this->signIn();

        $thread = create('App\Thread', ['user_id' => auth()->id()]);

        $this->patch(route('locked-threads.update', $thread))->assertStatus(403);

        $this->assertNull($thread->fresh()->locked_at);
    }

    /** @test */
    public function administrador_can_lock_threads()
    {
        $this->signIn(factory('App\User')->states('administrador')->create());
        $this->withoutExceptionHandling();

        $thread = create('App\Thread', ['user_id' => auth()->id()]);

        $this->patch(route('locked-threads.update', $thread))->assertStatus(200);

        $this->assertNotNull($thread->fresh()->locked_at, 'Failed asserting that the thread is locked');
    }

    /** @test */
    public function administrador_can_unlock_threads()
    {
        $this->signIn(factory('App\User')->states('administrador')->create());
        $this->withoutExceptionHandling();

        $thread = create('App\Thread', [
            'user_id' => auth()->id(),
            'locked_at' => Carbon::now()
        ]);

        $this->patch(route('locked-threads.update', $thread))->assertStatus(200);

        $this->assertNull($thread->fresh()->locked_at, 'Failed asserting that the thread is unlocked');
    }

    /** @test */
    public function once_locked_a_thread_may_not_receive_new_replies()
    {
        $this->signIn();

        $thread = create('App\Thread');

        $thread->update(['locked_at' => Carbon::now()]);

        $this->post($thread->path() . '/replies', [
            'body' => 'Foobar',
            'user_id' => auth()->id()
        ])->assertStatus(422);
    }
}
