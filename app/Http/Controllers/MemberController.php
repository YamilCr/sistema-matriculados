<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Location;
use App\Models\AccountStatus;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Validation\Rule; 
use Illuminate\Support\Facades\Storage;
use App\Models\User; // <--- Agregar
use Illuminate\Support\Facades\Hash; // <--- Agregar para la contraseña
use Illuminate\Support\Facades\DB;   // <--- Agregar para la transacción

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
        // 1. Validamos datos del Matriculado Y del Usuario (Email)
        $validated = $request->validate([
            'registration_number' => 'required|string|unique:members,registration_number',
            'first_name'          => 'required|string|max:255',
            'last_name'           => 'required|string|max:255',
            'dni'                 => 'required|string|unique:members,dni',
            'address'             => 'required|string|max:255',
            'phone'               => 'nullable|string|max:50',
            'city_id'             => 'required|exists:cities,id',
            'province_id'         => 'required|exists:provinces,id',
            'account_status_id'   => 'required|exists:account_statuses,id',
            'image'               => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'is_active'           => 'boolean',
            
            // Validamos el email para la tabla USERS
            'email'               => 'required|email|unique:users,email', 
        ]);

        // Usamos una transacción: Si falla crear el usuario, se borra el matriculado automáticamente
        DB::transaction(function () use ($request, $validated) {
            
            // A. Manejo de Imagen
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('members', 'public');
            }

            // B. Crear Matriculado (Member)
            // Quitamos el email del array porque 'email' no va en la tabla members (según tu migración)
            $memberData = collect($validated)->except(['email', 'image'])->toArray();
            $memberData['image'] = $imagePath; // Añadimos la ruta de la imagen

            $member = Member::create($memberData);

            // C. Crear Usuario (User) vinculado
            User::create([
                'name'      => $member->first_name . ' ' . $member->last_name, // Nombre completo
                'email'     => $request->email,
                'password'  => Hash::make($member->dni), // Contraseña por defecto = DNI
                'image'     => $imagePath, // Usamos la misma foto si quieres
                'role_id'   => 2, // <--- 2 = Matriculado (Ajusta según tu tabla roles)
                'member_id' => $member->id,
                'is_active' => true,
            ]);
        });

        return redirect()->back()->with('message', 'Matriculado y Usuario creados exitosamente.');
    }
        //
        

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


    public function update(Request $request, Member $member)
    {
        // 1. Validamos usando los nombres EXACTOS de la base de datos
        $validated = $request->validate([
            // Validaciones únicas ignorando el ID actual
            'registration_number' => ['required', 'string', Rule::unique('members')->ignore($member->id)],
            'dni'                 => ['required', 'string', Rule::unique('members')->ignore($member->id)],
            
            // Campos de texto simples
            'first_name'          => ['required', 'string', 'max:255'],
            'last_name'           => ['required', 'string', 'max:255'],
            'address'             => ['required', 'string', 'max:255'],
            'phone'               => ['nullable', 'string', 'max:20'],
            
            // ⚠️ CORRECCIÓN: Usamos city_id y province_id para coincidir con tu migración
            'city_id'             => ['required', 'exists:cities,id'], 
            'province_id'         => ['required', 'exists:provinces,id'],
            
            'account_status_id'   => ['required', 'exists:account_statuses,id'],
            
            // Booleano (asegúrate de enviarlo como 0/1 o true/false desde el front)
            'is_active'           => ['boolean'], // 'sometimes' o 'required' según tu lógica
            
            // Imagen
            'image'               => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        // 2. Lógica de la Imagen (sin cambios, estaba bien)
        if ($request->hasFile('image')) {
            if ($member->image) {
                \Storage::disk('public')->delete($member->image);
            }
            $member->image = $request->file('image')->store('profiles', 'public');
        }
        $member->save();

        // 3. Actualizar
        // Esto funcionará porque ahora las claves de $validated (city_id, etc.) 
        // coinciden con las columnas de tu tabla 'members'.
        $member->update($validated);

        return redirect()->back()->with('message', 'Datos actualizados correctamente.');
    }

 
    public function destroy(Member $member)
    {
        // OPCIÓN 1: Desactivar (Recomendado según tu migración is_active)
        $member->update(['is_active' => false]); 
        $mensaje = 'Matriculado desactivado exitosamente.';

        // OPCIÓN 2: Borrar físicamente (Si prefieres que desaparezca de la BD, descomenta la siguiente línea y comenta la anterior)
        // $member->delete(); 
        // $mensaje = 'Matriculado eliminado permanentemente.';

        return redirect()->back()->with('message', $mensaje);
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