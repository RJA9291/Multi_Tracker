<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Ensure the SQLite database file exists before anything touches the DB
        // (migrations, config caching, requests). Runs on every artisan command too.
        $path = storage_path('app/database.sqlite');
        if (! is_file($path)) {
            @mkdir(dirname($path), 0775, true);
            @touch($path);
        }
    }

    public function boot(): void
    {
        //
    }
}
