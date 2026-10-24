<?php

use App\Models\City;
use App\Models\Member;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(fn () => seedCatalogs());

function memberPayload(array $overrides = []): array
{
    $city = City::first();

    return $overrides + [
        'registration_number' => 'MAT-9001',
        'first_name' => 'Lucía',
        'last_name' => 'Gómez',
        'dni' => '30123456',
        'address' => 'Mitre 450',
        'phone' => '297-555-0000',
        'city_id' => $city->id,
        'province_id' => $city->province_id,
        'account_status_id' => 1,
        'email' => 'lucia@ejemplo.com',
    ];
}

test('staff can create a member and a login user is created with the DNI as password', function () {
    $this->actingAs(userWithRole(3))
        ->post(route('members.store'), memberPayload())
        ->assertSessionHasNoErrors();

    $member = Member::where('registration_number', 'MAT-9001')->firstOrFail();
    $user = User::where('email', 'lucia@ejemplo.com')->firstOrFail();

    expect($user->member_id)->toBe($member->id)
        ->and($user->role_id)->toBe(2)
        ->and(Hash::check('30123456', $user->password))->toBeTrue();
});

test('a member cannot be created with a city from another province', function () {
    $city = City::first();
    $otherProvince = App\Models\Province::where('id', '!=', $city->province_id)->first();

    $this->actingAs(userWithRole(1))
        ->post(route('members.store'), memberPayload(['province_id' => $otherProvince->id]))
        ->assertStatus(422);

    expect(Member::count())->toBe(0);
});

test('registration number, DNI and email must be unique', function () {
    $admin = userWithRole(1);
    $this->actingAs($admin)->post(route('members.store'), memberPayload());

    $this->actingAs($admin)
        ->post(route('members.store'), memberPayload())
        ->assertSessionHasErrors(['registration_number', 'dni', 'email']);

    expect(Member::count())->toBe(1);
});

test('required fields are validated', function () {
    $this->actingAs(userWithRole(1))
        ->post(route('members.store'), [])
        ->assertSessionHasErrors(['registration_number', 'first_name', 'last_name', 'dni', 'address', 'city_id', 'province_id', 'account_status_id', 'email']);
});

test('a profile picture is stored', function () {
    Storage::fake('public');

    $this->actingAs(userWithRole(1))
        ->post(route('members.store'), memberPayload(['image' => UploadedFile::fake()->image('foto.jpg')]))
        ->assertSessionHasNoErrors();

    Storage::disk('public')->assertExists(Member::first()->image);
});

test('staff can update a member and the linked user follows the name', function () {
    $this->actingAs(userWithRole(1))->post(route('members.store'), memberPayload());
    $member = Member::firstOrFail();

    $this->actingAs(userWithRole(3))
        ->put(route('members.update', $member), memberPayload([
            'first_name' => 'Lucía María',
            'account_status_id' => 2,
        ]))
        ->assertSessionHasNoErrors();

    expect($member->fresh()->first_name)->toBe('Lucía María')
        ->and($member->fresh()->account_status_id)->toBe(2)
        ->and($member->user->fresh()->name)->toBe('Lucía María Gómez');
});

test('only admin can delete a member, and their user goes with them', function () {
    $this->actingAs(userWithRole(1))->post(route('members.store'), memberPayload());
    $member = Member::firstOrFail();

    $this->actingAs(userWithRole(3))
        ->delete(route('members.destroy', $member))
        ->assertRedirect(route('dashboard'));
    expect(Member::count())->toBe(1);

    $this->actingAs(userWithRole(1))->delete(route('members.destroy', $member));
    expect(Member::count())->toBe(0)
        ->and(User::where('email', 'lucia@ejemplo.com')->exists())->toBeFalse();
});
