<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
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

        static::created(function ($thread) {
            $thread->update(['slug' => $thread->title]);
        });
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Return the string path of the Thread.
     *
     * @return string
     */
    public function path()
    {
        return "/threads/{$this->channel->slug}/{$this->slug}";
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
     * A thread has replies.
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    /**
     * A thread may have many subscriptions.
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subscriptions()
    {
        return $this->hasMany(ThreadSubscription::class);
    }

    /**
     * Get the visits of the thread.
     *
     * @return App\Visits
     */
    public function visits()
    {
        return new Visits($this);
    }

    /**
     * Applies the given filters to the query.
     *
     * @return mixed
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
     * Unsubscribe the user to the current thread.
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
     * Set the slug attribute.
     *
     * @return string
     */
    public function setSlugAttribute($value)
    {
        $slug = str_slug($value);

        if (static::whereSlug($slug)->exists()) {
            $slug = "{$slug}-" . $this->id;
        }

        $this->attributes['slug'] = $slug;
    }

    /**
     * Determine if the current user is subscribe to the thread.
     *
     * @return bool
     */
    public function getIsSubscribedToAttribute()
    {
        return $this->subscriptions()
                ->where('user_id', auth()->id())
                ->exists();
    }

    /**
     * Determine if the thread has been updated since the user last read.
     *
     * @param App\User $user
     * @return bool
     */
    public function hasUpdatesFor($user)
    {
        $user = $user ?: auth()->user();

        $key = $user->visitedThreadCacheKey($this);

        return $this->updated_at > cache($key);
    }

    /**
     * Marks the current reply as the best.
     *
     * @param int $replyId
     * @return void
     */
    public function markBestReply($replyId)
    {
        $this->update(['best_reply_id' => $replyId]);
    }
}
