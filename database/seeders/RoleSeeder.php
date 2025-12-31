<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Role::create(['name' => 'Admin', 'description' => 'Acceso completo al sistema']); // ID 1
        \App\Models\Role::create(['name' => 'Member', 'description' => 'Usuario matriculado']);   // ID 2
        \App\Models\Role::create(['name' => 'Staff', 'description' => 'Personal del sistema']);   // ID 3
    }
}
