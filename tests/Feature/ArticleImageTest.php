<?php

namespace Tests\Feature;

use App\Helpers\ArticleImage;
use App\Helpers\MyHelper;
use App\Models\Article;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Tests\TestCase;

class ArticleImageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        config(['image-resizer.driver' => 'gd']);
    }

    private function source(string $path = 'yazi_resimler/sample.jpg'): string
    {
        $image = (new ImageManager(['driver' => 'gd']))->canvas(1200, 800, '#336699');
        Storage::disk('public')->put($path, (string) $image->encode('jpg'));
        $image->destroy();
        return $path;
    }

    public function test_variants_are_stored_and_reused_without_cache_or_expiration(): void
    {
        $source = $this->source();
        $disk = Storage::disk('public');
        $url = ArticleImage::url($source);
        $path = 'article-images/'.explode('/article-images/', $url)[1];
        $this->assertTrue($disk->exists($path));
        $this->assertSame([200, 120], array_slice(getimagesize($disk->path($path)), 0, 2));
        // A marker timestamp detects rewriting even when encoded bytes are identical.
        touch($disk->path($path), 1000000000);
        Cache::flush();
        $this->travel(2)->days();
        $this->assertSame($url, ArticleImage::url($source));
        clearstatcache();
        $this->assertSame(1000000000, filemtime($disk->path($path)));
        $disk->delete($path);
        $this->assertSame($url, ArticleImage::url($source));
        $this->assertTrue($disk->exists($path));
    }

    public function test_all_display_sizes_and_model_urls_use_persistent_variants(): void
    {
        $this->source();
        $article = new Article(['featured_image' => 'sample.jpg']);
        foreach (['small' => [200, 120], 'medium' => [500, 300], 'large' => [800, 400]] as $size => $dimensions) {
            $url = $article->{'get'.ucfirst($size).'Url'}();
            $html = MyHelper::getImage('sample.jpg', '', '', '', $size.'_landscape');
            $this->assertStringContainsString(e($url), $html);
            $path = 'article-images/'.explode('/article-images/', $url)[1];
            $this->assertSame($dimensions, array_slice(getimagesize(Storage::disk('public')->path($path)), 0, 2));
        }
    }

    public function test_path_resolution_supports_imports_uploads_and_real_pngs(): void
    {
        $source = $this->source('yazi_resimler/legacy.jpg');
        $this->assertSame($source, ArticleImage::resolve('legacy.png'));
        $this->assertSame($source, ArticleImage::resolve('/storage/yazi_resimler/legacy.jpg'));
        $this->assertSame($source, ArticleImage::resolve('storage\\yazi_resimler\\legacy.jpg'));
        $upload = $this->source('upload-images/original/upload.jpg');
        $this->assertSame($upload, ArticleImage::resolve('upload.jpg'));
        $image = (new ImageManager(['driver' => 'gd']))->canvas(400, 300, '#ff0000');
        Storage::disk('public')->put('actual.png', (string) $image->encode('png'));
        $image->destroy();
        $this->assertSame('actual.png', ArticleImage::resolve('actual.png'));
        $this->assertStringContainsString('/article-images/', MyHelper::getImage('actual.png'));
        $this->assertNull(ArticleImage::resolve('../private.jpg'));
    }

    public function test_source_changes_and_identical_basenames_have_distinct_variants(): void
    {
        $source = $this->source();
        $first = ArticleImage::url($source);
        $other = $this->source('other/sample.jpg');
        $this->assertNotSame($first, ArticleImage::url($other));
        touch(Storage::disk('public')->path($source), time() + 10);
        clearstatcache();
        $this->assertNotSame($first, ArticleImage::url($source));
    }

    public function test_generation_in_progress_returns_original_without_waiting(): void
    {
        $source = $this->source();
        $disk = Storage::disk('public');
        $url = ArticleImage::url($source);
        $path = 'article-images/'.explode('/article-images/', $url)[1];
        $disk->delete($path);
        $lock = fopen($disk->path(preg_replace('/\.webp$/', '.lock', $path)), 'c');
        flock($lock, LOCK_EX);
        try {
            $this->assertSame($disk->url($source), ArticleImage::url($source));
            $this->assertFalse($disk->exists($path));
        } finally {
            flock($lock, LOCK_UN);
            fclose($lock);
        }
        $this->assertSame($url, ArticleImage::url($source));
    }

    public function test_category_fallback_is_resized_and_invalid_images_fall_back(): void
    {
        $this->source('catpicbox/category.jpg');
        $property = new \ReflectionProperty(MyHelper::class, 'categoryImages');
        $property->setAccessible(true);
        $previous = $property->getValue();
        $property->setValue(null, [42 => 'category.jpg']);
        try {
            $this->assertStringContainsString('/article-images/', MyHelper::getImage('', 42));
        } finally {
            $property->setValue(null, $previous);
        }
        Storage::disk('public')->put('broken.jpg', 'invalid image');
        $this->assertSame(Storage::disk('public')->url('broken.jpg'), ArticleImage::url('broken.jpg'));
        $this->assertSame([], Storage::disk('public')->files('cache/images'));
    }
}
