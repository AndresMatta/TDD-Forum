<?php

namespace App;

use Illuminate\Support\Facades\Redis;

class Trending
{
    /**
     * Description.
     *
     * @param
     * @return
     */
    public function get()
    {
        return array_map('json_decode', Redis::zrevrange($this->cacheKey(), 0, 4));
    }

    /**
     * Description.
     *
     * @param App\Thread $thread
     * @return void
     */
    public function push($thread)
    {
        Redis::zincrby($this->cacheKey(), 1, json_encode([
            'title' => $thread->title,
            'path' => $thread->path(),
        ]));
    }

    /**
     * Description.
     *
     * @param
     * @return
     */
    public function reset()
    {
        Redis::del($this->cacheKey());
    }

    /**
     * Description.
     *
     * @param
     * @return
     */
    public function cacheKey()
    {
        return app()->environment('testing') ? 'testing_trending_threads' : 'trending_threads';
    }
}
