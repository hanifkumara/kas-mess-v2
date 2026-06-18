<?php

namespace App\Providers;

use App\Models\CashPeriod;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Bagikan periode aktif & daftar periode ke seluruh view (sidebar/topbar).
        // Dijaga dengan hasTable agar tidak error saat `php artisan migrate`
        // sebelum tabel tercipta, atau saat DB belum siap.
        try {
            if (Schema::hasTable('cash_periods')) {
                View::share('sharedActivePeriod', CashPeriod::active()->latest('year')->latest('month')->first());
                View::share('sharedPeriods', CashPeriod::orderByDesc('year')->orderByDesc('month')->get(['id', 'name', 'is_active']));
            }
        } catch (\Throwable $e) {
            // Abaikan: DB belum siap (mis. saat migrasi pertama).
        }
    }
}
