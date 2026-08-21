<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class LlmSetting extends Model
{
    protected $fillable = [
        'frontend_model',
        'backend_model',
        'cron_model',
        'allowed_frontend_models',
    ];

    protected $casts = [
        'allowed_frontend_models' => 'array',
    ];

    public static function current(): self
    {
        if (Schema::hasTable('llm_settings') && ($setting = static::query()->first())) {
            return $setting;
        }

        return new static([
            'frontend_model' => config('llm.frontend_model'),
            'backend_model' => config('llm.backend_model'),
            'cron_model' => config('llm.cron_model'),
            'allowed_frontend_models' => null,
        ]);
    }

    public static function modelFor(string $context): string
    {
        $column = match ($context) {
            'frontend' => 'frontend_model',
            'cron' => 'cron_model',
            default => 'backend_model',
        };

        return static::current()->getAttribute($column) ?: config("llm.{$column}");
    }

    public function frontendModelIsAllowed(?string $model): bool
    {
        if (!$model) {
            return false;
        }

        $allowed = $this->allowed_frontend_models;

        return empty($allowed) || in_array($model, $allowed, true);
    }
}
