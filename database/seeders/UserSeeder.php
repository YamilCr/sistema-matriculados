<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Usuario Administrador
        User::create([
            'name' => 'admin_sistema',
            'email' => 'admin@sistema.com',
            'password' => Hash::make('admin123'),
            'role_id' => 1, // Asumiendo que 1 es Admin
            'is_active' => true,
        ]);

        // 2. Usuario Matriculado
        User::create([
            'name' => 'juan_perez',
            'email' => 'juan@matriculado.com',
            'password' => Hash::make('juan123'),
            'role_id' => 2, // Asumiendo que 2 es Matriculado
            //'member_id' => 1, // ID del matriculado en la tabla matriculados
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