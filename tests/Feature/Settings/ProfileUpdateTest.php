<?php

use App\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('profile page is displayed', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get(route('profile.edit'));

    $response->assertOk();
});

test('profile information can be updated', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from(route('profile.edit'))
        ->patch(route('profile.update'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('profile.edit'));

    $user->refresh();

    expect($user->name)->toBe('Test User');
    expect($user->email)->toBe('test@example.com');
});

test('email verification status is unchanged when the email address is unchanged', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from(route('profile.edit'))
        ->patch(route('profile.update'), [
            'name' => 'Test User',
            'email' => $user->email,
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('profile.edit'));

    expect($user->refresh()->email_verified_at)->not->toBeNull();
});

test('user can delete their account', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->delete(route('profile.destroy'), [
            'password' => 'password',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('home'));

    $this->assertGuest();
    expect($user->fresh())->toBeNull();
});

test('correct password must be provided to delete account', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from(route('profile.edit'))
        ->delete(route('profile.destroy'), [
            'password' => 'wrong-password',
        ]);

    $response
        ->assertSessionHasErrors('password')
        ->assertRedirect(route('profile.edit'));

    expect($user->fresh())->not->toBeNull();
});

// ---- Matriculado: puede cambiar su contacto, pero no sus datos validados por el colegio

function memberUser(): array
{
    seedCatalogs();
    $member = makeMember(['first_name' => 'Ana', 'last_name' => 'Ruiz', 'dni' => '28111222']);
    $user = userWithRole(2, ['member_id' => $member->id, 'name' => 'Ana Ruiz', 'email' => 'ana@ejemplo.com']);

    return [$user, $member];
}

test('a member can update phone, address and city from "Mis datos"', function () {
    [$user, $member] = memberUser();
    $other = App\Models\City::where('province_id', '!=', $member->province_id)->first();

    $this->actingAs($user)->patch(route('profile.update'), [
        'name' => 'Nombre Falso',
        'email' => 'ana@ejemplo.com',
        'phone' => '297-555-9999',
        'address' => 'Belgrano 123',
        'province_id' => $other->province_id,
        'city_id' => $other->id,
    ])->assertSessionHasNoErrors();

    $member->refresh();
    expect($member->phone)->toBe('297-555-9999')
        ->and($member->address)->toBe('Belgrano 123')
        ->and($member->city_id)->toBe($other->id)
        ->and($member->dni)->toBe('28111222')
        ->and($user->fresh()->name)->toBe('Ana Ruiz'); // el nombre no se puede cambiar
});

test('a member cannot choose a city that does not belong to the province', function () {
    [$user, $member] = memberUser();
    $other = App\Models\City::where('province_id', '!=', $member->province_id)->first();

    $this->actingAs($user)->patch(route('profile.update'), [
        'name' => 'Ana Ruiz',
        'email' => 'ana@ejemplo.com',
        'phone' => '297-555-9999',
        'address' => 'Belgrano 123',
        'province_id' => $member->province_id,
        'city_id' => $other->id,
    ])->assertSessionHasErrors('city_id');
});

test('a member cannot delete their own account', function () {
    [$user] = memberUser();

    $this->actingAs($user)->delete(route('profile.destroy'), ['password' => 'password'])->assertForbidden();

    expect(App\Models\User::find($user->id))->not->toBeNull();
});

test('the member dashboard explains the account status', function () {
    [$user] = memberUser();

    $this->actingAs($user)->get(route('dashboard'))
        ->assertInertia(fn ($page) => $page
            ->where('role', 'member')
            ->where('myMemberData.account_status.name', 'Al día')
            ->has('myMemberData.account_status.description'));
});
