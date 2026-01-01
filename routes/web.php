<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return redirect('dashboard');
})->name('home');


Route::get('dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/get-started', function () {
    return Inertia::render('GetStarted');
})->name('get-started');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
require __DIR__.'/batch.php';
require __DIR__.'/employee.php';
require __DIR__.'/project.php';
require __DIR__.'/evaluation.php';
require __DIR__.'/attendance.php';
require __DIR__.'/notifications.php';