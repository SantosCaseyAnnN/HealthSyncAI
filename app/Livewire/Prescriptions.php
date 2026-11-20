<?php

namespace App\Livewire;

use App\Livewire\Concerns\AuthorizesClinicStaff;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Prescription;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Prescriptions extends Component
{
    use AuthorizesClinicStaff;
    use WithPagination;

    public string $search = '';

    public bool $showForm = false;

    public ?int $editingPrescriptionId = null;

    #[Url(as: 'patient')]
    public ?int $patientFilter = null;

    public ?int $patientId = null;

    public ?int $appointmentId = null;

    public string $medication = '';

    public string $strength = '';

    public string $dosage = '';

    public string $prescriptionRoute = '';

    public string $frequency = '';

    public string $duration = '';

    public string $instructions = '';

    public string $issuedAt = '';

    public string $status = 'active';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPatientId(): void
    {
        $this->appointmentId = null;
    }

    public function openForm(): void
    {
        $this->resetForm();
        $this->patientId = $this->patientFilter;
        $this->issuedAt = now()->format('Y-m-d\TH:i');
        $this->showForm = true;
    }

    public function editPrescription(int $prescriptionId): void
    {
        $prescription = Prescription::query()->findOrFail($prescriptionId);
        $this->editingPrescriptionId = $prescription->id;
        $this->patientId = $prescription->patient_id;
        $this->appointmentId = $prescription->appointment_id;
        $this->medication = $prescription->medication;
        $this->strength = $prescription->strength ?? '';
        $this->dosage = $prescription->dosage;
        $this->prescriptionRoute = $prescription->route ?? '';
        $this->frequency = $prescription->frequency;
        $this->duration = $prescription->duration ?? '';
        $this->instructions = $prescription->instructions ?? '';
        $this->issuedAt = $prescription->issued_at->format('Y-m-d\TH:i');
        $this->status = $prescription->status;
        $this->showForm = true;
    }

    public function savePrescription(): void
    {
        $validated = $this->validate([
            'patientId' => ['required', 'integer', 'exists:patients,id'],
            'appointmentId' => [
                'nullable',
                'integer',
                Rule::exists('appointments', 'id')->where('patient_id', $this->patientId),
            ],
            'medication' => ['required', 'string', 'max:150'],
            'strength' => ['nullable', 'string', 'max:100'],
            'dosage' => ['required', 'string', 'max:150'],
            'prescriptionRoute' => ['nullable', 'string', 'max:50'],
            'frequency' => ['required', 'string', 'max:150'],
            'duration' => ['nullable', 'string', 'max:100'],
            'instructions' => ['nullable', 'string', 'max:5000'],
            'issuedAt' => ['required', 'date'],
            'status' => ['required', Rule::in(['active', 'discontinued'])],
        ]);

        $attributes = [
            'patient_id' => $validated['patientId'],
            'appointment_id' => $validated['appointmentId'] ?: null,
            'prescribed_by_user_id' => auth()->id(),
            'medication' => $validated['medication'],
            'strength' => $validated['strength'] ?: null,
            'dosage' => $validated['dosage'],
            'route' => $validated['prescriptionRoute'] ?: null,
            'frequency' => $validated['frequency'],
            'duration' => $validated['duration'] ?: null,
            'instructions' => $validated['instructions'] ?: null,
            'issued_at' => $validated['issuedAt'],
            'status' => $validated['status'],
        ];

        if ($this->editingPrescriptionId === null) {
            Prescription::query()->create($attributes);
        } else {
            Prescription::query()->findOrFail($this->editingPrescriptionId)->update($attributes);
        }

        session()->flash('status', $this->editingPrescriptionId === null
            ? 'Prescription record created.'
            : 'Prescription record updated.');

        $this->resetForm();
        $this->resetPage();
    }

    public function discontinuePrescription(int $prescriptionId): void
    {
        Prescription::query()->findOrFail($prescriptionId)->update(['status' => 'discontinued']);
        session()->flash('status', 'Prescription marked as discontinued.');
    }

    public function cancelForm(): void
    {
        $this->resetForm();
    }

    public function render(): View
    {
        $search = '%'.$this->search.'%';

        return view('livewire.prescriptions', [
            'prescriptions' => Prescription::query()
                ->with(['patient:id,patient_number,first_name,last_name', 'prescribedBy:id,name'])
                ->when($this->patientFilter, fn ($query) => $query->where('patient_id', $this->patientFilter))
                ->when($this->search !== '', fn ($query) => $query->where(function ($query) use ($search) {
                    $query->where('medication', 'like', $search)
                        ->orWhereHas('patient', fn ($query) => $query
                            ->where('patient_number', 'like', $search)
                            ->orWhere('first_name', 'like', $search)
                            ->orWhere('last_name', 'like', $search));
                }))
                ->orderByDesc('issued_at')
                ->orderByDesc('id')
                ->paginate(15),
            'patients' => Patient::query()->orderBy('last_name')->orderBy('first_name')->get(),
            'appointments' => Appointment::query()
                ->when($this->patientId, fn ($query) => $query->where('patient_id', $this->patientId))
                ->orderByDesc('scheduled_at')
                ->limit(100)
                ->get(),
        ]);
    }

    private function resetForm(): void
    {
        $this->reset(
            'editingPrescriptionId',
            'patientId',
            'appointmentId',
            'medication',
            'strength',
            'dosage',
            'prescriptionRoute',
            'frequency',
            'duration',
            'instructions',
        );
        $this->issuedAt = '';
        $this->status = 'active';
        $this->showForm = false;
        $this->resetValidation();
    }
}
