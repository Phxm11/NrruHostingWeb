<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ใช้ pagination view ที่ออกแบบเองแทนดีไซน์ default ของ Laravel
        // ให้เข้ากับธีมของแดชบอร์ดเจ้าหน้าที่ (ใช้ทุกหน้าที่มี ->links() โดยอัตโนมัติ)
        Paginator::defaultView('vendor.pagination.custom');
        Paginator::defaultSimpleView('vendor.pagination.custom');
    }
}
