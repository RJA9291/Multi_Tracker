<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StateController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/password', [AuthController::class, 'changePassword']);
    Route::get('/state', [StateController::class, 'show']);
    Route::put('/state', [StateController::class, 'update']);

    Route::get('/admin/users', [AdminController::class, 'users']);
    Route::post('/admin/users/{id}/reset', [AdminController::class, 'reset']);
    Route::post('/admin/users/{id}/approve', [AdminController::class, 'approve']);
    Route::delete('/admin/users/{id}', [AdminController::class, 'remove']);
    Route::post('/admin/reset-all', [AdminController::class, 'resetAll']);
});
