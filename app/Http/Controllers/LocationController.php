<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LocationController extends Controller
{
    public function index()
    {
        return Inertia::render('Locations/Index', [
            'locations' => Location::all()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'province' => 'required|string',
        ]);

        Location::create($validated);
        return redirect()->back()->with('message', 'Localidad creada.');
    }

    public function update(Request $request, Location $location)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'province' => 'required|string',
        ]);

        $location->update($validated);
        return redirect()->back()->with('message', 'Localidad actualizada.');
    }

    public function destroy(Location $location)
    {
        // Solo permitir eliminar si no hay miembros asociados
        if ($location->members()->count() > 0) {
            return redirect()->back()->with('error', 'No se puede eliminar: hay matriculados en esta localidad.');
        }
        $location->delete();
        return redirect()->back()->with('message', 'Localidad eliminada.');
    }
}