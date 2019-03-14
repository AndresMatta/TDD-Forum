<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Reply;

class BestReplyController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param App\Reply $reply
     * @return \Illuminate\Http\Response
     */
    public function store(Reply $reply)
    {
        $this->authorize('update', $reply->thread);

        $reply->thread->markBestReply($reply->id);
    }
}
