<?php

namespace App\Http\Controllers;

use App\Models\CheckIn;
use App\Models\Patient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PatientCheckInController extends Controller
{
    public function show(Patient $patient): View
    {
        return view('check-ins.public', ['patient' => $patient]);
    }

    public function store(Request $request, Patient $patient): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'date_of_birth' => ['nullable', 'date', 'before_or_equal:today'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'reason_for_visit' => ['nullable', 'string', 'max:2000'],
        ]);

        CheckIn::create([
            ...$validated,
            'patient_id' => $patient->id,
            'status' => 'pending',
            'check_in_date' => now()->toDateString(),
        ]);

        return back()->with('status', 'Your check-in request was sent to the clinic for review. It is not yet a queue registration.');
    }
}
