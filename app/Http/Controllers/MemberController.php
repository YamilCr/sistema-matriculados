<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Member;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class MemberController extends Controller
{
    /**
     * Crea un matriculado y su usuario de acceso (contraseña inicial = DNI).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'registration_number' => ['required', 'string', 'max:50', 'unique:members,registration_number'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'dni' => ['required', 'string', 'max:20', 'unique:members,dni'],
            'address' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'city_id' => ['required', 'exists:cities,id'],
            'province_id' => ['required', 'exists:provinces,id'],
            'account_status_id' => ['required', 'exists:account_statuses,id'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'email' => ['required', 'email', 'unique:users,email'],
        ]);

        $this->ensureCityBelongsToProvince($validated['city_id'], $validated['province_id']);

        DB::transaction(function () use ($request, $validated) {
            $imagePath = $request->hasFile('image')
                ? $request->file('image')->store('members', 'public')
                : null;

            $member = Member::create(
                collect($validated)->except(['email', 'image'])->all() + ['image' => $imagePath]
            );

            User::create([
                'name' => $member->first_name.' '.$member->last_name,
                'email' => $validated['email'],
                'password' => $member->dni, // el modelo lo hashea (cast "hashed")
                'image' => $imagePath,
                'role_id' => 2,
                'member_id' => $member->id,
                'is_active' => true,
            ]);
        });

        return redirect()->back()->with('message', 'Matriculado creado. Su contraseña inicial es su DNI.');
    }

    public function update(Request $request, Member $member)
    {
        $validated = $request->validate([
            'registration_number' => ['required', 'string', 'max:50', Rule::unique('members')->ignore($member->id)],
            'dni' => ['required', 'string', 'max:20', Rule::unique('members')->ignore($member->id)],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'city_id' => ['required', 'exists:cities,id'],
            'province_id' => ['required', 'exists:provinces,id'],
            'account_status_id' => ['required', 'exists:account_statuses,id'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        $this->ensureCityBelongsToProvince($validated['city_id'], $validated['province_id']);

        unset($validated['image']);

        if ($request->hasFile('image')) {
            if ($member->image) {
                Storage::disk('public')->delete($member->image);
            }
            $validated['image'] = $request->file('image')->store('members', 'public');
        }

        $member->update($validated);

        if ($member->user) {
            $member->user->update([
                'name' => $member->first_name.' '.$member->last_name,
                'image' => $member->image,
            ]);
        }

        return redirect()->back()->with('message', 'Datos actualizados correctamente.');
    }

    /**
     * Baja definitiva del matriculado junto con su usuario de acceso.
     */
    public function destroy(Member $member)
    {
        DB::transaction(function () use ($member) {
            $member->user?->delete();
            if ($member->image) {
                Storage::disk('public')->delete($member->image);
            }
            $member->delete();
        });

        return redirect()->back()->with('message', 'Matriculado eliminado.');
    }

    private function ensureCityBelongsToProvince(int|string $cityId, int|string $provinceId): void
    {
        if (! City::where('id', $cityId)->where('province_id', $provinceId)->exists()) {
            abort(422, 'La ciudad no pertenece a la provincia seleccionada.');
        }
    }
}
