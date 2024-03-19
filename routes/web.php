<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\Backend\DashboardController;
use App\Domains\Auth\Http\Controllers\Backend\Role\RoleController;
use App\Domains\Auth\Http\Controllers\Backend\User\UserController;
use App\Domains\Auth\Http\Controllers\Backend\User\DeactivatedUserController;
use App\Domains\Auth\Http\Controllers\Backend\User\DeletedUserController;
use App\Domains\Auth\Http\Controllers\Backend\User\UserPasswordController;
use App\Domains\Auth\Http\Controllers\Backend\User\UserSessionController;

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

Route::prefix('admin/auth')->middleware('auth')->group(function () {
    // User management
    Route::prefix('user')->name('admin.auth.user.')->group(function () {
        Route::get('deleted', [DeletedUserController::class, 'index'])->name('deleted');
        Route::get('create', [UserController::class, 'create'])->name('create');
        Route::post('store', [UserController::class, 'store'])->name('store');
        Route::get('{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::patch('{user}', [UserController::class, 'update'])->name('update');
        Route::delete('{user}', [UserController::class, 'destroy'])->name('destroy');
        Route::patch('{deletedUser}/restore', [DeletedUserController::class, 'update'])->name('restore');
        Route::delete('{deletedUser}/permanently-delete', [DeletedUserController::class, 'destroy'])->name('permanently-delete');
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('{user}', [UserController::class, 'show'])->name('show');
        Route::post('{user}/mark/{status}', [DeactivatedUserController::class, 'update'])->name('mark')->where(['status' => '[0,1]']);
        Route::post('{user}/clear-session', [UserSessionController::class, 'update'])->name('clear-session');
        Route::get('{user}/change-password', [UserPasswordController::class, 'edit'])->name('change-password');
        Route::patch('{user}/change-password', [UserPasswordController::class, 'update'])->name('change-password.update');
        Route::get('deactivated', [DeactivatedUserController::class, 'index'])->name('deactivated');
    });
    // Role management
    Route::prefix('role')->name('admin.auth.role.')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('index');
        Route::get('create', [RoleController::class, 'create'])->name('create');
        Route::post('store', [RoleController::class, 'store'])->name('store');
        Route::get('{role}/edit', [RoleController::class, 'edit'])->name('edit');
        Route::put('{role}', [RoleController::class, 'update'])->name('update');
        Route::delete('{role}', [RoleController::class, 'destroy'])->name('destroy');
    });
});
