<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use App\Support\RequestFiles;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;

class RequestFileController extends Controller
{
    public function show(ServiceRequest $serviceRequest, string $file)
    {
        abort_unless(in_array($file.'_path', RequestFiles::COLUMNS, true), 404);
        $path = $serviceRequest->{$file.'_path'};
        abort_unless($path && RequestFiles::isSafePath($path), 404);
        $disk = Storage::disk('private')->exists($path) ? 'private' : 'public';
        abort_unless(Storage::disk($disk)->exists($path), 404);
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $inline = in_array($extension, ['png', 'jpg', 'jpeg', 'gif', 'webp', 'pdf'], true);

        $headers = [
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
            'Content-Security-Policy' => "default-src 'none'; sandbox",
        ];

        /** @var FilesystemAdapter $filesystem */
        $filesystem = Storage::disk($disk);
        $absolutePath = $filesystem->path($path);

        return $inline
            ? response()->file($absolutePath, $headers)
            : response()->download($absolutePath, basename($path), $headers);
    }
}
