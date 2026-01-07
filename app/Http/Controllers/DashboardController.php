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

    if ($user->role_id === 1) {
        return Inertia::render('Dashboard', [
            'role' => 'admin',
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
            return Inertia::render('admin/Search');
        }
}