<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Province;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class CityController extends Controller
{
    public function index()
    {
        return Inertia::render('Cities/Index', [
            'cities' => City::sortedByName(City::with('province:id,name')->withCount('members')->get()),
            'provinces' => Province::sortedByName(Province::select('id', 'name')->get()),
        ]);
    }

    public function store(Request $request)
    {
        City::create($this->validated($request));

        return redirect()->back()->with('message', 'Ciudad creada.');
    }

    public function update(Request $request, City $city)
    {
        $city->update($this->validated($request, $city));

        return redirect()->back()->with('message', 'Ciudad actualizada.');
    }

    public function destroy(City $city)
    {
        if ($city->members()->exists()) {
            return redirect()->back()->with('error', 'No se puede eliminar: hay matriculados en esta ciudad.');
        }

        $city->delete();

        return redirect()->back()->with('message', 'Ciudad eliminada.');
    }

    private function validated(Request $request, ?City $city = null): array
    {
        return $request->validate([
            'province_id' => ['required', 'exists:provinces,id'],
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('cities', 'name')
                    ->where('province_id', $request->input('province_id'))
                    ->ignore($city?->id),
            ],
        ]);
    }
}
