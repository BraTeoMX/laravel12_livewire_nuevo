<?php

use Illuminate\Support\Facades\Route;
use App\Http\Livewire\InspeccionTela;
use Livewire\Volt\Volt;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
})->name('home');

Route::get('dashboard', function () {
    $user = auth()->user();
    
    $redirects = [
        1 => 'dashboard.administrador',
        2 => 'dashboard.gerente',
        3 => 'dashboard.gestion',
        4 => 'dashboard.consulta',
        5 => 'dashboard.auditor',
    ];
    
    $route = $redirects[$user->role_id] ?? 'dashboard.consulta';
    
    return redirect()->route($route);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    // Dashboards por rol
    Route::view('dashboard/administrador', 'dashboard.roles.administrador')->name('dashboard.administrador');
    Route::view('dashboard/gerente', 'dashboard.roles.gerente')->name('dashboard.gerente');
    Route::view('dashboard/gestion', 'dashboard.roles.gestion')->name('dashboard.gestion');
    Route::view('dashboard/consulta', 'dashboard.roles.consulta')->name('dashboard.consulta');
    Route::view('dashboard/auditor', 'dashboard.roles.auditor')->name('dashboard.auditor');

    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('users', 'users.index')->name('users.index');
    Volt::route('roles', 'catalogo-roles.index')->name('catalogo-roles.index');
    Route::get('inspeccion-tela', InspeccionTela::class)->name('inspeccion.tela');
});

require __DIR__.'/auth.php';
