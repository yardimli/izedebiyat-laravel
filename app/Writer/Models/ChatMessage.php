<?php

namespace App\Writer\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChatMessage extends Model
{
    use SoftDeletes;

    protected $table = 'writer_chat_messages';

    protected $guarded = ['id'];

    protected $casts = ['suggestions' => 'array'];
}
