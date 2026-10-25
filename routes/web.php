<?php

use App\Http\Controllers\AccountStatusController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\ProvinceController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

// Grupo 1: cualquier usuario logueado (Admin, Staff o Matriculado)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
});

// Grupo 2: acceso compartido (Admin y Staff)
Route::middleware(['auth', 'staff'])->group(function () {
    // El listado de matriculados vive en /admin/search
    Route::get('/members', fn () => redirect()->route('admin.search'))->name('members.index');
    Route::get('/admin/search', [DashboardController::class, 'searchMember'])->name('admin.search');

    // Alta y modificación de matriculados
    Route::post('/members', [MemberController::class, 'store'])->name('members.store');
    Route::put('/members/{member}', [MemberController::class, 'update'])->name('members.update');

    // Reportes
    Route::get('/report', [DashboardController::class, 'report'])->name('report.index');
});

// Grupo 3: solo Admin (lo delicado que el Staff no debe tocar)
Route::middleware(['auth', 'admin'])->group(function () {
    // Baja definitiva de matriculados
    Route::delete('/members/{member}', [MemberController::class, 'destroy'])->name('members.destroy');

    // Usuarios del sistema (admins, staff y matriculados con acceso)
    Route::resource('users', UserController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])
        ->name('users.toggle.status');

    // Tablas maestras
    Route::resource('provinces', ProvinceController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('cities', CityController::class)->only(['index', 'store', 'update', 'destroy']);
    // Estados de cuenta: datos fijos, solo consulta
    Route::get('accountstatuses', [AccountStatusController::class, 'index'])->name('accountstatuses.index');
});

require __DIR__.'/settings.php';
