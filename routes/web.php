<?php

use App\Http\Controllers\LocaleController;
use App\Domains\Auth\Http\Controllers\Backend\Role\RoleController;
use App\Domains\Auth\Http\Controllers\Backend\User\UserController;
use Illuminate\Support\Facades\Route;

/*
 * Global Routes
 *
 * Routes that are used between both frontend and backend.
 */

// Switch between the included languages
Route::get('lang/{lang}', [LocaleController::class, 'change'])->name('locale.change');

/*
 * Frontend Routes
 */
Route::group(['as' => 'frontend.'], function () {
    includeRouteFiles(__DIR__ . '/frontend/');
});

/*
 * Backend Routes
 *
 * These routes can only be accessed by users with type `admin`
 */
Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => 'admin'], function () {
    // User routes
    Route::prefix('users')->name('auth.user.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/deleted', [UserController::class, 'deleted'])->name('deleted');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::get('/{user}', [UserController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::get('/{user}/change-password', [UserController::class, 'changePassword'])->name('change-password');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        Route::get('/clear-session', [UserController::class, 'clearSession'])->name('clear-session');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::post('/mark', [UserController::class, 'mark'])->name('mark');
        Route::patch('/{user}', [UserController::class, 'update'])->name('update');
        Route::patch('/{id}/deactivate', [UserController::class, 'deactivate'])->name('deactivate');
        Route::patch('/{id}/restore', [UserController::class, 'restore'])->name('restore');
        Route::delete('/clear-session/{user}', [UserController::class, 'clearSession'])->name('clear-session');
    });

    // Role routes
    Route::prefix('roles')->name('auth.role.')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('index');
        Route::get('/create', [RoleController::class, 'create'])->name('create');
        Route::post('/store', [RoleController::class, 'store'])->name('store');
        Route::get('/{role}/edit', [RoleController::class, 'edit'])->name('edit');
        Route::delete('/{role}', [RoleController::class, 'destroy'])->name('destroy');
        Route::put('/{role}', [RoleController::class, 'update'])->name('update');
    });

    includeRouteFiles(__DIR__ . '/backend/');
});
