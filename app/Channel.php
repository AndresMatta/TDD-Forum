<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    /**
     * Overrides the route key for the model.
     *
     * @return string 
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Description.
     *
     * @param
     * @return 
     */
    public function threads()
    {
        return $this->hasMany(Thread::class);
    }
}
