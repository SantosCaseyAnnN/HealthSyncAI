<?php

namespace App\Livewire;

use App\Livewire\Concerns\AuthorizesClinicStaff;
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

    public string $status = '';

    public string $gender = '';

    public bool $showForm = false;

    public ?int $editingPatientId = null;

    public string $firstName = '';

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

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function updatedGender(): void
    {
        $this->resetPage();
    }

    public function openForm(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function editPatient(int $patientId): void
    {
        $patient = Patient::findOrFail($patientId);

        $this->editingPatientId = $patient->id;
        $this->firstName = $patient->first_name;
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
            'heightCm' => ['nullable', 'numeric', 'min:30', 'max:250'],
            'weightKg' => ['nullable', 'numeric', 'min:1', 'max:500'],
            'bmi' => ['nullable', 'numeric', 'min:5', 'max:100'],
            'bloodPressure' => ['nullable', 'string', 'max:20'],
            'temperatureC' => ['nullable', 'numeric', 'min:25', 'max:45'],
            'pulseRate' => ['nullable', 'integer', 'min:20', 'max:300'],
            'respiratoryRate' => ['nullable', 'integer', 'min:4', 'max:80'],
            'oxygenSaturation' => ['nullable', 'numeric', 'min:50', 'max:100'],
        ]);

        $attributes = [
            'first_name' => $validated['firstName'],
            'last_name' => $validated['lastName'],
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

            session()->flash('status', "Patient {$patient->patient_number} was created.");
        } else {
            Patient::findOrFail($this->editingPatientId)->update($attributes);

            session()->flash('status', 'Patient details were updated.');
        }

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
            ->withCount(['appointments', 'medicalRecords', 'labOrders'])
            ->when($this->search !== '', function ($query) {
                $search = '%'.$this->search.'%';

                $query->where(function ($query) use ($search) {
                    $query->where('patient_number', 'like', $search)
                        ->orWhere('first_name', 'like', $search)
                        ->orWhere('last_name', 'like', $search)
                        ->orWhere('phone', 'like', $search);
                });
            })
            ->when($this->status !== '', fn ($query) => $query->where('status', $this->status))
            ->when($this->gender !== '', fn ($query) => $query->where('gender', $this->gender))
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(15);

        return view('livewire.patients', [
            'filteredPatients' => $patients,
        ]);
    }

    private function resetForm(): void
    {
        $this->reset(
            'editingPatientId',
            'firstName',
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
