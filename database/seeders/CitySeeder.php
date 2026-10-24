<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Province;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $citiesByProvince = [
            'Chubut' => ['Comodoro Rivadavia', 'Trelew', 'Puerto Madryn', 'Rawson', 'Esquel', 'Sarmiento', 'Rada Tilly', 'Gaiman'],
            'Buenos Aires' => ['La Plata', 'Mar del Plata', 'Bahía Blanca', 'Tandil'],
            'Córdoba' => ['Córdoba', 'Río Cuarto', 'Villa Carlos Paz'],
            'Santa Fe' => ['Rosario', 'Santa Fe', 'Rafaela'],
            'Mendoza' => ['Mendoza', 'San Rafael'],
            'Neuquén' => ['Neuquén', 'San Martín de los Andes'],
            'Río Negro' => ['Bariloche', 'Viedma', 'General Roca'],
            'Santa Cruz' => ['Río Gallegos', 'Caleta Olivia'],
        ];

        foreach ($citiesByProvince as $provinceName => $cities) {
            $province = Province::where('name', $provinceName)->first();

            foreach ($cities as $name) {
                City::firstOrCreate(['name' => $name, 'province_id' => $province->id]);
            }
        }
    }
}
