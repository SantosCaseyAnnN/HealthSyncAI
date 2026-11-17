<?php

namespace App\Livewire;

use App\Livewire\Concerns\AuthorizesClinicStaff;
use App\Models\CheckIn as PatientCheckIn;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class CheckIns extends Component
{
    use AuthorizesClinicStaff;
    use WithPagination;

    public string $statusFilter = 'pending';

    public function registerInQueue(int $checkInId): void
    {
        $checkIn = PatientCheckIn::query()
            ->where('status', 'pending')
            ->findOrFail($checkInId);

        DB::transaction(function () use ($checkIn): void {
            $lastQueueNumber = PatientCheckIn::query()
                ->whereDate('check_in_date', $checkIn->check_in_date)
                ->whereNotNull('queue_number')
                ->lockForUpdate()
                ->max('queue_number');

            $checkIn->forceFill([
                'status' => 'registered',
                'queue_number' => ((int) $lastQueueNumber) + 1,
                'reviewed_by_user_id' => auth()->id(),
                'reviewed_at' => now(),
            ])->save();
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
        return view('livewire.check-ins', [
            'checkIns' => PatientCheckIn::query()
                ->with(['patient:id,patient_number,first_name,last_name', 'reviewedBy:id,name'])
                ->when($this->statusFilter === 'queue', fn ($query) => $query->where('status', 'registered')->whereDate('check_in_date', today()))
                ->when(in_array($this->statusFilter, ['pending', 'rejected'], true), fn ($query) => $query->where('status', $this->statusFilter))
                ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
                ->orderBy('queue_number')
                ->orderByDesc('created_at')
                ->paginate(15),
            'pendingCount' => PatientCheckIn::query()->where('status', 'pending')->count(),
            'todayQueueCount' => PatientCheckIn::query()
                ->where('status', 'registered')
                ->whereDate('check_in_date', today())
                ->count(),
        ]);
    }
}
