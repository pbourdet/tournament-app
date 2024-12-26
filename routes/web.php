<?php

declare(strict_types=1);

use App\Http\Controllers\Tournaments\TournamentInvitationController;
use App\Livewire;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect('/dashboard') : redirect('/login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', Livewire\Dashboard::class)->name('dashboard');

    Route::post('/tournaments/{tournament}/invitations/store', [TournamentInvitationController::class, 'store'])->name('tournament-invitations.store');

    Route::get('/tournaments/create', Livewire\Tournament\Create::class)->name('tournaments.create');
    Route::get('/tournaments/{tournament}/{page?}', Livewire\Tournament\Show::class)->name('tournaments.show');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', Livewire\Profile\Edit::class)->name('profile.edit');
});

require __DIR__.'/auth.php';
