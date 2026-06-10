<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class PublicUploadService
{
    /**
     * Store a file directly under public/ so it is browser-accessible via asset().
     * Returns path relative to public/ (e.g. uploads/gigs/images/file.jpg).
     */
    public static function store(UploadedFile $file, string $directory): string
    {
        $directory = trim($directory, '/');
        $absoluteDir = public_path('uploads/' . $directory);

        if (!File::isDirectory($absoluteDir)) {
            File::makeDirectory($absoluteDir, 0755, true);
        }

        $extension = strtolower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'bin');
        $filename = time() . '_' . Str::random(10) . '.' . $extension;

        $file->move($absoluteDir, $filename);

        return 'uploads/' . $directory . '/' . $filename;
    }

    public static function url(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        if (str_starts_with($path, 'uploads/')) {
            return asset($path);
        }

        return asset('storage/' . ltrim($path, '/'));
    }

    public static function delete(?string $path): void
    {
        if (!$path || str_starts_with($path, 'http')) {
            return;
        }

        if (str_starts_with($path, 'uploads/')) {
            $full = public_path($path);
            if (File::exists($full)) {
                File::delete($full);
            }

            return;
        }

        $storagePath = storage_path('app/public/' . ltrim($path, '/'));
        if (File::exists($storagePath)) {
            File::delete($storagePath);
        }
    }
}
