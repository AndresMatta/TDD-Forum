<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    /**
     * A channel has many Threads.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function threads()
    {
        return $this->hasMany(Thread::class);
    }

    /**
     * Overrides the route key for the model.
     *
     * @return string 
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }
}
