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
    Route::post('roles/{roleId}/restore', [RoleController::class, 'restore'])->name('roles.restore');

    Route::get('modules', [ModuleController::class, 'index'])->name('modules.index');
    Route::post('modules', [ModuleController::class, 'store'])->name('modules.store');
    Route::put('modules/{moduleId}', [ModuleController::class, 'update'])->name('modules.update');
    Route::delete('modules/{moduleId}', [ModuleController::class, 'destroy'])->name('modules.destroy');
    Route::post('modules/{moduleId}/restore', [ModuleController::class, 'restore'])->name('modules.restore');

    Route::get('permissions', [PermissionController::class, 'index'])->name('permissions.index');
    Route::post('permissions', [PermissionController::class, 'store'])->name('permissions.store');
    Route::put('permissions/{permissionId}', [PermissionController::class, 'update'])->name('permissions.update');
    Route::delete('permissions/{permissionId}', [PermissionController::class, 'destroy'])->name('permissions.destroy');
    Route::post('permissions/{permissionId}/restore', [PermissionController::class, 'restore'])->name('permissions.restore');
});
