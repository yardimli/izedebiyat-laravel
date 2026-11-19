<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;

class ArticleImage
{
    public static function resolve(?string $source): ?string
    {
        $source = ltrim(str_replace('\\', '/', $source ?? ''), '/');
        $source = preg_replace('~^(?:storage/|public/)~', '', $source);
        if ($source === '' || preg_match('~(^|/)\.\.(/|$)|://~', $source)) {
            return null;
        }

        // Older imported PNG names refer to converted JPEG files.
        $names = array_unique([$source, preg_replace('/\.png$/i', '.jpg', $source)]);
        foreach ($names as $name) {
            foreach ([$name, 'yazi_resimler/'.$name, 'upload-images/original/'.$name] as $path) {
                if (is_file(Storage::disk('public')->path($path))) {
                    return $path;
                }
            }
        }

        return null;
    }

    public static function url(string $source, string $template = 'small_landscape'): string
    {
        $disk = Storage::disk('public');
        $original = $disk->url($source);
        $settings = config('image-resizer.templates.'.$template);
        if (!is_array($settings) || !is_file($disk->path($source))) {
            return $original;
        }

        $lock = null;
        $temporary = null;
        $image = null;
        try {
            // Preserve source folders to avoid collisions between equal filenames.
            $format = strtolower(pathinfo($source, PATHINFO_EXTENSION));
            if (!in_array($format, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)) {
                return $original;
            }
            $settings['format'] = $format;
            $target = 'article-images/'.$template.'/'.$source;
            $directory = dirname($target);
            $metadata = $target.'.json';
            // This signature is stored as metadata, never used as a filename or TTL.
            $signature = hash('sha256', json_encode([
                $source, $disk->lastModified($source), $disk->size($source), $settings,
            ]));
            $isCurrent = function () use ($disk, $target, $metadata, $signature): bool {
                return $disk->exists($target) && $disk->exists($metadata)
                    && json_decode($disk->get($metadata), true) === ['signature' => $signature];
            };
            if ($isCurrent()) {
                return $disk->url($target);
            }

            if (!$disk->makeDirectory($directory)) {
                throw new \RuntimeException('Cannot create article image directory.');
            }
            // Do not make concurrent page requests wait for the same image to decode.
            $lock = fopen($disk->path($target.'.lock'), 'c');
            if ($lock === false) {
                throw new \RuntimeException('Cannot open article image lock.');
            }
            if (!flock($lock, LOCK_EX | LOCK_NB)) {
                return $original;
            }
            clearstatcache(true, $disk->path($target));
            if ($isCurrent()) {
                return $disk->url($target);
            }

            $image = (new ImageManager(['driver' => config('image-resizer.driver', 'gd')]))
                ->make($disk->path($source));
            $width = $settings['width'] ?? null;
            $height = $settings['height'] ?? null;
            if (!empty($settings['resize'])) {
                $image->resize($width, $height, function ($constraint) use ($settings) {
                    if (!empty($settings['keepRatio'])) {
                        $constraint->aspectRatio();
                    }
                    $constraint->upsize();
                });
            }
            if (!empty($settings['inCanvas'])) {
                $image->resizeCanvas($width, $height, 'center', false, 'rgba(0, 0, 0, 0)');
            }

            // Publish atomically so another request cannot serve a partially written image.
            $temporary = tempnam($disk->path($directory), 'thumb-');
            if ($temporary === false) {
                throw new \RuntimeException('Cannot create temporary article image.');
            }
            $image->save($temporary, 85, $format);
            if (!chmod($temporary, 0644) || !rename($temporary, $disk->path($target))) {
                throw new \RuntimeException('Cannot publish article image.');
            }
            if (!$disk->put($metadata, json_encode(['signature' => $signature]))) {
                throw new \RuntimeException('Cannot save article image metadata.');
            }

            return $disk->url($target);
        } catch (\Throwable $exception) {
            Log::warning('Article thumbnail generation failed', [
                'source' => $source, 'error' => $exception->getMessage(),
            ]);

            return $original;
        } finally {
            if ($image !== null) {
                $image->destroy();
            }
            if ($temporary && is_file($temporary)) {
                unlink($temporary);
            }
            if (is_resource($lock)) {
                flock($lock, LOCK_UN);
                fclose($lock);
            }
        }
    }
}
