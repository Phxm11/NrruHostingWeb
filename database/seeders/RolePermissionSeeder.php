<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/** @deprecated Role management is not part of this handover. */
class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $this->command?->warn('This legacy role seeder is disabled. Use php artisan staff:create.');
    }
}
