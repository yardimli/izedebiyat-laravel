<?php

namespace App\Writer\Models;

use Illuminate\Database\Eloquent\Model;

class AiProposal extends Model
{
    protected $table = 'writer_ai_proposals';

    protected $guarded = ['id'];

    protected $casts = ['changes' => 'array', 'decisions' => 'array', 'chat_message_id' => 'integer'];
}
