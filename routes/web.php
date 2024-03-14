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

Route::get('/admin/users', [UserController::class, 'index'])->name('admin.auth.user.index');

Route::get('/admin/roles', [RoleController::class, 'index'])->name('admin.auth.role.index');

// User roles
Route::get('/admin/users/deactivated', [UserController::class, 'deactivated'])->name('admin.auth.user.deactivated');
Route::get('/admin/users/deleted', [UserController::class, 'deleted'])->name('admin.auth.user.deleted');
Route::get('/admin/users/create', [UserController::class, 'create'])->name('admin.auth.user.create');
Route::get('/admin/users/{user}', [UserController::class, 'show'])->name('admin.auth.user.show');
Route::get('/admin/users/{user}/edit', [UserController::class, 'edit'])->name('admin.auth.user.edit');
Route::get('/admin/users/{user}/change-password', [UserController::class, 'changePassword'])->name('admin.auth.user.change-password');
Route::delete('/admin/users/{user}', [UserController::class, 'destroy'])->name('admin.auth.user.destroy');
Route::get('/admin/users/clear-session', [UserController::class, 'clearSession'])->name('admin.auth.user.clear-session');
Route::post('/admin/users/mark', [UserController::class, 'mark'])->name('admin.auth.user.mark');
Route::patch('/admin/users/{user}', [UserController::class, 'update'])->name('admin.auth.user.update');
Route::post('/admin/users', [UserController::class, 'store'])->name('admin.auth.user.store');





// Admin roles
Route::put('/admin/roles/{role}', [RoleController::class, 'roleUpdate'])->name('admin.auth.role.update');

Route::get('/admin/roles/create', [RoleController::class, 'create'])->name('admin.auth.role.create');
Route::post('/admin/roles/store', [RoleController::class, 'store'])->name('admin.auth.role.store');
Route::get('/admin/roles/{role}/edit', [RoleController::class, 'edit'])->name('admin.auth.role.edit');
Route::delete('/admin/roles/{role}', [RoleController::class, 'destroy'])->name('admin.auth.role.destroy');
Route::put('/admin/roles/{role}', [RoleController::class, 'update'])->name('admin.auth.role.update');
