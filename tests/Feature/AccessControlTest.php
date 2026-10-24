<?php

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(fn () => seedCatalogs());

dataset('admin-only-pages', ['users.index', 'provinces.index', 'cities.index', 'accountstatuses.index']);

test('admin can open every management page', function (string $routeName) {
    $this->actingAs(userWithRole(1))->get(route($routeName))->assertOk();
})->with('admin-only-pages');

test('staff cannot open admin-only pages', function (string $routeName) {
    $this->actingAs(userWithRole(3))
        ->get(route($routeName))
        ->assertRedirect(route('dashboard'));
})->with('admin-only-pages');

test('members cannot open admin-only pages', function (string $routeName) {
    $this->actingAs(userWithRole(2))
        ->get(route($routeName))
        ->assertRedirect(route('dashboard'));
})->with('admin-only-pages');

test('staff can open members and reports', function () {
    $staff = userWithRole(3);

    $this->actingAs($staff)->get(route('admin.search'))->assertOk();
    $this->actingAs($staff)->get(route('report.index'))->assertOk();
});

test('a plain member cannot open members or reports', function () {
    $member = userWithRole(2);

    $this->actingAs($member)->get(route('admin.search'))->assertRedirect(route('dashboard'));
    $this->actingAs($member)->get(route('report.index'))->assertRedirect(route('dashboard'));
});

test('deactivated users cannot log in', function () {
    $user = userWithRole(1, ['is_active' => false]);

    $this->post(route('login.store'), ['email' => $user->email, 'password' => 'password']);

    $this->assertGuest();
});

test('active users can log in', function () {
    $user = userWithRole(1);

    $this->post(route('login.store'), ['email' => $user->email, 'password' => 'password']);

    $this->assertAuthenticatedAs($user);
});
