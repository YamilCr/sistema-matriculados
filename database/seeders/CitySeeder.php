<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Province;
use App\Models\City;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        // Buscamos el ID de Chubut para que sea dinámico
        $chubut = Province::where('name', 'Chubut')->first();

        if ($chubut) {
            $cities = [
                'Comodoro Rivadavia',
                'Trelew',
                'Puerto Madryn',
                'Rawson',
                'Esquel',
                'Sarmiento',
                'Rada Tilly',
                'Gaiman'
            ];

            foreach ($cities as $city) {
                City::create([
                    'name' => $city,
                    'province_id' => $chubut->id
                ]);
            }
        }
        
        // Ejemplo para otra provincia si quisieras
        $bsas = Province::where('name', 'Buenos Aires')->first();
        if ($bsas) {
            City::create(['name' => 'La Plata', 'province_id' => $bsas->id]);
            City::create(['name' => 'Mar del Plata', 'province_id' => $bsas->id]);
        }
    }
}