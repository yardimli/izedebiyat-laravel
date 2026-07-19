<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForumTag extends Model
{
    protected $fillable = ['name', 'slug', 'color', 'description', 'sort_order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function discussions()
    {
        return $this->hasMany(ForumDiscussion::class);
    }
}
