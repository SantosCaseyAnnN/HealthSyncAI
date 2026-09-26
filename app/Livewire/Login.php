<?php

namespace App\Livewire;

use Livewire\Component;

class Login extends Component
{
    public $email = '';
    public $user_id = '';
    public $password = '';

    public function login()
    {
        // Authentication can be added here later.

        // Temporary redirect for testing:
        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('livewire.login');
    }
}