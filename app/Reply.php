<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Favoritable;

class Reply extends Model
{
    use Favoritable;

    /**
     * Don't auto-apply mass assigment protection.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * It references the related models that will be fetch with the Reply.
     *
     * @var array
     */
    protected $with = ['owner', 'favorites'];

    /**
     * A reply has an owner.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
