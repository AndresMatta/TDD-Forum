<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use RecordsActivity;
    
    /**
     * Don't auto-apply mass assigment protection.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Description.
     *
     * @return Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function favorited()
    {
        return $this->morphTo();
    }
}
