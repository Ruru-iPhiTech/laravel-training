<?php

use App\Http\Controllers\LocaleController;
use App\Domains\Auth\Http\Controllers\Backend\Role\RoleController;
use App\Domains\Auth\Http\Controllers\Backend\User\UserController;
use App\Domains\Auth\Http\Controllers\Backend\User\DeletedUserController;


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

Route::prefix('admin')->middleware('admin')->group(function () {
    Route::get('/roles', [RoleController::class, 'index'])->name('admin.auth.role.index');
    Route::get('/roles/create', [RoleController::class, 'create'])->name('admin.auth.role.create');
    Route::post('/roles', [RoleController::class, 'store'])->name('admin.auth.role.store');
    Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('admin.auth.role.edit');
    Route::patch('/roles/{role}', [RoleController::class, 'update'])->name('admin.auth.role.update');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('admin.auth.role.destroy');

});