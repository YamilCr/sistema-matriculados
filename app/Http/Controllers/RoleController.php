<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RoleController extends Controller
{
    public function index()
    {
        return Inertia::render('Roles/Index', [
            'roles' => Role::all()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:roles',
            'description' => 'nullable|string',
        ]);

        Role::create($validated);
        return redirect()->back();
    }
}