<?php

namespace App\Writer\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ModelCatalog
{
    public function get(): array
    {
        return $this->approved(Cache::get('openrouter.catalog', ['data' => [], 'refreshed_at' => null]));
    }

    private function approved(array $catalog): array
    {
        $allowed = \App\Models\LlmSetting::current()->allowed_frontend_models ?? [];
        $catalog['data'] = array_values(array_filter($catalog['data'], function ($model) use ($allowed) {
            $outputs = $model['architecture']['output_modalities'] ?? [];
            return in_array($model['id'] ?? '', $allowed, true)
                && stripos(($model['name'] ?? '').' '.($model['id'] ?? ''), 'batch') === false
                && in_array('text', $outputs) && !in_array('image', $outputs);
        }));
        usort($catalog['data'], fn ($a, $b) =>
            [(float) ($a['pricing']['prompt'] ?? 0) + (float) ($a['pricing']['completion'] ?? 0), $a['id']]
            <=> [(float) ($b['pricing']['prompt'] ?? 0) + (float) ($b['pricing']['completion'] ?? 0), $b['id']]);
        $priced = array_values(array_filter($catalog['data'], function ($model) {
            foreach (['prompt', 'completion'] as $key) {
                $price = $model['pricing'][$key] ?? null;
                if (!is_numeric($price) || !is_finite((float) $price) || $price < 0) {
                    return false;
                }
            }
            return true;
        }));
        // Choose the lower middle model when there is an even number of options.
        $catalog['default_model'] = $priced ? $priced[intdiv(count($priced) - 1, 2)]['id'] : null;
        return $catalog;
    }

    public function refresh(): array
    {
        try {
            $models = Http::timeout(15)->get(config('writer.openrouter_url').'/models')->throw()->json('data');
            if (! is_array($models)) {
                throw new \RuntimeException(__('Invalid catalog'));
            }
            $catalog = ['data' => $models, 'refreshed_at' => now()->toIso8601String()];
            Cache::forever('openrouter.catalog', $catalog);

            return $this->approved($catalog);
        } catch (\Throwable $e) {
            return $this->get() + ['error' => __('The model catalog could not refresh. Showing the last successful catalog.')];
        }
    }
}
