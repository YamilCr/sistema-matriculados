<?php

namespace Database\Seeders;

use App\Models\AccountStatus;
use Illuminate\Database\Seeder;

class AccountStatusSeeder extends Seeder
{
    /** Carga (o repara) los estados de cuenta fijos del sistema. */
    public function run(): void
    {
        foreach (AccountStatus::FIXED as $name => $description) {
            AccountStatus::updateOrCreate(['name' => $name], ['description' => $description]);
        }
    }
}
