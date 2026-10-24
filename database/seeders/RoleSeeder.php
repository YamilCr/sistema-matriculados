<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Role::create(['name' => 'Administrador', 'description' => 'Acceso completo al sistema']); // ID 1
        \App\Models\Role::create(['name' => 'Matriculado', 'description' => 'Ve su credencial y sus datos']);   // ID 2
        \App\Models\Role::create(['name' => 'Personal', 'description' => 'Gestiona matriculados y reportes']);   // ID 3
    }
}
