<?php

if (! function_exists('versioned_asset')) {
    /**
     * Build an asset URL with a cache-busting `?v=` query string based on
     * the file's last-modified time.
     *
     * Unlike calling filemtime(public_path(...)) directly in a view, this
     * never throws: if the file is missing (not built yet, mid-deploy,
     * accidentally deleted, etc.) it just falls back to a plain asset URL
     * instead of crashing the whole page with a 500 error.
     */
    function versioned_asset(string $path): string
    {
        $fullPath = public_path($path);

        if (! is_file($fullPath)) {
            report_missing_asset($path);

            return asset($path);
        }

        return asset($path).'?v='.filemtime($fullPath);
    }
}

if (! function_exists('report_missing_asset')) {
    /**
     * Log once per request that a referenced asset file was not found,
     * so the situation is still visible (e.g. forgot to run `npm run build`)
     * without taking the page down.
     */
    function report_missing_asset(string $path): void
    {
        static $reported = [];

        if (isset($reported[$path])) {
            return;
        }

        $reported[$path] = true;

        logger()->warning("Asset file not found for versioned_asset(): {$path}");
    }
}
