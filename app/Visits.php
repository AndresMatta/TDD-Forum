<?php

namespace App;

use Illuminate\Support\Facades\Redis;

class Visits
{
    protected $thread;

    /**
     * A new instance of Visits.
     *
     * @param App\Thread $thread
     */
    public function __construct($thread)
    {
        $this->thread = $thread;
    }

    
    /**
     * Description.
     *
     * @param
     * @return
     */
    public function record()
    {
        Redis::incr($this->cacheKey());

        return $this;
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
    public function count()
    {
        return Redis::get($this->cacheKey()) ?? 0;
    }

    /**
     * Description.
     *
     * @param
     * @return
     */
    public function cacheKey()
    {
        return "threads.{$this->thread->id}.visits";
    }
}
