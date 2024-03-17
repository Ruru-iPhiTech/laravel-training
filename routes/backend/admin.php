<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\DashboardController;
use Tabuna\Breadcrumbs\Trail;

// All route names are prefixed with 'admin.'.
Route::prefix('admin')->group(function () {
    Route::redirect('/', '/admin/dashboard', 301);
    Route::get('dashboard', [DashboardController::class, 'index'])
        ->name('admin.dashboard')
        ->breadcrumbs(function (Trail $trail) {
            $trail->push(__('Home'), route('admin.dashboard'));
        });
});