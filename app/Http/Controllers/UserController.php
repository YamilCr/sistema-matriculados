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
        return Inertia::render('Users/Index', [
            'users' => User::with(['role', 'member'])->get(),
            'roles' => Role::all(),
            'members' => Member::whereDoesntHave('user')->get() // Miembros sin usuario aún
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
        ]);

        $validated['password'] = Hash::make($validated['password']);
        
        User::create($validated);
        return redirect()->back()->with('message', 'Usuario de acceso creado.');
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'is_active' => 'required|boolean',
            'role_id' => 'required|exists:roles,id',
        ]);

        $user->update($validated);
        return redirect()->back()->with('message', 'Usuario actualizado.');
    }
}