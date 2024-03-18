<?php

use App\Http\Controllers\LocaleController;
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
    includeRouteFiles(__DIR__.'/frontend/');
});

/*
 * Backend Routes
 *
 * These routes can only be accessed by users with type `admin`
 */
Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => 'admin'], function () {
    includeRouteFiles(__DIR__.'/backend/');
});


Route::get('/admin/users', [UserController::class, 'index'])->name('admin.auth.user.index');
Route::get('/admin/roles', [RoleController::class, 'index'])->name('admin.auth.role.index');

// User routes
Route::get('/admin/users/deleted', [UserController::class, 'deleted'])->name('admin.auth.user.deleted');
Route::patch('/admin/auth/user/{user}/restore', [DeletedUserController::class, 'restore'])->name('admin.auth.user.restore');
