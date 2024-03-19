<?php

use Illuminate\Support\Facades\Route;
use App\Domains\Auth\Http\Controllers\Backend\User\UserController;
use App\Domains\Auth\Http\Controllers\Backend\Role\RoleController;
use App\Domains\Auth\Http\Controllers\Backend\User\UserPasswordController;
use App\Domains\Auth\Http\Controllers\Backend\User\UserSessionController;

/*
 * Backend Access Controllers
 * All route names are prefixed with 'admin.auth'.
 */
Route::group([
    'prefix' => 'admin/auth',
    'as' => 'admin.auth.',
    'middleware' => config('boilerplate.access.middleware.confirm'),
], function () {
    Route::group([
        'middleware' => 'role:' . config('boilerplate.access.role.admin'),
    ], function () {
        // User Management
        Route::resource('user', UserController::class)->except(['show']);
        Route::patch('user/{user}/restore', [UserController::class, 'restore'])->name('user.restore');
        Route::delete('user/{deletedUser}/permanently-delete', [UserController::class, 'permanentlyDelete'])->name('user.permanently-delete');

        // Role Management
        Route::resource('role', RoleController::class)->except(['show']);

        // Password Management
        Route::get('user/{user}/password/change', [UserPasswordController::class, 'edit'])->name('user.change-password');
        Route::patch('user/{user}/password/change', [UserPasswordController::class, 'update'])->name('user.change-password.update');

        // Session Management
        Route::post('user/clear-session', [UserSessionController::class, 'update'])->name('user.clear-session');
    });

    Route::group([
        'middleware' => 'permission:admin.access.user.list|admin.access.user.deactivate|admin.access.user.reactivate|admin.access.user.clear-session|admin.access.user.impersonate|admin.access.user.change-password',
    ], function () {
        // Deactivated Users
        Route::get('user/deactivated', [UserController::class, 'index'])->name('user.deactivated');

        // Users
        Route::get('user/{user}', [UserController::class, 'show'])->name('user.show');
        Route::post('user/{user}/mark/{status}', [UserController::class, 'mark'])->name('user.mark');
    });
});
