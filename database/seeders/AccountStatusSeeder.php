<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccountStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\AccountStatus::create(['name' => 'Al día', 'description' => 'Sin deuda']);
        \App\Models\AccountStatus::create(['name' => 'Moroso', 'description' => 'Deuda mayor a 3 meses']);
        \App\Models\AccountStatus::create(['name' => 'Suspendido', 'description' => 'Inhabilitado por sanción']);
    }
}
