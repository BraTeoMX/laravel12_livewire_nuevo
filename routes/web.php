<?php

use Illuminate\Support\Facades\Route;
use App\Http\Livewire\InspeccionTela;
use Livewire\Volt\Volt;

Route::get('/', function () {
    if (auth()->check()) {
        return (int) auth()->user()->role_id === 5
            ? redirect()->route('auditor.dashboard')
            : redirect()->route('dashboard');
    }

    return redirect()->route('login');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified', 'prevent.auditor.dashboard'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::view('auditorias', 'auditor.dashboard')
        ->middleware(['verified', 'auditor'])
        ->name('auditor.dashboard');

    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('users', 'users.index')->name('users.index');
    Volt::route('roles', 'catalogo-roles.index')->name('catalogo-roles.index');
    Route::get('inspeccion-tela', InspeccionTela::class)->name('inspeccion.tela');
});

require __DIR__.'/auth.php';
