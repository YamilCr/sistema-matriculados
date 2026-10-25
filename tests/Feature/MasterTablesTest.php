<?php

use App\Models\AccountStatus;
use Illuminate\Support\Facades\Route;
use App\Models\City;
use App\Models\Province;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    seedCatalogs();
    $this->actingAs(userWithRole(1));
});

test('provinces: create, update and delete', function () {
    $this->post(route('provinces.store'), ['name' => 'Provincia Nueva'])->assertSessionHasNoErrors();
    $province = Province::where('name', 'Provincia Nueva')->firstOrFail();

    $this->put(route('provinces.update', $province), ['name' => 'Provincia Renombrada'])->assertSessionHasNoErrors();
    expect($province->fresh()->name)->toBe('Provincia Renombrada');

    $this->delete(route('provinces.destroy', $province));
    expect(Province::find($province->id))->toBeNull();
});

test('province names must be unique', function () {
    $this->post(route('provinces.store'), ['name' => 'Chubut'])->assertSessionHasErrors('name');
});

test('a province with members cannot be deleted', function () {
    $member = makeMember();

    $this->delete(route('provinces.destroy', $member->province_id))
        ->assertSessionHas('error');

    expect(Province::find($member->province_id))->not->toBeNull();
});

test('cities: create under a province, same name allowed in another province', function () {
    $chubut = Province::where('name', 'Chubut')->first();
    $cordoba = Province::where('name', 'Córdoba')->first();

    $this->post(route('cities.store'), ['name' => 'Ciudad Nueva', 'province_id' => $chubut->id])
        ->assertSessionHasNoErrors();
    $this->post(route('cities.store'), ['name' => 'Ciudad Nueva', 'province_id' => $chubut->id])
        ->assertSessionHasErrors('name');
    $this->post(route('cities.store'), ['name' => 'Ciudad Nueva', 'province_id' => $cordoba->id])
        ->assertSessionHasNoErrors();
});

test('a city with members cannot be deleted', function () {
    $member = makeMember();

    $this->delete(route('cities.destroy', $member->city_id))->assertSessionHas('error');

    expect(City::find($member->city_id))->not->toBeNull();
});

test('account statuses are fixed: only the three system states exist', function () {
    expect(AccountStatus::orderBy('id')->pluck('name')->all())->toBe(['Al día', 'Moroso', 'Suspendido']);

    $this->get(route('accountstatuses.index'))->assertOk();
});

test('account statuses cannot be created, edited or deleted from the app', function () {
    expect(Route::has('accountstatuses.store'))->toBeFalse()
        ->and(Route::has('accountstatuses.update'))->toBeFalse()
        ->and(Route::has('accountstatuses.destroy'))->toBeFalse();

    $this->post('/accountstatuses', ['name' => 'Jubilado'])->assertStatus(405);
    expect(AccountStatus::count())->toBe(3);
});
