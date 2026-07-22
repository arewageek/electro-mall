<?php

use App\Models\Role;
use App\Models\User;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authorized roles can visit the dashboard', function () {
    Role::firstOrCreate(['name' => 'clerk']);
    $user = User::factory()->create();
    $user->assignRole('clerk');
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
});

test('pickers are redirected from dashboard to picking operations', function () {
    Role::firstOrCreate(['name' => 'picker']);
    $user = User::factory()->create();
    $user->assignRole('picker');
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('operations.picking'));
});

test('receiving staff are redirected from dashboard to receiving operations', function () {
    Role::firstOrCreate(['name' => 'receiving']);
    $user = User::factory()->create();
    $user->assignRole('receiving');
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('operations.receiving'));
});

test('unauthorized roles are redirected to profile', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('profile.edit'));
});
