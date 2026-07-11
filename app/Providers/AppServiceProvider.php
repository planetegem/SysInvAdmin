<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Pulse\Facades\Pulse;
use Laravel\Pulse\Entry;
use Laravel\Pulse\Value;

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

        // PULSE CONFIG
        Pulse::filter(function (Entry|Value $entry) {

            // 1. Check if the entry has a location property pointing to Sentinel Middleware
            if (isset($entry->location) && str_contains($entry->location, 'SentinelMiddleware.php')) {
                return false;
            }

            // 2. Fallback check: Block any session query by checking the raw SQL payload
            if (isset($entry->sql)) {
                $sql = strtolower($entry->sql);
                if (str_contains($sql, 'sessions')) {
                    return false;
                }
            }

            // 3. Block Pulse's own internal metrics table writes
            if (isset($entry->key) && str_contains($entry->key, 'pulse_')) {
                return false;
            }

            return true;
        });
    }
}
