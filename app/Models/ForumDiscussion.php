<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForumDiscussion extends Model
{
    protected $fillable = [
        'user_id', 'forum_tag_id', 'title', 'slug', 'views_count',
        'is_pinned', 'is_locked', 'last_post_at',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'is_locked' => 'boolean',
        'last_post_at' => 'datetime',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tag()
    {
        return $this->belongsTo(ForumTag::class, 'forum_tag_id');
    }

    public function posts()
    {
        return $this->hasMany(ForumPost::class);
    }

    public function latestPost()
    {
        return $this->hasOne(ForumPost::class)->latestOfMany();
    }
}
