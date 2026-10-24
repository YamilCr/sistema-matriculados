<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Cuentas internas de la demo (Admin y Staff).
 * Los usuarios de los matriculados los crea MemberSeeder.
 */
class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@sistema.com',
            'password' => 'admin123', // el modelo lo hashea (cast "hashed")
            'role_id' => 1,
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Personal Administrativo',
            'email' => 'staff@sistema.com',
            'password' => 'staff123',
            'role_id' => 3,
            'is_active' => true,
        ]);
    }
}
