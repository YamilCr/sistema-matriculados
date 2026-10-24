<?php

use Inertia\Testing\AssertableInertia as Assert;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(fn () => seedCatalogs());

test('guests are redirected to the login page', function () {
    $this->get(route('dashboard'))->assertRedirect(route('login'));
});

test('admin sees the panel with real stats', function () {
    makeMember(['account_status_id' => App\Models\AccountStatus::where('name', 'Moroso')->value('id')]);
    makeMember();

    $this->actingAs(userWithRole(1))
        ->get(route('dashboard'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
            ->where('role', 'admin')
            ->where('stats.total_members', 2)
            ->where('stats.morosos', 1));
});

test('staff gets the staff dashboard', function () {
    $this->actingAs(userWithRole(3))
        ->get(route('dashboard'))
        ->assertInertia(fn (Assert $page) => $page->where('role', 'staff'));
});

test('a member sees their own record', function () {
    $member = makeMember();
    $user = userWithRole(2, ['member_id' => $member->id]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertInertia(fn (Assert $page) => $page
            ->where('role', 'member')
            ->where('myMemberData.id', $member->id));
});

test('a user without a role is forbidden', function () {
    $this->actingAs(App\Models\User::factory()->create())
        ->get(route('dashboard'))
        ->assertForbidden();
});
