<?php
namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Province;
use Illuminate\Database\Seeder;

class ProvinceSeeder extends Seeder
{
    public function run(): void
    {
        $provinces = [
            'Buenos Aires', 'Catamarca', 'Chaco', 'Chubut', 'Córdoba', 'Corrientes', 
            'Entre Ríos', 'Formosa', 'Jujuy', 'La Pampa', 'La Rioja', 'Mendoza', 
            'Misiones', 'Neuquén', 'Río Negro', 'Salta', 'San Juan', 'San Luis', 
            'Santa Cruz', 'Santa Fe', 'Santiago del Estero', 'Tierra del Fuego', 'Tucumán'
        ];

        foreach ($provinces as $name) {
            Province::create(['name' => $name]);
        }
    }
}
