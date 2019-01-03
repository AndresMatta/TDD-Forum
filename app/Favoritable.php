<?php

namespace App;

trait Favoritable
{
    /**
     * It may be favorited by a user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function favorites()
    {
        return $this->morphMany(Favorite::class, 'favorited');
    }

    /**
     * The loggued in user favorites it.
     *
     */
    public function favorite()
    {
        $attributes = ['user_id' => auth()->id()];

        if (!$this->favorites()->where($attributes)->exists()) {
            return $this->favorites()->create($attributes);
        }
    }

    /**
     * Verify if it has been favorited by the User.
     *
     * @return bool
     */
    public function isFavorited()
    {
        return !! $this->favorites->where('user_id', auth()->id())->count();
    }

    /**
     * Description.
     *
     * @param
     * @return
     */
    public function getFavoritesCountAttribute()
    {
        return $this->favorites->count();
    }   
}