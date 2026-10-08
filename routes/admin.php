<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\ModuleController;
use App\Http\Controllers\Admin\PermissionController;

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('roles', [RoleController::class, 'index'])->name('roles.index');
    Route::post('roles', [RoleController::class, 'store'])->name('roles.store');
    Route::put('roles/{roleId}', [RoleController::class, 'update'])->name('roles.update');
    Route::delete('roles/{roleId}', [RoleController::class, 'destroy'])->name('roles.destroy');

    Route::get('modules', [ModuleController::class, 'index'])->name('modules.index');
    Route::post('modules', [ModuleController::class, 'store'])->name('modules.store');
    Route::put('modules/{moduleId}', [ModuleController::class, 'update'])->name('modules.update');
    Route::delete('modules/{moduleId}', [ModuleController::class, 'destroy'])->name('modules.destroy');

    Route::post('permissions', [PermissionController::class, 'store'])->name('permissions.store');
});
