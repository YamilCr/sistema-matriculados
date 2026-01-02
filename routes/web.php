<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LocationController;


Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');


// Grupo 1: Cualquier usuario logueado (Admin o Matriculado)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {

    $user = auth()->user();

    // Si es ADMIN (Role ID 1)
    if ($user->role_id === 1) {
        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'total_members' =>1,// \App\Models\Member::count(),
                'active_users' => 1,//\App\Models\User::where('is_active', true)->count(),
                'morosos' => 3,//\App\Models\Member::where('account_status_id', 2)->count(),
            ]
        ]);
    }

    // Si es MATRICULADO (Role ID 2)
    if ($user->role_id === 2) {
        return Inertia::render('Member/Dashboard', [
            'myMemberData' => \App\Models\Member::with(['location', 'accountStatus'])
                                ->find($user->member_id)
        ]);
    }
    })->name('dashboard');

    // Ver lista de matriculados
    Route::get('/members', [MemberController::class, 'index'])->name('members.index');
});

// Grupo 2: SOLO SUPER USUARIO (Usa el alias 'admin')
Route::middleware(['auth', 'admin'])->group(function () {
    // Gestión de usuarios y roles [cite: 6, 7]
    Route::resource('users', UserController::class);
    
    // Gestión de tablas maestras [cite: 8, 9]
    Route::resource('locations', LocationController::class);
    Route::resource('account-statuses', AccountStatusController::class);
    
    // Crear o editar matriculados
    Route::get('/members/create', [MemberController::class, 'create'])->name('members.create');
    Route::post('/members', [MemberController::class, 'store'])->name('members.store');
});

require __DIR__.'/settings.php';
