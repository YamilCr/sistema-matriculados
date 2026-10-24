<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

pest()->extend(Tests\TestCase::class)
 // ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function something()
{
    // ..
}

/*
|--------------------------------------------------------------------------
| Helpers de la demo
|--------------------------------------------------------------------------
*/

/** Carga roles, estados, provincias y ciudades (los IDs de rol son 1=Admin, 2=Member, 3=Staff). */
function seedCatalogs(): void
{
    test()->seed([
        Database\Seeders\RoleSeeder::class,
        Database\Seeders\AccountStatusSeeder::class,
        Database\Seeders\ProvinceSeeder::class,
        Database\Seeders\CitySeeder::class,
    ]);
}

function userWithRole(int $roleId, array $attributes = []): App\Models\User
{
    return App\Models\User::factory()->withoutTwoFactor()->create($attributes + ['role_id' => $roleId, 'is_active' => true]);
}

function makeMember(array $attributes = []): App\Models\Member
{
    $city = App\Models\City::first();

    return App\Models\Member::create($attributes + [
        'registration_number' => 'MAT-'.fake()->unique()->numerify('####'),
        'first_name' => 'Ana',
        'last_name' => 'Prueba',
        'dni' => (string) fake()->unique()->numberBetween(10000000, 99999999),
        'address' => 'Calle Falsa 123',
        'city_id' => $city->id,
        'province_id' => $city->province_id,
        'account_status_id' => App\Models\AccountStatus::first()->id,
    ]);
}
