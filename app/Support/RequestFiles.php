<?php

namespace App\Support;

class RequestFiles
{
    public const COLUMNS = ['system_detail_doc_path', 'screenshot_evidence_path', 'signature_image_path'];

    public static function isSafePath(string $path): bool
    {
        return ! str_contains($path, '..') && ! str_contains($path, '\\')
            && ! str_contains($path, ':') && ! str_contains($path, "\0")
            && (str_starts_with($path, 'attachments/') || str_starts_with($path, 'signatures/'));
    }
}
