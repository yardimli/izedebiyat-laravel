<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForumPostLike extends Model
{
    protected $fillable = ['forum_post_id', 'user_id'];

    public function post()
    {
        return $this->belongsTo(ForumPost::class, 'forum_post_id');
    }
}
