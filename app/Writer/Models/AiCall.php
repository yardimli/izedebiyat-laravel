<?php

namespace App\Writer\Models;

use Illuminate\Database\Eloquent\Model;

class AiCall extends Model
{
    protected $table = 'writer_ai_calls';

    protected $guarded = ['id'];

    protected $casts = ['request_payload' => 'array'];
}
