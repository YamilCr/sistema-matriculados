<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Location::create(['name' => 'Capital', 'province' => 'Buenos Aires']);
        \App\Models\Location::create(['name' => 'Córdoba', 'province' => 'Córdoba']);
        \App\Models\Location::create(['name' => 'Rosario', 'province' => 'Santa Fe']);
    }
}
