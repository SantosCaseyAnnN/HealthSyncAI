<?php

use App\Livewire\WorkflowAssistant;
use App\Models\User;
use Livewire\Livewire;

it('guides clinic staff to the matching workflow', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(WorkflowAssistant::class)
        ->set('question', 'Where do I review QR check-ins?')
        ->call('ask')
        ->assertSee('Review submitted patient details')
        ->assertSee('href="'.route('check-ins.index').'"', false);
});

it('guides administrators to account management without exposing clinic links', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $this->actingAs($admin);

    Livewire::test(WorkflowAssistant::class)
        ->set('question', 'How do I approve a staff application?')
        ->call('ask')
        ->assertSee('Review applications and manage authorized staff accounts')
        ->assertSee('href="'.route('admin.accounts').'"', false)
        ->assertDontSee(route('patients'), false);
});

it('opens and closes the assistant panel', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(WorkflowAssistant::class)
        ->assertSet('isOpen', false)
        ->assertDontSee('Not sure where to go?')
        ->call('toggle')
        ->assertSet('isOpen', true)
        ->assertSee('Not sure where to go?')
        ->call('close')
        ->assertSet('isOpen', false)
        ->assertDontSee('Not sure where to go?');
});

it('shows the assistant launcher in the patient registration workspace', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('patients'))
        ->assertSee('AI assistant')
        ->assertSee('Register Patient');
});
