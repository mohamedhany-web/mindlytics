<?php

use Illuminate\Support\Facades\Route;

/**
 * Webhooks, public storage fallback, and student-portal static assets.
 * These stay first so later routes cannot shadow them.
 * Loaded by routes/web.php. Route order is part of matching behavior.
 */

/*
|--------------------------------------------------------------------------
| Storage files (صور وملفات) - يجب أن يكون أول Route لضمان عدم اعتراضه
| يعمل عند عدم وجود symlink public/storage على الاستضافة
|--------------------------------------------------------------------------
*/
require app_path('Domains/Messaging/routes/webhooks.php');

Route::get('/storage/{path}', function ($path) {
    $path = rawurldecode($path);
    $path = str_replace('..', '', $path);
    $path = ltrim($path, '/');

    $basePath = storage_path('app/public');
    $filePath = $basePath . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
    $filePath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $filePath);

    if (!@file_exists($filePath) || !@is_file($filePath)) {
        if (config('app.debug')) {
            \Log::warning('Storage file not found', ['requested_path' => $path]);
        }
        abort(404, 'File not found');
    }

    $realPath = @realpath($filePath) ?: $filePath;
    $allowedPath = @realpath($basePath) ?: $basePath;
    $normalizedRealPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $realPath);
    $normalizedAllowedPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $allowedPath);

    if ($allowedPath === '' || strpos($normalizedRealPath, $normalizedAllowedPath) !== 0) {
        abort(404, 'Access denied');
    }

    if (!@is_readable($realPath)) {
        abort(403, 'File not readable');
    }

    $mimeType = @mime_content_type($realPath);
    if (!$mimeType) {
        $extension = strtolower(pathinfo($realPath, PATHINFO_EXTENSION));
        $mimeTypes = [
            'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif',
            'webp' => 'image/webp', 'svg' => 'image/svg+xml', 'pdf' => 'application/pdf',
            'doc' => 'application/msword', 'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ];
        $mimeType = $mimeTypes[$extension] ?? 'application/octet-stream';
    }

    $headers = [
        'Content-Type' => $mimeType,
        'Cache-Control' => 'public, max-age=31536000, immutable',
    ];
    if ($mimeType === 'application/pdf') {
        $headers['Content-Disposition'] = 'inline; filename="' . basename($realPath) . '"';
    }

    return response()->file($realPath, $headers);
})->where('path', '.*')->name('storage.file')->middleware('web');

/*
|--------------------------------------------------------------------------
| Student portal static assets (icons/images for dashboard + sidebar)
| Serves from public/images/student-portal when the host does not expose them.
|--------------------------------------------------------------------------
*/
Route::get('/images/student-portal/{asset}', function (string $asset) {
    $asset = basename(str_replace(['..', '/', '\\'], '', $asset));
    $filePath = public_path('images/student-portal/'.$asset);

    if (! @is_file($filePath) || ! @is_readable($filePath)) {
        abort(404, 'Student portal asset not found');
    }

    $realPath = @realpath($filePath) ?: $filePath;
    $allowedDir = @realpath(public_path('images/student-portal')) ?: public_path('images/student-portal');
    $normalizedReal = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $realPath);
    $normalizedAllowed = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $allowedDir);

    if ($allowedDir === '' || strpos($normalizedReal, $normalizedAllowed) !== 0) {
        abort(404, 'Access denied');
    }

    $extension = strtolower(pathinfo($realPath, PATHINFO_EXTENSION));
    $mimeTypes = [
        'svg' => 'image/svg+xml',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'webp' => 'image/webp',
        'gif' => 'image/gif',
    ];
    $mimeType = $mimeTypes[$extension] ?? (@mime_content_type($realPath) ?: 'application/octet-stream');

    return response()->file($realPath, [
        'Content-Type' => $mimeType,
        'Cache-Control' => 'public, max-age=31536000, immutable',
    ]);
})->where('asset', '[A-Za-z0-9._-]+')->name('student-portal.asset')->middleware('web');

/*
|--------------------------------------------------------------------------
| Site photos (homepage hero, about, auth pages)
| Serves from public/images/home when the host's public_html does not have them.
|--------------------------------------------------------------------------
*/
Route::get('/images/home/{asset}', function (string $asset) {
    $filePath = public_path('images/home/'.basename($asset));
    if (! @is_file($filePath) || ! @is_readable($filePath)) {
        abort(404, 'Site image not found');
    }

    $mimeTypes = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp', 'avif' => 'image/avif', 'svg' => 'image/svg+xml'];
    $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

    return response()->file($filePath, [
        'Content-Type' => $mimeTypes[$extension] ?? 'application/octet-stream',
        'Cache-Control' => 'public, max-age=31536000, immutable',
    ]);
})->where('asset', '[A-Za-z0-9_-]+\.(jpg|jpeg|png|webp|avif|svg)')->name('site.image')->middleware('web');

/*
|--------------------------------------------------------------------------
| Stylesheets and scripts (public/css, public/js)
| Serves them from the project when the host's public_html does not have them (e.g. the CRM stylesheet).
|--------------------------------------------------------------------------
*/
Route::get('/{dir}/{asset}', function (string $dir, string $asset) {
    $filePath = public_path($dir.'/'.basename($asset));
    if (! @is_file($filePath) || ! @is_readable($filePath)) {
        abort(404, 'Asset not found');
    }

    return response()->file($filePath, [
        'Content-Type' => $dir === 'css' ? 'text/css; charset=UTF-8' : 'application/javascript; charset=UTF-8',
        'Cache-Control' => 'public, max-age=604800',
    ]);
})->where(['dir' => 'css|js', 'asset' => '[A-Za-z0-9._-]+\.(css|js)'])->name('site.static-asset')->middleware('web');
