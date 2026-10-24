<?php

namespace App\Http\Controllers;

use App\Models\Province;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ProvinceController extends Controller
{
    public function index()
    {
        return Inertia::render('Provinces/Index', [
            'provinces' => Province::sortedByName(Province::withCount(['cities', 'members'])->get()),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:provinces,name'],
        ]);

        Province::create($validated);

        return redirect()->back()->with('message', 'Provincia creada.');
    }

    public function update(Request $request, Province $province)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('provinces', 'name')->ignore($province->id)],
        ]);

        $province->update($validated);

        return redirect()->back()->with('message', 'Provincia actualizada.');
    }

    public function destroy(Province $province)
    {
        if ($province->members()->exists()) {
            return redirect()->back()->with('error', 'No se puede eliminar: hay matriculados en esta provincia.');
        }

        $province->delete(); // sus ciudades se eliminan en cascada

        return redirect()->back()->with('message', 'Provincia eliminada.');
    }
}
