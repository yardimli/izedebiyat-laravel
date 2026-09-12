<?php

namespace App\Writer\Models;

use Illuminate\Database\Eloquent\Model;

class Revision extends Model
{
    protected $table = 'writer_revisions';

    protected $guarded = ['id'];

    protected $casts = ['snapshot' => 'array'];
}
