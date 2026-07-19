<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForumPostFlag extends Model
{
    protected $fillable = ['forum_post_id', 'user_id', 'reason', 'status'];
}
