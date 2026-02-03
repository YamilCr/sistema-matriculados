<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Member;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Usuario Administrador
        User::create([
            'name' => 'admin',
            'email' => 'admin@sistema.com',
            'password' => Hash::make('admin123'),
            'role_id' => 1, // Asumiendo que 1 es Admin
            'is_active' => true,
        ]);

        // 2. Usuario Matriculado
        $member = Member::create([
            'registration_number' => 'REG-001',
            'first_name' => 'Juan',
            'last_name' => 'Pérez',
            'dni' => '12345678',
            'address' => 'Calle Principal 123',
            'phone' => '555-1234',
            'city_id'=> 1,
            'province_id' => 1, // Asumiendo que existe una ubicación con ID 1
            'account_status_id' => 1, // Asumiendo que existe un estado con ID 1
        ]);

        User::create([
            'name' => 'juan_perez',
            'email' => 'juan@matriculado.com',
            'password' => Hash::make('juan123'),
            'role_id' => 2, // Asumiendo que 2 es Matriculado
            'member_id' => $member->id, // Usar el ID del miembro creado
            'is_active' => true,
        ]);

        // 3. Otro Usuario (por ejemplo, Administrativo)
        User::create([
            'name' => 'mauro_user',
            'email' => 'arjonasmauro@gmail.com',
            'password' => Hash::make('password123'),
            'role_id' => 3, 
            'is_active' => true,
        ]);
    }
}