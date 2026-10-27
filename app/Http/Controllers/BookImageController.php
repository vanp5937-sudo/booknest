<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;

class BookImageController extends Controller
{
    public function show(string $path)
    {
        $path = ltrim(str_replace('\\', '/', $path), '/');

        if ($path === '' || str_contains($path, '..') || str_contains($path, "\0")) {
            abort(404);
        }

        $disk = Storage::disk('public');
        if ($disk->exists($path)) {
            return response()->file($disk->path($path), [
                'Content-Type' => $disk->mimeType($path) ?: 'application/octet-stream',
                'Cache-Control' => 'public, max-age=3600',
                'X-Content-Type-Options' => 'nosniff',
            ]);
        }

        $publicRoot = realpath(public_path());
        $candidate = realpath(public_path($path));
        if ($publicRoot && $candidate && str_starts_with($candidate, $publicRoot . DIRECTORY_SEPARATOR) && is_file($candidate)) {
            $mime = mime_content_type($candidate) ?: 'application/octet-stream';
            return response()->file($candidate, [
                'Content-Type' => $mime,
                'Cache-Control' => 'public, max-age=3600',
                'X-Content-Type-Options' => 'nosniff',
            ]);
        }

        $placeholder = public_path('images/book-placeholder.svg');
        if (is_file($placeholder)) {
            return response()->file($placeholder, [
                'Content-Type' => 'image/svg+xml',
                'Cache-Control' => 'no-cache',
                'X-Content-Type-Options' => 'nosniff',
            ]);
        }

        abort(404);
    }
}
