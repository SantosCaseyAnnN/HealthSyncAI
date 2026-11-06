<?php

namespace App\Livewire;

use App\Models\Patient;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class Patients extends Component
{
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
        );
        $this->patientStatus = 'active';
        $this->showForm = false;
        $this->resetValidation();
    }
}
