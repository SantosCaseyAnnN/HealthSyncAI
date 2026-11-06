<?php

namespace App\Livewire;

use App\Models\Appointment;
use App\Models\LabOrder;
use App\Models\MedicalRecord;
use App\Models\Patient;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Laboratory extends Component
{
    use WithPagination;

    public string $search = '';

    #[Url(as: 'patient')]
    public ?int $patientFilter = null;

    public bool $showForm = false;

    public ?int $editingLabOrderId = null;

    public ?int $patientId = null;

    public ?int $appointmentId = null;

    public ?int $medicalRecordId = null;

    public string $testName = '';

    public string $orderedBy = '';

    public string $status = 'ordered';

    public string $orderedAt = '';

    public string $result = '';

    public string $resultAt = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPatientFilter(): void
    {
        $this->resetPage();
    }

    public function updatedPatientId(): void
    {
        $this->appointmentId = null;
        $this->medicalRecordId = null;
    }

    public function openForm(): void
    {
        $this->resetForm();
        $this->patientId = $this->patientFilter;
        $this->orderedAt = now()->format('Y-m-d\TH:i');
        $this->showForm = true;
    }

    public function editLabOrder(int $labOrderId): void
    {
        $labOrder = LabOrder::findOrFail($labOrderId);

        $this->editingLabOrderId = $labOrder->id;
        $this->patientId = $labOrder->patient_id;
        $this->appointmentId = $labOrder->appointment_id;
        $this->medicalRecordId = $labOrder->medical_record_id;
        $this->testName = $labOrder->test_name;
        $this->orderedBy = $labOrder->ordered_by;
        $this->status = $labOrder->status;
        $this->orderedAt = $labOrder->ordered_at->format('Y-m-d\TH:i');
        $this->result = $labOrder->result ?? '';
        $this->resultAt = $labOrder->result_at?->format('Y-m-d\TH:i') ?? '';
        $this->showForm = true;
    }

    public function saveLabOrder(): void
    {
        $validated = $this->validate([
            'patientId' => ['required', 'integer', 'exists:patients,id'],
            'appointmentId' => [
                'nullable',
                'integer',
                Rule::exists('appointments', 'id')->where('patient_id', $this->patientId),
            ],
            'medicalRecordId' => [
                'nullable',
                'integer',
                Rule::exists('medical_records', 'id')->where('patient_id', $this->patientId),
            ],
            'testName' => ['required', 'string', 'max:150'],
            'orderedBy' => ['required', 'string', 'max:150'],
            'status' => ['required', Rule::in(['ordered', 'in_progress', 'resulted', 'cancelled'])],
            'orderedAt' => ['required', 'date'],
            'result' => [Rule::requiredIf($this->status === 'resulted'), 'nullable', 'string', 'max:10000'],
            'resultAt' => ['nullable', 'date'],
        ]);

        $attributes = [
            'patient_id' => $validated['patientId'],
            'appointment_id' => $validated['appointmentId'] ?: null,
            'medical_record_id' => $validated['medicalRecordId'] ?: null,
            'test_name' => $validated['testName'],
            'ordered_by' => $validated['orderedBy'],
            'status' => $validated['status'],
            'ordered_at' => $validated['orderedAt'],
            'result' => $validated['result'] ?: null,
            'result_at' => $validated['resultAt'] ?: ($validated['status'] === 'resulted' ? now() : null),
        ];

        if ($this->editingLabOrderId === null) {
            $labOrder = LabOrder::create([
                'order_number' => 'LAB-'.Str::upper(Str::random(8)),
                ...$attributes,
            ]);

            session()->flash('status', "Laboratory order {$labOrder->order_number} was created.");
        } else {
            LabOrder::findOrFail($this->editingLabOrderId)->update($attributes);

            session()->flash('status', 'Laboratory order details were updated.');
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
        $labOrdersQuery = LabOrder::query()
            ->with(['patient', 'appointment', 'medicalRecord'])
            ->when($this->patientFilter, fn ($query) => $query->where('patient_id', $this->patientFilter))
            ->when($this->search !== '', function ($query) {
                $search = '%'.$this->search.'%';

                $query->where(function ($query) use ($search) {
                    $query->where('order_number', 'like', $search)
                        ->orWhere('test_name', 'like', $search)
                        ->orWhereHas('patient', function ($query) use ($search) {
                            $query->where('patient_number', 'like', $search)
                                ->orWhere('first_name', 'like', $search)
                                ->orWhere('last_name', 'like', $search);
                        });
                });
            });

        return view('livewire.laboratory', [
            'labOrders' => $labOrdersQuery->orderByDesc('ordered_at')->orderByDesc('id')->paginate(15),
            'patients' => Patient::query()->orderBy('last_name')->orderBy('first_name')->get(),
            'appointments' => Appointment::query()
                ->when($this->patientId, fn ($query) => $query->where('patient_id', $this->patientId))
                ->orderByDesc('scheduled_at')
                ->limit(100)
                ->get(),
            'medicalRecords' => MedicalRecord::query()
                ->when($this->patientId, fn ($query) => $query->where('patient_id', $this->patientId))
                ->orderByDesc('recorded_at')
                ->limit(100)
                ->get(),
            'orderCount' => (clone $labOrdersQuery)->count(),
            'pendingCount' => (clone $labOrdersQuery)->whereIn('status', ['ordered', 'in_progress'])->count(),
            'resultedCount' => (clone $labOrdersQuery)->where('status', 'resulted')->count(),
        ]);
    }

    private function resetForm(): void
    {
        $this->reset(
            'editingLabOrderId',
            'patientId',
            'appointmentId',
            'medicalRecordId',
            'testName',
            'orderedBy',
            'result',
            'resultAt',
        );
        $this->status = 'ordered';
        $this->orderedAt = '';
        $this->showForm = false;
        $this->resetValidation();
    }
}
