<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ParticipateInForumTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function unauthenticated_users_may_not_add_replies()
    {
        $this->post('/threads/some-channel/1/replies', [])
            ->assertRedirect('/login');
    }

    /** @test */
    public function an_authenticated_user_may_participate_in_forum_threads()
    {
        $this->withoutExceptionHandling();

        $this->be(factory('App\User')->create());

        $thread = factory('App\Thread')->create();
        
        $reply = factory('App\Reply')->make();

        $this->post($thread->path() . ' /replies', $reply->toArray());

        $this->get($thread->path())
            ->assertSee($reply->body);
    }

    /** @test */
    public function a_reply_requires_a_body()
    {
        $thread = create('App\Thread');
        $reply = raw('App\Reply', ['body' => null]);

        $this->signIn()
            ->post($thread->path() . '/replies', $reply)
            ->assertSessionHasErrors('body');
    }
}
