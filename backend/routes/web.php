<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Teams\TeamInvitationController;
use App\Http\Middleware\EnsureTeamMembership;
use Illuminate\Support\Facades\Route;

// Specimen Repository Admin Dashboard on root
Route::get('/', [AdminDashboardController::class, 'index'])->name('home');

// Specimen Repository Admin Dashboard
Route::get('/admin/{tab?}', [AdminDashboardController::class, 'index'])
    ->where('tab', 'overview|specimens|loans|taxa|collections|locations|researchers|audit-logs')
    ->name('admin.dashboard');

Route::prefix('{current_team}')
    ->middleware(['auth', 'verified', EnsureTeamMembership::class])
    ->group(function () {
        Route::get('dashboard', DashboardController::class)->name('dashboard');
    });

Route::middleware(['auth'])->group(function () {
    Route::post('invitations/{invitation}/accept', [TeamInvitationController::class, 'accept'])->name('invitations.accept');
    Route::delete('invitations/{invitation}', [TeamInvitationController::class, 'decline'])->name('invitations.decline');
});

require __DIR__.'/settings.php';
