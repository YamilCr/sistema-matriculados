<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class UserController extends Controller
{
 public function index()
    {
        $users = User::with(['role', 'member'])
            ->latest()
            ->get();

        $roles = Role::all();
        $members = Member::select('id','image' ,'first_name','last_name', 'registration_number')->get();

        return Inertia::render('User/Index', [
            'users' => $users,
            'roles' => $roles,
            'members' => $members
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'role_id' => 'required|exists:roles,id',
            'member_id' => 'nullable|exists:members,id',
             // Booleano (asegúrate de enviarlo como 0/1 o true/false desde el front)
            'is_active'=> ['boolean'], // 'sometimes' o 'required' según tu lógica
             // Imagen
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);
        // 2. Lógica de la Imagen (sin cambios, estaba bien)
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('profiles', 'public');
            $validated['image'] = $path;
        }
        $validated['password'] = Hash::make($validated['password']);
        
        User::create($validated);

        return redirect()->back()->with('message', 'Usuario de acceso creado.');
    }


    public function update(Request $request, User $user)
    {
        // 1. Validamos usando los nombres EXACTOS de la base de datos
        $validated = $request->validate([
            'name' => 'required|string',
            'is_active' => 'required|boolean',
            'role_id' => 'required|exists:roles,id',
        ]);
        // 2. Lógica de la Imagen (sin cambios, estaba bien)
        if ($request->hasFile('image')) {
            if ($user->image) {
                \Storage::disk('public')->delete($user->image);
            }
            $user->image = $request->file('image')->store('profiles', 'public');
        }
        $user->save();

        // 3. Actualizar
        $user->update($validated);

        // Redirigir con mensaje de éxito
        return redirect()->back()->with('message', 'Usuario actualizado.');
    }

        public function toggleStatus(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);
        
        return redirect()->back()->with('success', 'Estado del usuario actualizado');
    }

    public function destroy(User $user)
    {
        $user->delete();
        
        return redirect()->back()->with('success', 'Usuario eliminado exitosamente');
    }
}
