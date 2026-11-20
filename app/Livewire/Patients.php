<?php

namespace App\Livewire;

use App\Livewire\Concerns\AuthorizesClinicStaff;
use App\Models\CheckIn as PatientCheckIn;
use App\Models\Patient;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class Patients extends Component
{
    use AuthorizesClinicStaff;
    use WithPagination;

    public string $search = '';

    public ?int $selectedPatientId = null;

    public bool $showForm = false;

    public ?int $editingPatientId = null;

    public string $firstName = '';

    public string $middleName = '';

    public string $lastName = '';

    public string $dateOfBirth = '';

    public string $genderInput = '';

    public string $phone = '';

    public string $email = '';

    public string $provider = '';

    public string $patientStatus = 'active';

    public string $bloodType = '';

    public string $allergies = '';

    public string $medicalHistory = '';

    public string $familyHistory = '';

    public string $emergencyContactName = '';

    public string $emergencyContactRelationship = '';

    public string $emergencyContactPhone = '';

    public string $address = '';

    public string $heightCm = '';

    public string $weightKg = '';

    public string $bmi = '';

    public string $bloodPressure = '';

    public string $temperatureC = '';

    public string $pulseRate = '';

    public string $respiratoryRate = '';

    public string $oxygenSaturation = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function openForm(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function selectPatient(int $patientId): void
    {
        $this->selectedPatientId = Patient::query()->findOrFail($patientId)->id;
    }

    public function editPatient(int $patientId): void
    {
        $patient = Patient::findOrFail($patientId);

        $this->selectedPatientId = $patient->id;
        $this->editingPatientId = $patient->id;
        $this->firstName = $patient->first_name;
        $this->middleName = $patient->middle_name ?? '';
        $this->lastName = $patient->last_name;
        $this->dateOfBirth = $patient->date_of_birth?->toDateString() ?? '';
        $this->genderInput = $patient->gender ?? '';
        $this->phone = $patient->phone ?? '';
        $this->email = $patient->email ?? '';
        $this->provider = $patient->provider ?? '';
        $this->patientStatus = $patient->status;
        $this->bloodType = $patient->blood_type ?? '';
        $this->allergies = $patient->allergies ?? '';
        $this->medicalHistory = $patient->medical_history ?? '';
        $this->familyHistory = $patient->family_history ?? '';
        $this->emergencyContactName = $patient->emergency_contact_name ?? '';
        $this->emergencyContactRelationship = $patient->emergency_contact_relationship ?? '';
        $this->emergencyContactPhone = $patient->emergency_contact_phone ?? '';
        $this->address = $patient->address ?? '';
        $this->heightCm = $patient->height_cm !== null ? (string) $patient->height_cm : '';
        $this->weightKg = $patient->weight_kg !== null ? (string) $patient->weight_kg : '';
        $this->bmi = $patient->bmi !== null ? (string) $patient->bmi : '';
        $this->bloodPressure = $patient->blood_pressure ?? '';
        $this->temperatureC = $patient->temperature_c !== null ? (string) $patient->temperature_c : '';
        $this->pulseRate = $patient->pulse_rate !== null ? (string) $patient->pulse_rate : '';
        $this->respiratoryRate = $patient->respiratory_rate !== null ? (string) $patient->respiratory_rate : '';
        $this->oxygenSaturation = $patient->oxygen_saturation !== null ? (string) $patient->oxygen_saturation : '';
        $this->showForm = true;
    }

    public function savePatient(): void
    {
        $validated = $this->validate([
            'firstName' => ['required', 'string', 'max:100'],
            'middleName' => ['required', 'string', 'max:100'],
            'lastName' => ['required', 'string', 'max:100'],
            'dateOfBirth' => ['nullable', 'date', 'before_or_equal:today'],
            'genderInput' => ['nullable', Rule::in(['Female', 'Male', 'Other'])],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('patients', 'email')->ignore($this->editingPatientId),
            ],
            'provider' => ['nullable', 'string', 'max:150'],
            'patientStatus' => ['required', Rule::in(['active', 'inactive'])],
            'bloodType' => ['nullable', Rule::in(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])],
            'allergies' => ['nullable', 'string', 'max:5000'],
            'medicalHistory' => ['nullable', 'string', 'max:10000'],
            'familyHistory' => ['nullable', 'string', 'max:10000'],
            'emergencyContactName' => ['nullable', 'string', 'max:150'],
            'emergencyContactRelationship' => ['nullable', 'string', 'max:100'],
            'emergencyContactPhone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:255'],
            'heightCm' => ['nullable', 'numeric', 'min:30', 'max:250'],
            'weightKg' => ['nullable', 'numeric', 'min:1', 'max:500'],
            'bmi' => ['nullable', 'numeric', 'min:5', 'max:100'],
            'bloodPressure' => ['nullable', 'string', 'max:20'],
            'temperatureC' => ['nullable', 'numeric', 'min:25', 'max:45'],
            'pulseRate' => ['nullable', 'integer', 'min:20', 'max:300'],
            'respiratoryRate' => ['nullable', 'integer', 'min:4', 'max:80'],
            'oxygenSaturation' => ['nullable', 'numeric', 'min:50', 'max:100'],
        ]);

        $firstName = trim($validated['firstName']);
        $middleName = trim($validated['middleName']);
        $lastName = trim($validated['lastName']);

        if ($validated['dateOfBirth'] !== null && $validated['dateOfBirth'] !== '') {
            $matchingPatient = Patient::query()
                ->matchingIdentity($firstName, $middleName, $lastName, $validated['dateOfBirth'])
                ->when($this->editingPatientId !== null, fn ($query) => $query->where('id', '!=', $this->editingPatientId))
                ->orderBy('id')
                ->first();

            if ($matchingPatient !== null) {
                $this->addError(
                    'middleName',
                    "A patient with this name and date of birth already has record {$matchingPatient->patient_number}. Review that record instead of creating a duplicate.",
                );

                return;
            }
        }

        $attributes = [
            'first_name' => $firstName,
            'middle_name' => $middleName,
            'last_name' => $lastName,
            'date_of_birth' => $validated['dateOfBirth'] ?: null,
            'gender' => $validated['genderInput'] ?: null,
            'phone' => $validated['phone'] ?: null,
            'email' => $validated['email'] ?: null,
            'provider' => $validated['provider'] ?: null,
            'status' => $validated['patientStatus'],
            'blood_type' => $validated['bloodType'] ?: null,
            'allergies' => $validated['allergies'] ?: null,
            'medical_history' => $validated['medicalHistory'] ?: null,
            'family_history' => $validated['familyHistory'] ?: null,
            'emergency_contact_name' => $validated['emergencyContactName'] ?: null,
            'emergency_contact_relationship' => $validated['emergencyContactRelationship'] ?: null,
            'emergency_contact_phone' => $validated['emergencyContactPhone'] ?: null,
            'address' => $validated['address'] ?: null,
            'height_cm' => $validated['heightCm'] !== null && $validated['heightCm'] !== '' ? $validated['heightCm'] : null,
            'weight_kg' => $validated['weightKg'] !== null && $validated['weightKg'] !== '' ? $validated['weightKg'] : null,
            'bmi' => $validated['bmi'] !== null && $validated['bmi'] !== '' ? $validated['bmi'] : null,
            'blood_pressure' => $validated['bloodPressure'] ?: null,
            'temperature_c' => $validated['temperatureC'] !== null && $validated['temperatureC'] !== '' ? $validated['temperatureC'] : null,
            'pulse_rate' => $validated['pulseRate'] !== null && $validated['pulseRate'] !== '' ? $validated['pulseRate'] : null,
            'respiratory_rate' => $validated['respiratoryRate'] !== null && $validated['respiratoryRate'] !== '' ? $validated['respiratoryRate'] : null,
            'oxygen_saturation' => $validated['oxygenSaturation'] !== null && $validated['oxygenSaturation'] !== '' ? $validated['oxygenSaturation'] : null,
        ];

        if ($this->editingPatientId === null) {
            $patient = Patient::create([
                'patient_number' => 'PAT-'.Str::upper(Str::random(8)),
                ...$attributes,
            ]);

            $this->selectedPatientId = $patient->id;
            session()->flash('status', "Patient {$patient->patient_number} was created.");
        } else {
            $patient = Patient::findOrFail($this->editingPatientId);
            $patient->update($attributes);
            $this->selectedPatientId = $patient->id;

            session()->flash('status', 'Patient details were updated.');
        }

        PatientCheckIn::linkPendingDuplicates($patient);

        $this->resetForm();
        $this->resetPage();
    }

    public function cancelForm(): void
    {
        $this->resetForm();
    }

    public function render(): View
    {
        $patients = Patient::query()
            ->with(['latestMedicalRecord' => fn ($query) => $query->select(
                'medical_records.id',
                'medical_records.patient_id',
                'medical_records.title',
                'medical_records.recorded_at',
            )])
            ->when($this->search !== '', function ($query) {
                $search = '%'.$this->search.'%';

                $query->where(function ($query) use ($search) {
                    $query->where('patient_number', 'like', $search)
                        ->orWhere('first_name', 'like', $search)
                        ->orWhere('middle_name', 'like', $search)
                        ->orWhere('last_name', 'like', $search)
                        ->orWhere('phone', 'like', $search);
                });
            })
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(6);
        $selectedPatient = $patients->getCollection()->firstWhere('id', $this->selectedPatientId)
            ?? $patients->first();

        return view('livewire.patients', [
            'filteredPatients' => $patients,
            'selectedPatient' => $selectedPatient,
        ]);
    }

    private function resetForm(): void
    {
        $this->reset(
            'editingPatientId',
            'firstName',
            'middleName',
            'lastName',
            'dateOfBirth',
            'genderInput',
            'phone',
            'email',
            'provider',
            'bloodType',
            'allergies',
            'medicalHistory',
            'familyHistory',
            'emergencyContactName',
            'emergencyContactRelationship',
            'emergencyContactPhone',
            'address',
            'heightCm',
            'weightKg',
            'bmi',
            'bloodPressure',
            'temperatureC',
            'pulseRate',
            'respiratoryRate',
            'oxygenSaturation',
        );
        $this->patientStatus = 'active';
        $this->showForm = false;
        $this->resetValidation();
    }
}
