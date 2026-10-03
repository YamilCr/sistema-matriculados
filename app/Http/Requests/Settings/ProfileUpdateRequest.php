<?php

namespace App\Http\Requests\Settings;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        // Determinamos si el usuario es un miembro (matriculado)
        $isMember = $this->user()->member !== null;

        return [
            // Reglas para TODOS los usuarios
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],

            // Reglas CONDICIONALES para datos profesionales
            // Si es miembro: requerido. Si no: opcional/ignorado.
            'phone' => [$isMember ? 'required' : 'nullable', 'string', 'max:30'],
            
            'address' => [
                $isMember ? 'required' : 'nullable', 
                'string', 
                'max:255'
            ],
            
            'province_id' => [
                $isMember ? 'required' : 'nullable', 
                'exists:provinces,id'
            ],
            
            'city_id' => [
                $isMember ? 'required' : 'nullable', 
                'exists:cities,id'
            ],
        ];
    }

    /**
     * Opcional: Personalizar mensajes de error para que sean más claros
     */
    public function messages(): array
    {
        return [
            'province_id.required' => 'La provincia es obligatoria para los matriculados.',
            'city_id.required' => 'La ciudad es obligatoria para los matriculados.',
            'address.required' => 'El domicilio profesional es necesario para tu credencial.',
        ];
    }
}