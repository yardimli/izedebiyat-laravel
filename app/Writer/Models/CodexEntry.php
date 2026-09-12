<?php

namespace App\Writer\Models;

use Illuminate\Database\Eloquent\Model;

class CodexEntry extends Model
{
    protected $table = 'writer_codex_entries';

    protected $guarded = [];

    protected $casts = ['aliases' => 'array'];
}
