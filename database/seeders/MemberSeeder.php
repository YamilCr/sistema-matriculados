<?php

namespace Database\Seeders;

use App\Models\AccountStatus;
use App\Models\City;
use App\Models\Member;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Matriculados de ejemplo (datos ficticios, sin aleatoriedad:
 * la demo se ve igual cada vez que se resetea la base).
 */
class MemberSeeder extends Seeder
{
    public function run(): void
    {
        $firstNames = ['Juan', 'María', 'Carlos', 'Ana', 'Lucía', 'Martín', 'Sofía', 'Diego', 'Valentina', 'Facundo',
            'Camila', 'Nicolás', 'Florencia', 'Matías', 'Julieta', 'Pablo', 'Agustina', 'Sebastián', 'Carolina', 'Federico'];
        $lastNames = ['Pérez', 'González', 'Rodríguez', 'Martínez', 'Fernández', 'López', 'Díaz', 'Gómez', 'Romero', 'Sosa',
            'Álvarez', 'Torres', 'Ruiz', 'Acosta', 'Benítez', 'Medina', 'Herrera', 'Suárez', 'Molina', 'Castro'];
        $streets = ['San Martín', 'Belgrano', 'Rivadavia', 'Sarmiento', 'Mitre', '9 de Julio', 'Moreno', 'Urquiza'];

        $statusIds = AccountStatus::orderBy('id')->pluck('id'); // Al día, Moroso, Suspendido
        $cities = City::with('province')->orderBy('id')->get();

        for ($i = 0; $i < 36; $i++) {
            $city = $cities[($i * 5) % $cities->count()];
            $first = $firstNames[$i % count($firstNames)];
            $last = $lastNames[($i * 3 + intdiv($i, 4)) % count($lastNames)];

            // ~65% al día, ~25% morosos, ~10% suspendidos
            $statusId = match (true) {
                $i % 10 === 9 => $statusIds[2],
                $i % 4 === 3 => $statusIds[1],
                default => $statusIds[0],
            };

            $member = Member::create([
                'registration_number' => sprintf('MAT-%04d', $i + 1),
                'first_name' => $first,
                'last_name' => $last,
                'dni' => (string) (20000000 + ($i * 1370041) % 25000000),
                'address' => $streets[$i % count($streets)].' '.(100 + ($i * 37) % 900),
                'phone' => '+54 9 297 '.sprintf('%03d-%04d', 400 + $i, 1000 + ($i * 211) % 9000),
                'city_id' => $city->id,
                'province_id' => $city->province_id,
                'account_status_id' => $statusId,
            ]);

            // Fechas de alta escalonadas para que los reportes por fecha tengan sentido
            $member->forceFill(['created_at' => now()->subDays(($i * 9) % 540)->startOfDay()])->saveQuietly();

            // Los primeros 8 tienen usuario para entrar (contraseña inicial = DNI, igual que el alta real)
            if ($i < 8) {
                User::create([
                    'name' => "$first $last",
                    'email' => $i === 0 ? 'juan@matriculado.com' : strtolower(iconv('UTF-8', 'ASCII//TRANSLIT', "$first.$last")).'@ejemplo.com',
                    'password' => $i === 0 ? 'juan123' : $member->dni,
                    'role_id' => 2,
                    'member_id' => $member->id,
                    'is_active' => true,
                ]);
            }
        }
    }
}
