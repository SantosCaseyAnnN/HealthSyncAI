<?php

use App\Models\User;

it('renders the clinic dashboard and floating smart assist panel', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertOk()
        ->assertSee('Good evening, Admin')
        ->assertSee('Smart Assist');
});

it('redirects users back to login with a sign-up success message', function () {
    $this->from('/register')
        ->post('/register', [
            'name' => 'Casey Santos',
            'user_id' => 'USER001',
            'email' => 'casey@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'terms' => 'on',
        ])
        ->assertRedirect('/login')
        ->assertSessionHas('status', 'Sign up complete!');
});
