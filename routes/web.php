<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\AccountStatusController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');


// Grupo 1: Cualquier usuario logueado (Admin o Matriculado)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', DashboardController::class)
        ->name('dashboard');

    Route::get('/members', [MemberController::class, 'index'])
        ->name('members.index');
});


// Grupo 2: SOLO SUPER USUARIO (Usa el alias 'admin')
Route::middleware(['auth', 'admin'])->group(function () {
    // Gestión de usuarios y roles [cite: 6, 7]
    Route::resource('users', UserController::class);
    
    // Gestión de tablas maestras [cite: 8, 9]
    Route::resource('locations', LocationController::class);
    Route::resource('accountstatuses', AccountStatusController::class);
    
    // Crear o editar matriculados
    Route::get('/members/create', [MemberController::class, 'create'])->name('members.create');
    Route::post('/members', [MemberController::class, 'store'])->name('members.store');
    
    // Reportes
    Route::get('/report', [DashboardController::class, 'report'])->name('report.index');
   
    // Buscar matriculados
    Route::get('/admin/search', [DashboardController::class, 'searchMember'])->name('admin.search');    

});

require __DIR__.'/settings.php';
