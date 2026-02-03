<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LocationController; //supuestamente por que location controller no existe y me tira error ahi.
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


/// Grupo 2: ACCESO COMPARTIDO (Admin y Staff)
// Usamos el middleware 'staff' que acabamos de modificar para que acepte a ambos (IDs 1 y 3)
Route::middleware(['auth', 'staff'])->group(function () {
    
    // Gestión básica que el Staff puede hacer
    Route::get('/members/create', [MemberController::class, 'create'])->name('members.create');
    Route::post('/members', [MemberController::class, 'store'])->name('members.store');
    Route::put('/members/{member}', [MemberController::class, 'update'])->name('members.update');

    
    // Búsqueda (Unificamos la ruta, sirve para ambos)
    Route::get('/admin/search', [DashboardController::class, 'searchMember'])->name('admin.search');
    
    // Reportes (Si el staff puede ver reportes)
    Route::get('/report', [DashboardController::class, 'report'])->name('report.index');
});

// Grupo 3: SOLO ADMIN (Cosas delicadas que el Staff NO debe tocar)
Route::middleware(['auth', 'admin'])->group(function () {
    // Gestión de usuarios del sistema (Crear otros admins o staff)
    Route::resource('users', UserController::class);
    Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])
    ->name('users.toggle.status');

    // Tablas maestras (Para que el staff no rompa la configuración)
    Route::resource('locations', LocationController::class);
    Route::resource('accountstatuses', AccountStatusController::class);
});

require __DIR__.'/settings.php';
