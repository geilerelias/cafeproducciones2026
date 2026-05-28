<?php

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'identification_type' => 'cc',
        'identification_number' => '1234567890',
        'phone' => '3001234567',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('verification.notice', absolute: false));

    $user = auth()->user();
    expect($user)->not->toBeNull();
    expect($user->identification_type)->toBe('cc');
    expect($user->identification_number)->toBe('1234567890');
    expect($user->phone)->toBe('3001234567');
    expect($user->hasVerifiedEmail())->toBeFalse();
});