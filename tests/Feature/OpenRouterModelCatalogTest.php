<?php

namespace Tests\Feature;

use App\Services\OpenRouterModelCatalog;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OpenRouterModelCatalogTest extends TestCase
{
    private function model(string $id): array
    {
        return ['id' => $id, 'name' => $id, 'pricing' => ['prompt' => '0.000001', 'completion' => '0.000002']];
    }

    public function test_each_load_fetches_a_fresh_catalog(): void
    {
        Http::fake(['*/models' => Http::sequence()
            ->push(['data' => [$this->model('provider/first')]])
            ->push(['data' => [$this->model('provider/latest')]])]);
        $catalog = new OpenRouterModelCatalog;
        $this->assertSame('provider/first', $catalog->load()['models'][0]['id']);
        $result = $catalog->load();
        $this->assertSame('provider/latest', $result['models'][0]['id']);
        $this->assertSame('live', $result['source']);
        $this->assertSame([], $result['warnings']);
        Http::assertSentCount(2);
    }

    public function test_cache_write_exception_does_not_discard_fresh_models(): void
    {
        Http::fake(['*/models' => Http::response(['data' => [$this->model('provider/latest')]])]);
        Cache::shouldReceive('put')->once()->andThrow(new \RuntimeException('Permission denied'));
        $result = (new OpenRouterModelCatalog)->load();
        $this->assertSame('live', $result['source']);
        $this->assertSame('provider/latest', $result['models'][0]['id']);
        $this->assertCount(1, $result['warnings']);
    }

    public function test_failed_cache_write_is_reported_even_without_an_exception(): void
    {
        Http::fake(['*/models' => Http::response(['data' => [$this->model('provider/latest')]])]);
        Cache::shouldReceive('put')->once()->andReturn(false);
        $result = (new OpenRouterModelCatalog)->load();
        $this->assertSame('live', $result['source']);
        $this->assertCount(1, $result['warnings']);
    }

    public function test_failed_fetch_reports_cached_fallback(): void
    {
        Cache::put('openrouter.all-models.v1', [$this->model('provider/old')]);
        Http::fake(['*/models' => Http::response([], 503)]);
        $result = (new OpenRouterModelCatalog)->load();
        $this->assertSame('cache', $result['source']);
        $this->assertSame('provider/old', $result['models'][0]['id']);
        $this->assertCount(1, $result['warnings']);
    }

    public function test_empty_response_reports_missing_catalog(): void
    {
        Cache::forget('openrouter.all-models.v1');
        Storage::fake('public');
        Http::fake(['*/models' => Http::response(['data' => []])]);
        $result = (new OpenRouterModelCatalog)->load();
        $this->assertSame('none', $result['source']);
        $this->assertSame([], $result['models']);
        $this->assertCount(1, $result['warnings']);
    }
}
