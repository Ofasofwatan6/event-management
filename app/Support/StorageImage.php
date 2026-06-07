<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

class StorageImage
{
    public static function normalize(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        // If DB stored a full URL, try to extract the disk-relative path from it.
        if (str_starts_with($path, 'http')) {
            $parsedPath = parse_url($path, PHP_URL_PATH);

            if ($parsedPath) {
                $parsedPath = str_replace('\\', '/', $parsedPath);
                $parsedPath = ltrim($parsedPath, '/');

                // DB might contain double prefixes like "/storage/storage/...".
                for ($i = 0; $i < 5; $i++) {
                    $next = preg_replace('#^(public/|storage/)#', '', $parsedPath);
                    if ($next === $parsedPath) {
                        break;
                    }
                    $parsedPath = ltrim($next, '/');
                }

                return $parsedPath ?: null;
            }

            return $path;
        }

        // Normalize user/DB stored paths into a "disk-relative" path
        // compatible with Storage::disk('public')->exists($normalized).
        // Examples we may receive:
        // - "profile_images/avatar.png"
        // - "storage/profile_images/avatar.png"
        // - "/storage/profile_images/avatar.png"
        // - "public/profile_images/avatar.png"
        // - "profile_images\\avatar.png" (Windows separators)
        $path = str_replace('\\', '/', $path);
        $path = ltrim($path, '/');

        // DB might contain paths like "storage/storage/..." if something
        // already wrapped a storage path before saving it.
        for ($i = 0; $i < 5; $i++) {
            $next = preg_replace('#^(public/|storage/)#', '', $path);
            if ($next === $path) {
                break;
            }
            $path = ltrim($next, '/');
        }

        return $path ?: null;
    }

    public static function exists(?string $path): bool
    {
        $normalized = self::normalize($path);

        if (! $normalized || str_starts_with($normalized, 'http')) {
            return false;
        }

        return Storage::disk('public')->exists($normalized);
    }

    public static function url(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (str_starts_with($path, 'http')) {
            // If it's a URL pointing to our `/storage/...` files, validate it.
            $parsedPath = parse_url($path, PHP_URL_PATH);

            if ($parsedPath && (str_starts_with($parsedPath, '/storage/') || str_contains($parsedPath, '/storage/'))) {
                $normalized = self::normalize($path);

                if ($normalized && Storage::disk('public')->exists($normalized)) {
                    return $path;
                }

                return null;
            }

            return $path;
        }

        $normalized = self::normalize($path);

        if ($normalized && Storage::disk('public')->exists($normalized)) {
            return url('/storage/'.$normalized);
        }

        return null;
    }
}
