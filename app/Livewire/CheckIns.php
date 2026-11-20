<?php

namespace App\Livewire;

use App\Livewire\Concerns\AuthorizesClinicStaff;
use App\Models\CheckIn as PatientCheckIn;
use App\Models\Patient;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class CheckIns extends Component
{
    use AuthorizesClinicStaff;
    use WithPagination;

    public string $statusFilter = 'pending';

    public function callNextPatient(): void
    {
        $checkIn = PatientCheckIn::query()
            ->where('status', 'registered')
            ->whereDate('check_in_date', today())
            ->orderBy('queue_number')
            ->firstOrFail();

        $checkIn->forceFill([
            'status' => 'in_consultation',
            'reviewed_by_user_id' => auth()->id(),
            'reviewed_at' => now(),
        ])->save();

        session()->flash('status', "Queue #{$checkIn->queue_number} has been called.");
    }

    public function completePatient(int $checkInId): void
    {
        $checkIn = PatientCheckIn::query()
            ->where('status', 'in_consultation')
            ->findOrFail($checkInId);

        $checkIn->forceFill([
            'status' => 'completed',
            'reviewed_by_user_id' => auth()->id(),
            'reviewed_at' => now(),
        ])->save();

        session()->flash('status', "Queue #{$checkIn->queue_number} has been completed.");
    }

    public function registerInQueue(int $checkInId): void
    {
        $this->reviewAndRegister($checkInId);
    }

    public function registerAsNewPatient(int $checkInId): void
    {
        $this->reviewAndRegister($checkInId, true);
    }

    private function reviewAndRegister(int $checkInId, bool $createNewPatient = false): void
    {
        $checkIn = PatientCheckIn::query()
            ->where('status', 'pending')
            ->findOrFail($checkInId);
        abort_unless($checkIn->consent_given, 422);
        abort_if($createNewPatient && $checkIn->patient_id !== null, 422);

        $matchedPatient = null;
        if (! $createNewPatient && $checkIn->patient === null && $checkIn->existing_patient_number !== null) {
            $matchedPatient = Patient::query()
                ->where('patient_number', $checkIn->existing_patient_number)
                ->first();

            if ($matchedPatient === null) {
                session()->flash('status', 'No patient record matches the number submitted. Verify the number or create a new patient record before registering this request.');

                return;
            }
        }

        DB::transaction(function () use ($checkIn, $matchedPatient, $createNewPatient): void {
            $patient = $checkIn->patient;

            if (! $createNewPatient && $patient === null && $matchedPatient !== null) {
                $patient = $matchedPatient;
                $checkIn->patient()->associate($patient);
            }

            if ($patient === null) {
                $patient = Patient::create([
                    'patient_number' => 'PAT-'.Str::upper(Str::random(8)),
                    'first_name' => $checkIn->first_name,
                    'last_name' => $checkIn->last_name,
                    'date_of_birth' => $checkIn->date_of_birth,
                    'phone' => $checkIn->phone,
                    'email' => $checkIn->email,
                    'status' => 'active',
                ]);
                $checkIn->patient()->associate($patient);
            }

            $lastQueueNumber = PatientCheckIn::query()
                ->whereDate('check_in_date', $checkIn->check_in_date)
                ->whereNotNull('queue_number')
                ->lockForUpdate()
                ->max('queue_number');

            $checkIn->forceFill([
                'status' => 'registered',
                'queue_number' => ((int) $lastQueueNumber) + 1,
                'patient_id' => $patient->id,
                'reviewed_by_user_id' => auth()->id(),
                'reviewed_at' => now(),
            ])->save();

            $patientAttributes = array_filter([
                'address' => $checkIn->address,
                'gender' => $checkIn->gender,
                'height_cm' => $checkIn->height_cm,
                'weight_kg' => $checkIn->weight_kg,
                'blood_pressure' => $checkIn->blood_pressure,
                'temperature_c' => $checkIn->temperature_c,
                'pulse_rate' => $checkIn->pulse_rate,
                'respiratory_rate' => $checkIn->respiratory_rate,
                'oxygen_saturation' => $checkIn->oxygen_saturation,
            ], fn ($value) => $value !== null && $value !== '');

            if ($checkIn->height_cm !== null && $checkIn->weight_kg !== null) {
                $heightMeters = ((float) $checkIn->height_cm) / 100;
                $patientAttributes['bmi'] = round(((float) $checkIn->weight_kg) / ($heightMeters * $heightMeters), 2);
            }

            if ($patientAttributes !== []) {
                $patient->update($patientAttributes);
            }
        });

        session()->flash('status', "Check-in verified and added to queue #{$checkIn->fresh()->queue_number}. Estimated wait times are guidance only.");
    }

    public function rejectCheckIn(int $checkInId): void
    {
        $checkIn = PatientCheckIn::query()
            ->where('status', 'pending')
            ->findOrFail($checkInId);

        $checkIn->forceFill([
            'status' => 'rejected',
            'reviewed_by_user_id' => auth()->id(),
            'reviewed_at' => now(),
        ])->save();

        session()->flash('status', 'Check-in request rejected.');
    }

    public function render(): View
    {
        $nextPatient = PatientCheckIn::query()
            ->where('status', 'registered')
            ->whereDate('check_in_date', today())
            ->orderBy('queue_number')
            ->first();

        return view('livewire.check-ins', [
            'checkIns' => PatientCheckIn::query()
                ->with(['patient:id,patient_number,first_name,last_name', 'requestedPatient:id,patient_number,first_name,last_name', 'reviewedBy:id,name'])
                ->when($this->statusFilter === 'queue', fn ($query) => $query->whereIn('status', ['registered', 'in_consultation'])->whereDate('check_in_date', today()))
                ->when(in_array($this->statusFilter, ['pending', 'rejected'], true), fn ($query) => $query->where('status', $this->statusFilter))
                ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
                ->orderBy('queue_number')
                ->orderByDesc('created_at')
                ->paginate(15),
            'pendingCount' => PatientCheckIn::query()->where('status', 'pending')->count(),
            'todayQueueCount' => PatientCheckIn::query()
                ->whereIn('status', ['registered', 'in_consultation'])
                ->whereDate('check_in_date', today())
                ->count(),
            'nextPatient' => $nextPatient,
        ]);
    }
}
