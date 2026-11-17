<?php

use App\Models\User;

it('renders role-specific clinic dashboard links', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertOk()
        ->assertSee('Hello, '.$user->first_name)
        ->assertSee('Today at your clinic')
        ->assertSee('href="'.route('dashboard').'"', false)
        ->assertSee('href="'.route('patients').'"', false)
        ->assertSee('href="'.route('appointments').'"', false)
        ->assertSee('href="'.route('medical-records').'"', false)
        ->assertSee('href="'.route('laboratory').'"', false)
        ->assertSee('href="'.route('prescriptions').'"', false)
        ->assertSee('href="'.route('check-ins.index').'"', false);
});

it('shows a pending application message after registration', function () {
    $this->post('/register', [
        'first_name' => 'Casey',
        'last_name' => 'Santos',
        'email' => 'casey@example.com',
        'role' => 'clinic_staff',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
    ])->assertRedirect(route('dashboard', absolute: false));

    $this->get('/dashboard')->assertSee('awaiting review');
});
