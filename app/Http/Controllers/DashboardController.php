<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\Member;
use App\Models\User;
use App\Models\Province;
use App\Models\City;
use App\Models\AccountStatus;

class DashboardController extends Controller
{
   public function __invoke()
{
    $user = auth()->user();

    if ($user->role_id === 1 || $user->role_id === 3) {
        return Inertia::render('Dashboard', [
            'role' => $user->role_id === 1 ? 'admin' : 'staff',
            'stats' => [
                'total_members' => 1,
                'active_users' => 1,
                'morosos' => 3,
            ],
        ]);
    }

    if ($user->role_id === 2) {
        return Inertia::render('Dashboard', [
            'role' => 'member',
            'myMemberData' => \App\Models\Member::with(['province', 'accountStatus'])
                ->findOrFail($user->member_id),
        ]);
    }
    

    abort(403);
}
// Método para la vista de reportes (solo para admin)
        public function report()
        {
            return Inertia::render('report/Index');
        }
// Método para la búsqueda de matriculados (solo para admin)
        public function searchMember()
        {
            $user = auth()->user();

            // 1. Agregamos 'province' al with para que no de error al buscar el nombre
            $members = Member::with(['city', 'province', 'accountStatus']) 
                ->latest()
                ->get()
                ->map(function ($member) {
                    return [
                        'id' => $member->id,
                        'enrollment_number' => $member->registration_number,
                        'name' => $member->first_name . ' ' . $member->last_name,
                        'first_name' => $member->first_name, // <--- Útil para el edit
                        'last_name' => $member->last_name,   // <--- Útil para el edit
                        'email' => $member->user ? $member->user->email : 'Sin usuario',
                        'phone' => $member->phone,
                        
                        // Datos visuales (Texto)
                        'city' => $member->city->name ?? 'N/A',
                        'province' => $member->province->name ?? 'N/A', 
                        'status' => $member->accountStatus->name ?? 'inactive',
                        'registration_date' => $member->created_at->format('Y-m-d'),
                        'dni' => $member->dni,
                        'address' => $member->address,
                        'image' => $member->image,
                        'is_active' => $member->is_active, // Para el botón toggle

                        // 👇👇👇 AQUÍ ESTABA EL PROBLEMA: FALTABAN LOS IDs PARA EL MODAL 👇👇👇
                        'province_id' => $member->province_id,          // El modal busca esto
                        'city_id' => $member->city_id,                  // El modal busca esto
                        'account_status_id' => $member->account_status_id, // El modal busca esto
                    ];
                });

            return Inertia::render('Admin/Search', [   
                'members' => $members, 
                'provinces' => Province::select('id', 'name')->get(),
                'cities' => City::select('id', 'name', 'province_id')->get(),
                'accountStatuses' => \App\Models\AccountStatus::select('id', 'name')->get(),
            ]);
        }

}