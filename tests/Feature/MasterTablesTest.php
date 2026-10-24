<?php

use App\Models\AccountStatus;
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

test('account statuses: create, update and delete', function () {
    $this->post(route('accountstatuses.store'), ['name' => 'Jubilado', 'description' => 'Matrícula pasiva'])
        ->assertSessionHasNoErrors();
    $status = AccountStatus::where('name', 'Jubilado')->firstOrFail();

    $this->put(route('accountstatuses.update', $status), ['name' => 'Retirado'])->assertSessionHasNoErrors();
    expect($status->fresh()->name)->toBe('Retirado');

    $this->delete(route('accountstatuses.destroy', $status));
    expect(AccountStatus::find($status->id))->toBeNull();
});

test('an account status in use cannot be deleted', function () {
    $member = makeMember();

    $this->delete(route('accountstatuses.destroy', $member->account_status_id))->assertSessionHas('error');

    expect(AccountStatus::find($member->account_status_id))->not->toBeNull();
});
