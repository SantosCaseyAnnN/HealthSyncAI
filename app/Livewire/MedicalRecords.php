<?php

namespace App\Livewire;

use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Models\Patient;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class MedicalRecords extends Component
{
    use WithPagination;

    public string $search = '';

    #[Url(as: 'patient')]
    public ?int $patientFilter = null;

    public ?int $selectedRecordId = null;

    public bool $showForm = false;

    public ?int $editingRecordId = null;

    public ?int $patientId = null;

    public ?int $appointmentId = null;

    public string $recordTitle = '';

    public string $provider = '';

    public string $recordedAt = '';

    public string $subjective = '';

    public string $objective = '';

    public string $assessment = '';

    public string $plan = '';

    public function mount(): void
    {
        $this->selectedRecordId = $this->recordsQuery()->value('id');
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
        $this->selectedRecordId = $this->recordsQuery()->value('id');
    }

    public function updatedPatientFilter(): void
    {
        $this->resetPage();
        $this->selectedRecordId = $this->recordsQuery()->value('id');
    }

    public function updatedPatientId(): void
    {
        $this->appointmentId = null;
    }

    public function selectRecord(int $recordId): void
    {
        $record = $this->recordsQuery()->findOrFail($recordId);
        $this->selectedRecordId = $record->id;
        $this->showForm = false;
    }

    public function openForm(): void
    {
        $this->resetForm();
        $this->patientId = $this->patientFilter;
        $this->recordedAt = now()->format('Y-m-d\TH:i');
        $this->showForm = true;
    }

    public function editRecord(int $recordId): void
    {
        $record = $this->recordsQuery()->findOrFail($recordId);

        $this->editingRecordId = $record->id;
        $this->selectedRecordId = $record->id;
        $this->patientId = $record->patient_id;
        $this->appointmentId = $record->appointment_id;
        $this->recordTitle = $record->title;
        $this->provider = $record->provider;
        $this->recordedAt = $record->recorded_at->format('Y-m-d\TH:i');
        $this->subjective = $record->subjective;
        $this->objective = $record->objective;
        $this->assessment = $record->assessment;
        $this->plan = $record->plan;
        $this->showForm = true;
    }

    public function saveRecord(): void
    {
        $existingRecord = $this->editingRecordId === null
            ? null
            : $this->recordsQuery()->findOrFail($this->editingRecordId);
        $patientIdRules = ['required', 'integer', 'exists:patients,id'];

        if ($existingRecord !== null) {
            $patientIdRules[] = Rule::in([$existingRecord->patient_id]);
        }

        $validated = $this->validate([
            'patientId' => $patientIdRules,
            'appointmentId' => [
                'nullable',
                'integer',
                Rule::exists('appointments', 'id')->where('patient_id', $this->patientId),
            ],
            'recordTitle' => ['required', 'string', 'max:150'],
            'provider' => ['required', 'string', 'max:150'],
            'recordedAt' => ['required', 'date'],
            'subjective' => ['required', 'string', 'max:10000'],
            'objective' => ['required', 'string', 'max:10000'],
            'assessment' => ['required', 'string', 'max:10000'],
            'plan' => ['required', 'string', 'max:10000'],
        ]);

        $attributes = [
            'patient_id' => $validated['patientId'],
            'appointment_id' => $validated['appointmentId'] ?: null,
            'title' => $validated['recordTitle'],
            'provider' => $validated['provider'],
            'recorded_at' => $validated['recordedAt'],
            'subjective' => $validated['subjective'],
            'objective' => $validated['objective'],
            'assessment' => $validated['assessment'],
            'plan' => $validated['plan'],
        ];

        if ($this->editingRecordId === null) {
            $record = MedicalRecord::create([
                'record_number' => 'REC-'.Str::upper(Str::random(8)),
                ...$attributes,
            ]);

            session()->flash('status', "Medical record {$record->record_number} was created.");
        } else {
            $record = $existingRecord;
            $record->update($attributes);

            session()->flash('status', 'Medical record details were updated.');
        }

        $this->selectedRecordId = $record->id;
        $this->resetForm();
        $this->resetPage();
    }

    public function cancelForm(): void
    {
        $this->resetForm();
    }

    public function render(): View
    {
        return view('livewire.medical-records', [
            'filteredRecords' => $this->recordsQuery()
                ->orderByDesc('recorded_at')
                ->orderByDesc('id')
                ->paginate(12),
            'selectedRecord' => $this->recordsQuery()->find($this->selectedRecordId),
            'patients' => Patient::query()->orderBy('last_name')->orderBy('first_name')->get(),
            'appointments' => Appointment::query()
                ->when($this->patientId, fn ($query) => $query->where('patient_id', $this->patientId))
                ->orderByDesc('scheduled_at')
                ->limit(100)
                ->get(),
            'recordCount' => MedicalRecord::query()
                ->when($this->patientFilter, fn ($query) => $query->where('patient_id', $this->patientFilter))
                ->count(),
        ]);
    }

    /**
     * @return Builder<MedicalRecord>
     */
    private function recordsQuery(): Builder
    {
        return MedicalRecord::query()
            ->with(['patient', 'appointment', 'labOrders'])
            ->when($this->patientFilter, fn ($query) => $query->where('patient_id', $this->patientFilter))
            ->when($this->search !== '', function ($query) {
                $search = '%'.$this->search.'%';

                $query->where(function ($query) use ($search) {
                    $query->where('record_number', 'like', $search)
                        ->orWhere('title', 'like', $search)
                        ->orWhereHas('patient', function ($query) use ($search) {
                            $query->where('patient_number', 'like', $search)
                                ->orWhere('first_name', 'like', $search)
                                ->orWhere('last_name', 'like', $search);
                        });
                });
            });
    }

    private function resetForm(): void
    {
        $this->reset(
            'editingRecordId',
            'patientId',
            'appointmentId',
            'recordTitle',
            'provider',
            'subjective',
            'objective',
            'assessment',
            'plan',
        );
        $this->recordedAt = '';
        $this->showForm = false;
        $this->resetValidation();
    }
}
