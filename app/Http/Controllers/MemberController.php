<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Location;
use App\Models\AccountStatus;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MemberController extends Controller
{
    /**
     * Muestra la lista de matriculados.
     */
    public function index()
    {
        // Cargamos las relaciones 'location' y 'accountStatus' definidas en los modelos 
        $members = Member::with(['location', 'accountStatus'])->get();

        return Inertia::render('Members/Index', [
            'members' => $members
        ]);
    }

    /**
     * Muestra el formulario para crear uno nuevo.
     */
    public function create()
    {
        // Necesitamos enviar las opciones para los selects de la interfaz Vue
        return Inertia::render('Members/Create', [
            'locations' => Location::all(),
            'accountStatuses' => AccountStatus::all()
        ]);
    }

    /**
     * Guarda el nuevo matriculado en la base de datos.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'registration_number' => 'required|string|unique:members,registration_number',
            'first_name'          => 'required|string', 
            'last_name'           => 'required|string', 
            'dni'                 => 'required|string|unique:members,dni', 
            'address'             => 'required|string', 
            'phone'               => 'nullable|string', 
            'location_id'         => 'required|exists:locations,id', 
            'account_status_id'   => 'required|exists:account_statuses,id',
            'image'               => 'nullable|image|mimes:jpg,jpeg,png|max:2048', 
        ]);

        if ($request->hasFile('image')) {
            // Guarda en storage/app/public/members
            $validated['image'] = $request->file('image')->store('members', 'public');
        }

        Member::create($validated);

        return redirect()->route('members.index')->with('message', 'Matriculado creado.');
    }

    /**
     * Muestra la ficha de un matriculado específico.
     */
    public function show(Member $member)
    {
        // Cargamos las relaciones para ver el detalle completo
        return Inertia::render('Members/Show', [
            'member' => $member->load(['location', 'accountStatus'])
        ]);
    }

    /**
     * Muestra el formulario de edición.
     */
    public function edit(Member $member)
    {
        return Inertia::render('Members/Edit', [
            'member'          => $member,
            'locations'       => Location::all(),
            'accountStatuses' => AccountStatus::all()
        ]);
    }

    /**
     * Actualiza los datos en la base de datos.
     */
    public function update(Request $request, Member $member)
    {
        $validated = $request->validate([
            'registration_number' => 'required|string|unique:members,registration_number,' . $member->id,
            'first_name'          => 'required|string',
            'last_name'           => 'required|string',
            'dni'                 => 'required|string|unique:members,dni,' . $member->id,
            'address'             => 'required|string',
            'phone'               => 'nullable|string',
            'location_id'         => 'required|exists:locations,id',
            'account_status_id'   => 'required|exists:account_statuses,id',
            'is_active'           => 'required|boolean',
            'image'               => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // Opcional: Eliminar imagen anterior del disco si existe
            if ($member->image) {
                \Storage::disk('public')->delete($member->image);
            }
            $validated['image'] = $request->file('image')->store('members', 'public');
        }

        $member->update($validated);

        return redirect()->route('members.index')->with('message', 'Datos actualizados.');
    }

    /**
     * Elimina (o desactiva) un registro.
     */
    public function destroy(Member $member)
    {
        // En sistemas de gestión es mejor desactivar que borrar físicamente
        $member->update(['is_active' => false]); 

        return redirect()->route('members.index')->with('message', 'Matriculado desactivado.');
    }

    /**
     * Busca matriculados por nombre, apellido o DNI.
     */
    public function search(Request $request)
    {
        $query = $request->input('query');  
        $members = Member::where('first_name', 'like', "%{$query}%")
            ->orWhere('last_name', 'like', "%{$query}%")
            ->orWhere('dni', 'like', "%{$query}%")
            ->with(['location', 'accountStatus'])
            ->get();
        return Inertia::render('members/Search', [
            'members' => $members,
            'searchQuery' => $query
        ]);
    }
}