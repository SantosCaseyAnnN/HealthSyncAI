<?php

use App\Models\User;

it('renders role-specific clinic dashboard links', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertOk()
        ->assertSee('Good morning, '.$user->first_name)
        ->assertSee('Patient check-in review')
        ->assertSee('AI assistant')
        ->assertSee('Toggle navigation')
        ->assertSee('aria-controls="clinical-navigation"', false)
        ->assertSee('href="'.route('dashboard').'"', false)
        ->assertSee('href="'.route('patients').'"', false)
        ->assertSee('href="'.route('appointments').'"', false)
        ->assertSee('href="'.route('prescriptions').'"', false)
        ->assertSee('href="'.route('check-ins.index').'"', false)
        ->assertDontSee('Laboratory')
        ->assertDontSee('Medical Records');
});

it('renders the mobile navigation toggle on clinic workspace pages', function () {
    $staff = User::factory()->create();

    $this->actingAs($staff)
        ->get(route('patients'))
        ->assertSee('Toggle navigation')
        ->assertSee('aria-controls="clinical-navigation"', false)
        ->assertSee('x-data="{ mobileNavigationOpen: false }"', false);
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

it('gives administrators clinic overview totals without patient queue details', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

    $this->actingAs($admin)
        ->get('/dashboard')
        ->assertOk()
        ->assertSee('Clinic overview')
        ->assertSee('Manage accounts')
        ->assertSee('AI assistant')
        ->assertSee('Patient records and queue details remain restricted')
        ->assertDontSee('Patient check-in review');
});
