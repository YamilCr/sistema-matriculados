<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Show the user's profile settings page.
     */
    public function edit(Request $request): Response
    {
        // Cargamos al miembro con sus datos actuales para que aparezcan en el formulario
        $user = $request->user()->load('member');

        return Inertia::render('settings/Profile', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => $request->session()->get('status'),
            'provinces' => \App\Models\Province::all(),
            'cities' => \App\Models\City::all(),
            'member' => $user->member, // Pasamos los datos del miembro (phone, address, etc)
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        
        // 1. Actualizar datos de la tabla USERS (Nombre y Email)
        $user->fill($request->safe()->only(['name', 'email']));

        // Lógica de la imagen de usuario (Sidebar)
        if ($request->hasFile('image')) {
            if ($user->image) {
                \Storage::disk('public')->delete($user->image);
            }
            $user->image = $request->file('image')->store('profiles', 'public');
        }
        $user->save();

        // 2. Actualizar datos de la tabla MEMBERS (Phone, Address, City, Province)
        // Solo si el usuario tiene un registro de matriculado vinculado
        if ($user->member_id) {
            $memberData = $request->safe()->only(['phone', 'address', 'province_id', 'city_id']);
            
            \App\Models\Member::where('id', $user->member_id)
                ->update($memberData);
        }

        return to_route('profile.edit');
    }

    /**
     * Delete the user's profile.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
