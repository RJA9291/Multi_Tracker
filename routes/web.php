<?php

use Illuminate\Support\Facades\Route;

// Serve the self-contained PWA shell (built from app.html into public/shell.html).
Route::get('/', function () {
    return response()->file(public_path('shell.html'));
});
