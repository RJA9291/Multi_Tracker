<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // This server's shared storage was provisioned without Laravel's writable
        // subdirs, so create them (sessions, cache, views, logs) before anything uses them.
        foreach (['framework/sessions', 'framework/cache/data', 'framework/views', 'logs', 'app/public'] as $dir) {
            $p = storage_path($dir);
            if (! is_dir($p)) {
                @mkdir($p, 0775, true);
            }
        }

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
