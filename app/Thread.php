<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Notifications\ThreadWasUpdated;
use App\Events\ThreadReceiveNewReply;

class Thread extends Model
{
    use RecordsActivity;

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
     * Description.
     *
     * @var array
     */
    protected $appends = ['isSubscribedTo'];

    /**
     * Boot the model.
     *
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($thread) {
            $thread->replies->each->delete();
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
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
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
     * Description.
     *
     * @param
     * @return
     */
    public function scopeFilter($query, $filters)
    {
        return $filters->apply($query);
    }

    /**
     * It creates a Reply attached to the Thread.
     *
     * @param array $reply
     * @return App\Reply $reply
     */
    public function addReply($reply)
    {
        $reply = $this->replies()->create($reply);

        // Prepare notifications for all subscribers.

        event(new ThreadReceiveNewReply($reply));

        return $reply;
    }

    /**
     * Subscribes a user to the current thread.
     *
     * @param int $userId
     * @return $this
     */
    public function subscribe($userId = null)
    {
        $this->subscriptions()->create([
            'user_id' => $userId ?: auth()->id()
        ]);

        return $this;
    }

    /**
     * Description.
     *
     * @param int $userId
     */
    public function unsubscribe($userId = null)
    {
        $this->subscriptions()
            ->where('user_id', $userId ?: auth()->id())
            ->delete();
    }

    /**
     * Description.
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subscriptions()
    {
        return $this->hasMany(ThreadSubscription::class);
    }

    /**
     * Description.
     *
     * @param
     * @return
     */
    public function getIsSubscribedToAttribute()
    {
        return $this->subscriptions()
                ->where('user_id', auth()->id())
                ->exists();
    }

    /**
     * Description.
     *
     * @param
     * @return
     */
    public function hasUpdatesFor($user)
    {
        $user = $user ?: auth()->user();

        $key = $user->visitedThreadCacheKey($this);

        return $this->updated_at > cache($key);
    }
}
