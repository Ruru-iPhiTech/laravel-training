<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\Backend\DashboardController;
use App\Domains\Auth\Http\Controllers\Backend\Role\RoleController;
use App\Domains\Auth\Http\Controllers\Backend\User\UserController;

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
    includeRouteFiles(__DIR__ . '/backend/');
});

Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');



Route::prefix('admin')->group(function () {
    // User roles
    Route::prefix('/users')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('admin.auth.user.index');
        Route::get('/create', [UserController::class, 'create'])->name('admin.auth.user.create');
        Route::post('/', [UserController::class, 'store'])->name('admin.auth.user.store');
        Route::get('/{user}', [UserController::class, 'show'])->name('admin.auth.user.show');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('admin.auth.user.edit');
        Route::patch('/{user}', [UserController::class, 'update'])->name('admin.auth.user.update');
        Route::get('/deleted', [UserController::class, 'deleted'])->name('admin.auth.user.deleted');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('admin.auth.user.destroy');
        Route::get('/deactivated', [UserController::class, 'deactivated'])->name('admin.auth.user.deactivated');
        Route::get('/{user}/change-password', [UserController::class, 'changePassword'])->name('admin.auth.user.change-password');
        Route::get('/clear-session', [UserController::class, 'clearSession'])->name('admin.auth.user.clear-session');
        Route::post('/mark', [UserController::class, 'mark'])->name('admin.auth.user.mark');
    });

    // Admin roles
    Route::prefix('/roles')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('admin.auth.role.index');
        Route::get('/create', [RoleController::class, 'create'])->name('admin.auth.role.create');
        Route::post('/store', [RoleController::class, 'store'])->name('admin.auth.role.store');
        Route::get('/{role}/edit', [RoleController::class, 'edit'])->name('admin.auth.role.edit');
        Route::put('/{role}', [RoleController::class, 'update'])->name('admin.auth.role.update');
        Route::delete('/{role}', [RoleController::class, 'destroy'])->name('admin.auth.role.destroy');
    });
});

