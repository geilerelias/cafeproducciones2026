<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('security settings page can be rendered', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get('/settings/security');

    $response
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('settings/Security')
            ->where('twoFactorEnabled', false)
            ->where('twoFactorPending', false)
        );
});

test('security page reflects enabled two factor authentication', function () {
    $user = User::factory()->create([
        'two_factor_secret' => encrypt('test-secret-key'),
        'two_factor_confirmed_at' => now(),
        'two_factor_recovery_codes' => encrypt(json_encode(['recovery-code-1'])),
    ]);

    $response = $this
        ->actingAs($user)
        ->get('/settings/security');

    $response
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('twoFactorEnabled', true)
            ->where('twoFactorPending', false)
        );
});
