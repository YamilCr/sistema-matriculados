<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\Member;
use App\Models\User;

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

            $members = Member::with(['city', 'accountStatus'])
            ->latest()
            ->get()
            ->map(function ($member) {
                return [
                    'id' => $member->id,
                    'enrollment_number' => $member->registration_number,
                    'name' => $member->first_name . ' ' . $member->last_name, // Concatenado
                    'email' => $member->user ? $member->user->email : 'Sin usuario', // Asumiendo relación
                    'phone' => $member->phone,
                    'location' => $member->city->name ?? 'N/A',
                    'status' => $member->accountStatus->name ?? 'inactive',
                    'registration_date' => $member->created_at->format('Y-m-d'),
                    
                    // --- NUEVOS CAMPOS SEGÚN TU MIGRACIÓN ---
                    'dni' => $member->dni,
                    'address' => $member->address,
                    'image' => $member->image,
                    'province' => $member->province->name ?? 'N/A',
                    'status_id' => $member->account_status_id, // Útil para colores
                ];
            });
            return Inertia::render('Admin/Search', [
            'members' => $members
        ]);

        }

}