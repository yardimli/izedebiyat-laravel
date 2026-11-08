<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class OpenRouterModelCatalog
{
    private const CACHE_KEY = 'openrouter.all-models.v1';

    public function all(): array
    {
        return $this->load()['models'];
    }

    public function load(): array
    {
        $warnings = [];
        try {
            $models = Http::acceptJson()
                ->timeout(20)
                ->get('https://openrouter.ai/api/v1/models')
                ->throw()
                ->json('data', []);

            if (!is_array($models) || empty($models)) {
                throw new \RuntimeException('OpenRouter returned an empty or invalid model catalog.');
            }
            $normalized = $this->normalize($models);
            if (empty($normalized)) {
                throw new \RuntimeException('OpenRouter returned no usable models.');
            }
        } catch (Throwable $e) {
            Log::warning('OpenRouter model catalog refresh failed.', ['exception' => $e]);
            $warnings[] = 'OpenRouter kataloğu yenilenemedi. Bağlantı, zaman aşımı veya yanıt hatası oluştu. Ayrıntılar sunucu günlüğüne kaydedildi.';
            $normalized = null;
        }

        if ($normalized !== null) {
            try {
                if (!Cache::put(self::CACHE_KEY, $models, now()->addHours(6))) {
                    throw new \RuntimeException('Model catalog cache write returned false.');
                }
            } catch (Throwable $e) {
                Log::warning('OpenRouter model catalog cache write failed.', ['exception' => $e]);
                $warnings[] = 'Güncel modeller alındı ancak önbelleğe kaydedilemedi. Bu sayfada güncel liste gösteriliyor. Ayrıntılar sunucu günlüğüne kaydedildi.';
            }

            return ['models' => $normalized, 'source' => 'live', 'warnings' => $warnings];
        }

        foreach (['cache', 'file'] as $source) {
            try {
                $models = $source === 'cache'
                    ? Cache::get(self::CACHE_KEY, [])
                    : (Storage::disk('public')->exists('llms.json') ? json_decode(Storage::disk('public')->get('llms.json'), true) : []);
                $normalized = is_array($models) ? $this->normalize($models) : [];
                if (!empty($normalized)) {
                    return ['models' => $normalized, 'source' => $source, 'warnings' => $warnings];
                }
            } catch (Throwable $e) {
                Log::warning('OpenRouter fallback catalog read failed.', ['source' => $source, 'exception' => $e]);
            }
        }

        return ['models' => [], 'source' => 'none', 'warnings' => $warnings];
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
