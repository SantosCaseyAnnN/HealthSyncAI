<?php

use Laravel\Fortify\Features;
use Laravel\Jetstream\Jetstream;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
})->skip(function () {
    return ! Features::enabled(Features::registration());
}, 'Registration support is not enabled.');

test('registration screen cannot be rendered if support is disabled', function () {
    $response = $this->get('/register');

    $response->assertStatus(404);
})->skip(function () {
    return Features::enabled(Features::registration());
}, 'Registration support is enabled.');

test('new users can register', function () {
    $response = $this->post('/register', [
        'first_name' => 'Test',
        'middle_name' => '',
        'last_name' => 'User',
        'email' => 'test@example.com',
        'role' => 'clinic_staff',
        'password' => 'password',
        'password_confirmation' => 'password',
        'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature(),
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
    $this->assertDatabaseHas('users', [
        'email' => 'test@example.com',
        'role' => 'clinic_staff',
        'approval_status' => 'pending',
        'is_active' => false,
    ]);
})->skip(function () {
    return ! Features::enabled(Features::registration());
}, 'Registration support is not enabled.');

test('student applications require a school ID and current year level', function () {
    $this->post('/register', [
        'first_name' => 'Jamie',
        'last_name' => 'Rivera',
        'email' => 'jamie@example.com',
        'role' => 'student',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertSessionHasErrors(['school_id', 'year_level']);

    $this->assertDatabaseMissing('users', ['email' => 'jamie@example.com']);
})->skip(function () {
    return ! Features::enabled(Features::registration());
}, 'Registration support is not enabled.');
