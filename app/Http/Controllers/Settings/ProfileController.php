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
        return Inertia::render('settings/Profile', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => $request->session()->get('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    // app/Http/Controllers/Settings/ProfileController.php

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        
        // Obtenemos todos los datos validados EXCEPTO la imagen primero
        $user->fill($request->safe()->except(['image']));

        if ($request->hasFile('image')) {
            // Si ya tenía una foto personal, la borramos para no acumular basura
            if ($user->image) {
                \Storage::disk('public')->delete($user->image);
            }
            
            // Guardamos y asignamos la nueva ruta
            $path = $request->file('image')->store('profiles', 'public');
            $user->image = $path;
        }

        $user->save();

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
