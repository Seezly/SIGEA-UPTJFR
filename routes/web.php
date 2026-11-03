<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Static\LandingPageController;
use App\Http\Controllers\Static\AboutUsPageController;
use App\Http\Controllers\Static\TermsPageController;
use App\Http\Controllers\Static\PrivacyPoliciesPageController;
use Inertia\Inertia;

Route::get('/', LandingPageController::class)->name('home');
Route::get('/nosotros', AboutUsPageController::class)->name('about-us');
Route::get('/terminos-del-servicio', TermsPageController::class)->name('terms');
Route::get('/politicas-de-privacidad', PrivacyPoliciesPageController::class)->name('privacy-policies');

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
require __DIR__ . '/admin.php';
