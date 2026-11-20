<?php

namespace App\Http\Controllers;

use App\Models\CheckIn;
use App\Models\Patient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PatientCheckInController extends Controller
{
    public function showPublic(): View
    {
        return view('check-ins.public', ['patient' => null]);
    }

    public function show(Patient $patient): View
    {
        return view('check-ins.public', ['patient' => $patient]);
    }

    public function storePublic(Request $request): RedirectResponse
    {
        return $this->submit($request);
    }

    public function store(Request $request, Patient $patient): RedirectResponse
    {
        return $this->submit($request, $patient);
    }

    private function submit(Request $request, ?Patient $patient = null): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'date_of_birth' => ['nullable', 'date', 'before_or_equal:today'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'existing_patient_number' => ['nullable', 'string', 'max:30'],
            'consent_given' => ['accepted'],
            'reason_for_visit' => ['nullable', 'string', 'max:2000'],
            'gender' => ['nullable', 'string', 'in:Female,Male,Other'],
            'height_cm' => ['nullable', 'numeric', 'min:30', 'max:250'],
            'weight_kg' => ['nullable', 'numeric', 'min:1', 'max:500'],
            'blood_pressure' => ['nullable', 'string', 'max:20'],
            'temperature_c' => ['nullable', 'numeric', 'min:25', 'max:45'],
            'pulse_rate' => ['nullable', 'integer', 'min:20', 'max:300'],
            'respiratory_rate' => ['nullable', 'integer', 'min:4', 'max:80'],
            'oxygen_saturation' => ['nullable', 'numeric', 'min:50', 'max:100'],
        ]);

        CheckIn::create([
            ...$validated,
            'existing_patient_number' => ($validated['existing_patient_number'] ?? null)
                ? strtoupper(trim($validated['existing_patient_number']))
                : null,
            'patient_id' => $patient?->id,
            'status' => 'pending',
            'check_in_date' => now()->toDateString(),
        ]);

        return back()->with('status', 'Your check-in request was sent to the clinic for review. It is not yet a queue registration.');
    }
}
