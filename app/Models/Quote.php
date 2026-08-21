<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Quote extends Model
{
    use HasFactory;

    protected $fillable = ['quote', 'author', 'day', 'month'];

    protected $casts = [
        'day' => 'integer',
        'month' => 'integer',
    ];

    public static function forToday(): ?self
    {
        if (!Schema::hasTable('quotes')) {
            return null;
        }

        $today = Carbon::now('Europe/Istanbul');

        return static::query()
            ->where('day', $today->day)
            ->where('month', $today->month)
            ->inRandomOrder()
            ->first()
            ?? static::query()
                ->whereNull('day')
                ->whereNull('month')
                ->inRandomOrder()
                ->first()
            ?? static::query()->inRandomOrder()->first();
    }
}
