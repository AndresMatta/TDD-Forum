<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Thread extends Model
{
    /**
     * Don't auto-apply mass assigment protection.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * It references the related models that will be fetch with the Thread.
     *
     * @var array
     */
    protected $with = ['creator', 'channel'];

    /**
     * Boot the model.
     * 
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('replyCount', function ($builder) {
            $builder->withCount('replies');
        });
    }

    /**
     * Return the string path of the Thread.
     *
     * @return string
     */
    public function path()
    {
        return "/threads/{$this->channel->slug}/{$this->id}";
    }

    /**
     * A thread has replies.
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    /**
     * A thread belongs to an specific user.
     *
     * @return App\User
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * A thread belongs to an specific channel.
     *
     * @return App\Channel
     */
    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    /**
     * It creates a Reply attached to the Thread.
     *
     * @param array $reply
     */
    public function addReply($reply)
    {
        $this->replies()->create($reply);
    }

    /**
     * Description.
     *
     * @param
     * @return
     */
    public function scopeFilter($query, $filters)
    {
        return $filters->apply($query);
    }
}
