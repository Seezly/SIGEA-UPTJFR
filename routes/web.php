<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Static\LandingPageController;
use App\Http\Controllers\Static\AboutUsPageController;
use Inertia\Inertia;

Route::get('/', LandingPageController::class)->name('home');
Route::get('/nosotros', AboutUsPageController::class)->name('about-us');

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
require __DIR__ . '/admin.php';
