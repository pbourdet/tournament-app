<?php

declare(strict_types=1);

use App\Http\Controllers\Account\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TournamentController;
use App\Http\Controllers\Tournaments\TournamentInvitationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect('/dashboard') : redirect('/login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::post('/tournaments/{tournament}/invitation/create', [TournamentInvitationController::class, 'store'])->name('tournament_invitation.create');
    Route::get('/tournaments/{code}/join', [TournamentController::class, 'show'])->name('tournament.invitation');
    Route::post('/tournaments/{tournament}/join', [TournamentController::class, 'join'])->name('tournament.join');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
