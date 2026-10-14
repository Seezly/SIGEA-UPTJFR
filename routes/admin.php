<?php

use App\Http\Controllers\Admin\AreaController;
use App\Http\Controllers\Admin\JobCategoryController;
use App\Http\Controllers\Admin\ModuleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SkillController;
use Illuminate\Support\Facades\Route;

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
    Route::get('skills', [SkillController::class, 'index'])->name('skills.index');
    Route::post('skills', [SkillController::class, 'store'])->name('skills.store');
    Route::put('skills/{skillId}', [SkillController::class, 'update'])->name('skills.update');
    Route::delete('skills/{skillId}', [SkillController::class, 'destroy'])->name('skills.destroy');
    Route::post('skills/{skillId}/restore', [SkillController::class, 'restore'])->name('skills.restore');

    Route::get('areas', [AreaController::class, 'index'])->name('areas.index');
    Route::post('areas', [AreaController::class, 'store'])->name('areas.store');
    Route::put('areas/{areaId}', [AreaController::class, 'update'])->name('areas.update');
    Route::delete('areas/{areaId}', [AreaController::class, 'destroy'])->name('areas.destroy');
    Route::post('areas/{areaId}/restore', [AreaController::class, 'restore'])->name('areas.restore');

    Route::post('job-categories', [JobCategoryController::class, 'store'])->name('job-categories.store');
    Route::put('job-categories/{jobCategoryId}', [JobCategoryController::class, 'update'])->name('job-categories.update');
    Route::delete('job-categories/{jobCategoryId}', [JobCategoryController::class, 'destroy'])->name('job-categories.destroy');

    Route::post('permissions', [PermissionController::class, 'store'])->name('permissions.store');
    Route::put('permissions/{permissionId}', [PermissionController::class, 'update'])->name('permissions.update');
    Route::delete('permissions/{permissionId}', [PermissionController::class, 'destroy'])->name('permissions.destroy');
    Route::post('permissions/{permissionId}/restore', [PermissionController::class, 'restore'])->name('permissions.restore');
});
