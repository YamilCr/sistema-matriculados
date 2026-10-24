<?php

namespace App\Http\Controllers;

use App\Models\AccountStatus;
use App\Models\City;
use App\Models\Member;
use App\Models\Province;
use App\Models\User;
use Inertia\Inertia;

class DashboardController extends Controller
{
    private const ROLE_ADMIN = 1;

    private const ROLE_MEMBER = 2;

    private const ROLE_STAFF = 3;

    public function __invoke()
    {
        $user = auth()->user();

        if (in_array($user->role_id, [self::ROLE_ADMIN, self::ROLE_STAFF], true)) {
            $morosoId = AccountStatus::where('name', 'Moroso')->value('id');

            return Inertia::render('Dashboard', [
                'role' => $user->role_id === self::ROLE_ADMIN ? 'admin' : 'staff',
                'stats' => [
                    'total_members' => Member::count(),
                    'active_users' => User::where('is_active', true)->count(),
                    'morosos' => $morosoId ? Member::where('account_status_id', $morosoId)->count() : 0,
                ],
            ]);
        }

        if ($user->role_id === self::ROLE_MEMBER && $user->member_id) {
            return Inertia::render('Dashboard', [
                'role' => 'member',
                'myMemberData' => Member::with(['city', 'province', 'accountStatus'])
                    ->findOrFail($user->member_id),
            ]);
        }

        abort(403);
    }

    /** Listado de matriculados con sus ABM (Admin y Staff). */
    public function searchMember()
    {
        return Inertia::render('admin/Search', [
            'members' => $this->membersPayload(),
            'provinces' => Province::sortedByName(Province::select('id', 'name')->get()),
            'cities' => City::sortedByName(City::select('id', 'name', 'province_id')->get()),
            'accountStatuses' => AccountStatus::select('id', 'name')->orderBy('id')->get(),
            'canDelete' => auth()->user()->role_id === self::ROLE_ADMIN,
        ]);
    }

    /** Reportes calculados con datos reales (en el cliente, sobre este listado). */
    public function report()
    {
        return Inertia::render('report/Index', [
            'members' => $this->membersPayload(),
            'statuses' => AccountStatus::select('id', 'name')->orderBy('id')->get(),
            'provinces' => Province::sortedByName(Province::select('id', 'name')->get()),
            'cities' => City::sortedByName(City::select('id', 'name', 'province_id')->get()),
        ]);
    }

    private function membersPayload()
    {
        return Member::with(['city', 'province', 'accountStatus', 'user'])
            ->latest()
            ->get()
            ->map(fn (Member $m) => [
                'id' => $m->id,
                'enrollment_number' => $m->registration_number,
                'name' => $m->first_name.' '.$m->last_name,
                'first_name' => $m->first_name,
                'last_name' => $m->last_name,
                'email' => $m->user?->email ?? 'Sin usuario',
                'phone' => $m->phone,
                'dni' => $m->dni,
                'address' => $m->address,
                'image' => $m->image,
                'city' => $m->city?->name ?? 'N/A',
                'province' => $m->province?->name ?? 'N/A',
                'status' => $m->accountStatus?->name ?? 'Sin estado',
                'registration_date' => $m->created_at->format('Y-m-d'),
                'province_id' => $m->province_id,
                'city_id' => $m->city_id,
                'account_status_id' => $m->account_status_id,
            ]);
    }
}
