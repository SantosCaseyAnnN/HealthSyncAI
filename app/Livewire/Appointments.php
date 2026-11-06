<?php

namespace App\Livewire;

use App\Models\Appointment;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Appointments extends Component
{
    use WithPagination;

    public string $viewMode = 'list';

    public string $selectedDate = '';

    #[Url(as: 'patient')]
    public ?int $patientFilter = null;

    public bool $showForm = false;

    public ?int $editingAppointmentId = null;

    public ?int $patientId = null;

    public string $scheduledAt = '';

    public string $provider = '';

    public string $appointmentType = '';

    public string $status = 'scheduled';

    public string $notes = '';

    public function mount(): void
    {
        $this->selectedDate = now()->toDateString();
    }

    public function rules(): array
    {
        return [
            'selectedDate' => ['required', 'date_format:Y-m-d'],
        ];
    }

    public function updatedSelectedDate(): void
    {
        $this->validateOnly('selectedDate');
        $this->resetPage();
    }

    public function updatedPatientFilter(): void
    {
        $this->resetPage();
    }

    public function previousDay(): void
    {
        $this->selectedDate = Carbon::parse($this->selectedDate)->subDay()->toDateString();
        $this->resetPage();
    }

    public function nextDay(): void
    {
        $this->selectedDate = Carbon::parse($this->selectedDate)->addDay()->toDateString();
        $this->resetPage();
    }

    public function today(): void
    {
        $this->selectedDate = now()->toDateString();
        $this->resetPage();
    }

    public function openForm(): void
    {
        $this->resetForm();
        $this->patientId = $this->patientFilter;
        $this->scheduledAt = Carbon::parse($this->selectedDate)
            ->setTime(9, 0)
            ->format('Y-m-d\TH:i');
        $this->showForm = true;
    }

    public function editAppointment(int $appointmentId): void
    {
        $appointment = Appointment::findOrFail($appointmentId);

        $this->editingAppointmentId = $appointment->id;
        $this->patientId = $appointment->patient_id;
        $this->scheduledAt = $appointment->scheduled_at->format('Y-m-d\TH:i');
        $this->provider = $appointment->provider;
        $this->appointmentType = $appointment->appointment_type;
        $this->status = $appointment->status;
        $this->notes = $appointment->notes ?? '';
        $this->showForm = true;
    }

    public function saveAppointment(): void
    {
        $existingAppointment = $this->editingAppointmentId === null
            ? null
            : Appointment::findOrFail($this->editingAppointmentId);
        $patientIdRules = ['required', 'integer', 'exists:patients,id'];

        if ($existingAppointment !== null) {
            $patientIdRules[] = Rule::in([$existingAppointment->patient_id]);
        }

        $validated = $this->validate([
            'patientId' => $patientIdRules,
            'scheduledAt' => ['required', 'date'],
            'provider' => ['required', 'string', 'max:150'],
            'appointmentType' => ['required', 'string', 'max:150'],
            'status' => ['required', Rule::in(['pending', 'scheduled', 'in_progress', 'completed', 'cancelled'])],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $attributes = [
            'patient_id' => $validated['patientId'],
            'scheduled_at' => $validated['scheduledAt'],
            'provider' => $validated['provider'],
            'appointment_type' => $validated['appointmentType'],
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?: null,
        ];

        if ($this->editingAppointmentId === null) {
            $appointment = Appointment::create([
                'appointment_code' => 'APT-'.Str::upper(Str::random(8)),
                ...$attributes,
            ]);

            session()->flash('status', "Appointment {$appointment->appointment_code} was created.");
        } else {
            $existingAppointment->update($attributes);

            session()->flash('status', 'Appointment details were updated.');
        }

        $this->resetForm();
        $this->resetPage();
    }

    public function cancelAppointment(int $appointmentId): void
    {
        Appointment::findOrFail($appointmentId)->update(['status' => 'cancelled']);
    }

    public function cancelForm(): void
    {
        $this->resetForm();
    }

    public function render(): View
    {
        $appointmentsQuery = Appointment::query()
            ->withCount(['medicalRecords', 'labOrders'])
            ->whereDate('scheduled_at', $this->selectedDate)
            ->when($this->patientFilter, fn ($query) => $query->where('patient_id', $this->patientFilter));

        $totalCount = (clone $appointmentsQuery)->count();
        $pendingCount = (clone $appointmentsQuery)
            ->whereIn('status', ['pending', 'in_progress'])
            ->count();
        $completedCount = (clone $appointmentsQuery)->where('status', 'completed')->count();

        return view('livewire.appointments', [
            'appointments' => (clone $appointmentsQuery)
                ->with('patient:id,patient_number,first_name,last_name')
                ->orderBy('scheduled_at')
                ->paginate(15),
            'patients' => Patient::query()->orderBy('last_name')->orderBy('first_name')->get(),
            'totalCount' => $totalCount,
            'pendingCount' => $pendingCount,
            'completedCount' => $completedCount,
        ]);
    }

    private function resetForm(): void
    {
        $this->reset('editingAppointmentId', 'patientId', 'provider', 'appointmentType', 'notes');
        $this->status = 'scheduled';
        $this->scheduledAt = '';
        $this->showForm = false;
        $this->resetValidation();
    }
}
