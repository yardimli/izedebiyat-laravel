<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Throwable;

class OpenRouterModelCatalog
{
    private const CACHE_KEY = 'openrouter.all-models.v1';

    public function all(): array
    {
        try {
            $models = Http::acceptJson()
                ->timeout(20)
                ->get('https://openrouter.ai/api/v1/models')
                ->throw()
                ->json('data', []);

            if (!empty($models)) {
                Cache::put(self::CACHE_KEY, $models, now()->addHours(6));
                return $this->normalize($models);
            }
        } catch (Throwable) {
            // Continue with the most recent cached or on-disk catalog.
        }

        $models = Cache::get(self::CACHE_KEY, []);
        if (empty($models) && Storage::disk('public')->exists('llms.json')) {
            $models = json_decode(Storage::disk('public')->get('llms.json'), true) ?: [];
        }

        return $this->normalize($models);
    }

    private function normalize(array $models): array
    {
        $models = array_values(array_filter(array_map(function (array $model): array {
            $input = max(0, (float) ($model['pricing']['prompt'] ?? 0)) * 1_000_000;
            $output = max(0, (float) ($model['pricing']['completion'] ?? 0)) * 1_000_000;

            return [
                'id' => (string) ($model['id'] ?? ''),
                'name' => (string) ($model['name'] ?? $model['id'] ?? ''),
                'description' => (string) ($model['description'] ?? ''),
                'input_per_million' => $input,
                'output_per_million' => $output,
                'combined_per_million' => $input + $output,
            ];
        }, $models), fn (array $model): bool => $model['id'] !== ''));

        usort($models, fn (array $a, array $b): int =>
            [$a['combined_per_million'], $a['name']] <=> [$b['combined_per_million'], $b['name']]
        );

        return $models;
    }
}
