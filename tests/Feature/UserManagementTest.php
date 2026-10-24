<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    seedCatalogs();
    $this->admin = userWithRole(1);
    $this->actingAs($this->admin);
});

test('admin can create a staff user', function () {
    $this->post(route('users.store'), [
        'name' => 'Nuevo Staff',
        'email' => 'nuevo@sistema.com',
        'password' => 'secreto123',
        'password_confirmation' => 'secreto123',
        'role_id' => 3,
        'is_active' => true,
    ])->assertSessionHasNoErrors();

    $user = User::where('email', 'nuevo@sistema.com')->firstOrFail();
    expect(Hash::check('secreto123', $user->password))->toBeTrue();
});

test('password confirmation is required', function () {
    $this->post(route('users.store'), [
        'name' => 'X', 'email' => 'x@sistema.com', 'password' => 'secreto123',
        'password_confirmation' => 'otra-cosa', 'role_id' => 3,
    ])->assertSessionHasErrors('password');
});

test('a member can be linked to only one user', function () {
    $member = makeMember();
    userWithRole(2, ['member_id' => $member->id]);

    $this->post(route('users.store'), [
        'name' => 'Duplicado', 'email' => 'dup@sistema.com', 'password' => 'secreto123',
        'password_confirmation' => 'secreto123', 'role_id' => 2, 'member_id' => $member->id,
    ])->assertSessionHasErrors('member_id');
});

test('update keeps the password when none is sent and changes it when sent', function () {
    $user = userWithRole(3);
    $oldHash = $user->password;

    $this->put(route('users.update', $user), [
        'name' => 'Renombrado', 'email' => $user->email, 'role_id' => 3, 'is_active' => true,
    ])->assertSessionHasNoErrors();
    expect($user->fresh()->name)->toBe('Renombrado')
        ->and($user->fresh()->password)->toBe($oldHash);

    $this->put(route('users.update', $user), [
        'name' => 'Renombrado', 'email' => $user->email, 'role_id' => 3, 'is_active' => true,
        'password' => 'nueva-clave-1', 'password_confirmation' => 'nueva-clave-1',
    ])->assertSessionHasNoErrors();
    expect(Hash::check('nueva-clave-1', $user->fresh()->password))->toBeTrue();
});

test('toggle status flips is_active', function () {
    $user = userWithRole(3);

    $this->patch(route('users.toggle.status', $user));
    expect($user->fresh()->is_active)->toBeFalse();

    $this->patch(route('users.toggle.status', $user));
    expect($user->fresh()->is_active)->toBeTrue();
});

test('an admin cannot lock themselves out', function () {
    $this->patch(route('users.toggle.status', $this->admin))->assertSessionHas('error');
    $this->delete(route('users.destroy', $this->admin))->assertSessionHas('error');
    $this->put(route('users.update', $this->admin), [
        'name' => 'Admin', 'email' => $this->admin->email, 'role_id' => 3, 'is_active' => true,
    ])->assertSessionHas('error');

    expect($this->admin->fresh()->is_active)->toBeTrue()
        ->and($this->admin->fresh()->role_id)->toBe(1)
        ->and(User::find($this->admin->id))->not->toBeNull();
});

test('admin can delete another user', function () {
    $user = userWithRole(3);

    $this->delete(route('users.destroy', $user));

    expect(User::find($user->id))->toBeNull();
});
