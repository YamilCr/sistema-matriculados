<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {
        return Inertia::render('User/Index', [
            'users' => User::with(['role', 'member'])->latest()->get(),
            'roles' => Role::all(),
            'members' => Member::select('id', 'image', 'first_name', 'last_name', 'registration_number')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role_id' => ['required', 'exists:roles,id'],
            'member_id' => ['nullable', 'exists:members,id', 'unique:users,member_id'],
            'is_active' => ['boolean'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        unset($validated['image']);
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('profiles', 'public');
        }

        User::create($validated); // el modelo hashea la contraseña

        return redirect()->back()->with('message', 'Usuario de acceso creado.');
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'is_active' => ['required', 'boolean'],
            'role_id' => ['required', 'exists:roles,id'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        // Un admin no puede quitarse a sí mismo el acceso ni el rol de Admin.
        if ($user->id === $request->user()->id && ((int) $validated['role_id'] !== 1 || ! $validated['is_active'])) {
            return redirect()->back()->with('error', 'No podés quitarte tu propio rol de Admin ni desactivarte.');
        }

        unset($validated['image']);
        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        if ($request->hasFile('image')) {
            if ($user->image) {
                Storage::disk('public')->delete($user->image);
            }
            $validated['image'] = $request->file('image')->store('profiles', 'public');
        }

        $user->update($validated);

        return redirect()->back()->with('message', 'Usuario actualizado.');
    }

    public function toggleStatus(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return redirect()->back()->with('error', 'No podés desactivar tu propia cuenta.');
        }

        $user->update(['is_active' => ! $user->is_active]);

        return redirect()->back()->with('message', 'Estado del usuario actualizado.');
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return redirect()->back()->with('error', 'No podés eliminar tu propia cuenta.');
        }

        $user->delete();

        return redirect()->back()->with('message', 'Usuario eliminado.');
    }
}
