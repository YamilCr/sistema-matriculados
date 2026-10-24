<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Modo demo: cada madrugada la base vuelve a su estado inicial
// (requiere el scheduler: `* * * * * php artisan schedule:run`).
if (config('app.demo')) {
    Schedule::command('migrate:fresh --seed --force')->dailyAt('04:00');
}
