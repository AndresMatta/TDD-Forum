<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Thread;
use Carbon\Carbon;

class LockedThreadController extends Controller
{
    /**
     * An administrador can lock a thread.
     *
     * @param App\Thread $thread
     * @return void
     */
    public function update(Thread $thread)
    {
        if ($thread->locked_at) {
            $thread->update(['locked_at' => null]);
        } else {
            $thread->update(['locked_at' => Carbon::now()]);
        }
    }
}
