<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MentionUsersTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function mentioned_users_in_a_reply_are_notified()
    {
        $john = create('App\User', ['name' => 'JohnDoe']);
        
        $this->signIn($john)
            ->withoutExceptionHandling();

        $jane = create('App\User', ['name' => 'JaneDoe']);

        $thread = create('App\Thread');
        
        $reply = raw('App\Reply', [
            'body' => '@JaneDoe look at this.',
            'thread_id' => $thread->id
        ]);
        
        $this->postJson($thread->path() . '/replies', $reply);

        $this->assertCount(1, $jane->notifications);
    }
}
