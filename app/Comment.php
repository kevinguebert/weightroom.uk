<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Comment extends Model
{
    protected $primaryKey = 'comment_id';
    protected $dates = ['comment_date'];
    protected $guarded = ['comment_id'];
    protected $appends = ['user_name'];

    /**
     * Get all of the owning commentable models.
     */
    public function commentable()
    {
        return $this->morphTo();
    }

    /**
     * a parent comment can have many children
     *
     * @returns Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function children()
    {
        return $this->hasMany('App\Comment','parent_id')->withTrashed();
    }

    /**
     * get display value
     *
     * @returns bool
     */
    public function getUserNameAttribute()
    {
        return User::find($this->attributes['user_id'])->value('user_name');
    }
}
