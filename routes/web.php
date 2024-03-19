<?php

use App\Http\Controllers\LocaleController;
use App\Domains\Auth\Http\Controllers\Backend\User\UserController;
use App\Domains\Auth\Http\Controllers\Backend\Role\RoleController;
use App\Domains\Auth\Http\Controllers\Frontend\Auth\TwoFactorAuthenticationController;
use App\Http\Controllers\Backend\DashboardController;
use Lab404\Impersonate\Controllers\ImpersonateController;
use Lab404\Impersonate\Impersonate;
use Illuminate\Support\Facades\Route;
use Arcanedev\LogViewer\Http\Controllers\LogViewerController;
use App\Http\Controllers\Frontend\User\AccountController;
use App\Http\Controllers\Frontend\User\ProfileController;
use App\Http\Controllers\Frontend\User\DashboardController as FrontendDashboardController;
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
Route::group(['prefix' => 'log-viewer', 'as' => 'log-viewer::', 'middleware' => ['web', 'can:admin.access.log-viewer']], function () {
    Route::get('/dashboard', [LogViewerController::class, 'index'])->name('dashboard');
    Route::get('/logs', [LogViewerController::class, 'listLogs'])->name('logs.list');
    Route::get('/logs/{date}', [LogViewerController::class, 'show'])->name('logs.show');
    Route::get('/logs/{date}/download', [LogViewerController::class, 'download'])->name('logs.download');
    Route::delete('/logs/delete', [LogViewerController::class, 'delete'])->name('logs.delete');
});
Route::group(['as' => 'frontend.'], function () {
    includeRouteFiles(__DIR__.'/frontend/');
});
Route::get('/account/2fa/create', [TwoFactorAuthenticationController::class, 'create'])
    ->name('frontend.auth.account.2fa.create');
Route::patch('/user/profile/update', [ProfileController::class, 'update'])->name('frontend.user.profile.update');
Route::post('/auth/account/2fa/validate-code', [TwoFactorAuthenticationController::class, 'validateCode'])
    ->name('frontend.auth.account.2fa.validateCode');
    Route::get('/user/dashboard', [FrontendDashboardController::class, 'index'])->name('frontend.user.dashboard');
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/users',[UserController::class, 'index'])->name('admin.auth.user.index');
    Route::get('/roles',[RoleController::class, 'index'])->name('admin.auth.role.index');
    Route::get('/users/deactivated', [UserController::class, 'deactivated'])->name('admin.auth.user.deactivated');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('admin.auth.user.show');
    Route::get('admin/auth/user/deleted', 'App\Domains\Auth\Http\Controllers\Backend\User\UserController@deletedusers')->name('admin.auth.user.deleted');
    Route::get('/users/create', [UserController::class, 'create'])->name('admin.auth.user.create');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('admin.auth.user.edit');
    Route::get('/users/{user}/change-password', [UserController::class, 'changePassword'])->name('admin.auth.user.change-password');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('admin.auth.user.destroy');
    Route::get('/users/clear-session', [UserController::class, 'clearSession'])->name('admin.auth.user.clear-session');
    Route::post('/users/mark', [UserController::class, 'mark'])->name('admin.auth.user.mark');
    Route::patch('/users/{user}', [UserController::class, 'update'])->name('admin.auth.user.update');
    Route::post('/users', [UserController::class, 'store'])->name('admin.auth.user.store');
    Route::post('/users/{user}/impersonate', [ImpersonateController::class, 'take'])->name('admin.auth.user.impersonate');
    Route::get('impersonate/{user}', [ImpersonateController::class, 'take'])->name('impersonate');
    Route::get('impersonate/leave', [ImpersonateController::class, 'leave'])->name('impersonate.leave');

    // Admin roles
    Route::put('/admin/roles/{role}', [RoleController::class, 'roleUpdate'])->name('admin.auth.role.update');

    Route::get('/admin/roles/create', [RoleController::class, 'create'])->name('admin.auth.role.create');
    Route::post('/admin/roles/store', [RoleController::class, 'store'])->name('admin.auth.role.store');
    Route::get('/admin/roles/{role}/edit', [RoleController::class, 'edit'])->name('admin.auth.role.edit');
    Route::delete('/admin/roles/{role}', [RoleController::class, 'destroy'])->name('admin.auth.role.destroy');
    Route::patch('/admin/roles/{role}', [RoleController::class, 'update'])->name('admin.auth.role.update');
    /*
    * Backend Routes
    *
    * These routes can only be accessed by users with type `admin`
    */
    Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => 'admin'], function () {
        includeRouteFiles(__DIR__ . '/backend/');
});