<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ForumPost extends Model
{
    use SoftDeletes;

    protected $fillable = ['forum_discussion_id', 'user_id', 'parent_id', 'body', 'edited_at'];

    protected $casts = ['edited_at' => 'datetime'];

    public function discussion()
    {
        return $this->belongsTo(ForumDiscussion::class, 'forum_discussion_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(ForumPost::class, 'parent_id');
    }

    public function likes()
    {
        return $this->hasMany(ForumPostLike::class);
    }

    public function flags()
    {
        return $this->hasMany(ForumPostFlag::class);
    }
}
