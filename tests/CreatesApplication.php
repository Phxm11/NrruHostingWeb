<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;

trait CreatesApplication
{
    /**
     * Creates the application.
     */
    public function createApplication(): Application
    {
        if (is_file(__DIR__.'/../bootstrap/cache/config.php')) {
            throw new \RuntimeException('Run php artisan config:clear before testing; tests must use the isolated database.');
        }
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        $driver = config('database.default');
        // Inspect the resolved connection too: a DATABASE_URL can override DB_DATABASE.
        $database = $app['db']->connection()->getDatabaseName();
        if (! $app->environment('testing') || ! (
            ($driver === 'sqlite' && $database === ':memory:') ||
            ($driver === 'mysql' && str_starts_with($database, 'nrru_handover_test_'))
        )) {
            throw new \RuntimeException('Tests require SQLite :memory: or a dedicated nrru_handover_test_* MySQL database.');
        }

        return $app;
    }
}
