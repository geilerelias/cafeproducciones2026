<?php

use App\Models\User;

test('users with two factor enabled are redirected to the challenge screen', function () {
    $user = User::factory()->create([
        'two_factor_secret' => encrypt('test-secret-key'),
        'two_factor_confirmed_at' => now(),
    ]);

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertGuest();
    $response->assertRedirect(route('two-factor.login'));
    expect(session('login.id'))->toBe($user->id);
});

test('users without two factor can still login normally', function () {
    $user = User::factory()->create();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticatedAs($user);
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('two factor challenge screen can be rendered', function () {
    $user = User::factory()->create();

    $response = $this
        ->withSession(['login.id' => $user->id])
        ->get('/two-factor-challenge');

    $response->assertOk();
});
