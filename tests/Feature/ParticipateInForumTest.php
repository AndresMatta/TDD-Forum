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
        $this->signIn()
            ->withoutExceptionHandling();

        $thread = create('App\Thread');
        $reply = make('App\Reply');

        $this->post($thread->path() . '/replies', $reply->toArray());

        $this->assertDatabaseHas('replies', ['body' => $reply->body]);
        $this->assertEquals(1, $thread->fresh()->replies_count);
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

    /** @test */
    public function unauthorized_users_cannot_delete_replies()
    {
        $reply = create('App\Reply');

        $this->delete("replies/{$reply->id}")
            ->assertRedirect('login');

        $this->signIn()
            ->delete("replies/{$reply->id}")
            ->assertStatus(403);
    }

    /** @test */
    public function authorized_users_can_delete_replies()
    {
        $this->signIn();

        $reply = create('App\Reply', ['user_id' => auth()->id()]);

        $this->delete("replies/{$reply->id}")->assertStatus(302);

        $this->assertDatabaseMissing('replies', ['id' => $reply->id]);
        $this->assertEquals(0, $reply->thread->fresh()->replies_count);
    }

    /** @test */
    public function unauthorized_users_cannot_update_replies()
    {
        $reply = create('App\Reply');

        $this->patch("replies/{$reply->id}")
            ->assertRedirect('login');

        $this->signIn()
            ->patch("replies/{$reply->id}")
            ->assertStatus(403);
    }

    /** @test */
    public function authorized_can_update_replies()
    {
        $this->signIn();

        $reply = create('App\Reply', ['user_id' => auth()->id()]);

        $this->patch("replies/{$reply->id}", ['body' => 'You been changed, fool.']);

        $this->assertDatabaseHas('replies', ['id' => $reply->id, 'body' => 'You been changed, fool.']);
    }

    /** @test */
    public function replies_that_contain_spam_may_not_be_created()
    {
        $this->signIn();

        $thread = create('App\Thread');
        $reply = raw('App\Reply', [
            'body' => 'Yahoo Customer Support'
        ]);

        $this->postJson($thread->path() . '/replies', $reply)
            ->assertStatus(422);
    }

    /** @test */
    public function a_user_may_only_reply_a_maximum_of_once_per_minute()
    {
        $this->signIn();

        $thread = create('App\Thread');

        $reply = raw('App\Reply');

        $this->post($thread->path() . '/replies', $reply)
            ->assertStatus(201);

        $this->post($thread->path() . '/replies', $reply)
            ->assertStatus(429);
    }
}
