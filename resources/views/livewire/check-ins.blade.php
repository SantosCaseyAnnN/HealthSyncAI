<div class="healthsync-dashboard">
    @include('livewire.partials.clinical-navigation', ['active' => 'check-ins'])
    <main class="main-content">
        <div class="page-heading">
            <div><h2>Patient check-ins & queue</h2><p>Review QR check-in submissions before registering patients in today's queue.</p></div>
        </div>
        @if (session()->has('status')) <p class="clinical-notice" role="status">{{ session('status') }}</p> @endif
        <p class="clinical-notice" role="note">Queue waiting times are estimates for workflow planning only, not medical judgments. Review patients who may need urgent care using your clinical procedures.</p>
        <div class="appointment-stats">
            <div class="appointment-stat"><span class="stat-icon pending-icon">↪</span><div><small>Waiting for review</small><strong>{{ $pendingCount }}</strong></div></div>
            <div class="appointment-stat"><span class="stat-icon calendar-icon">▣</span><div><small>Registered today</small><strong>{{ $todayQueueCount }}</strong></div></div>
        </div>
        <div class="filter-bar">
            <label>Show <select wire:model.live="statusFilter" class="filter-select">
                <option value="pending">Needs review</option><option value="queue">Today's queue</option><option value="rejected">Rejected requests</option>
            </select></label>
        </div>
        <section class="directory-panel">
            <div class="panel-heading"><div><strong>Check-in submissions</strong><small>Review submitted information against the patient's record before registering in queue.</small></div><span class="record-count">{{ $checkIns->total() }} requests</span></div>
            <div class="table-scroll">
                <table class="health-table">
                    <thead><tr><th>SUBMITTED</th><th>PATIENT</th><th>CONTACT</th><th>VISIT REASON</th><th>STATUS / QUEUE</th><th>WAIT ESTIMATE</th><th>ACTIONS</th></tr></thead>
                    <tbody>
                        @forelse ($checkIns as $checkIn)
                            <tr wire:key="check-in-{{ $checkIn->id }}">
                                <td>{{ $checkIn->created_at->format('M d, h:i A') }}</td>
                                <td><strong>{{ $checkIn->first_name }} {{ $checkIn->last_name }}</strong><small class="block">{{ $checkIn->date_of_birth?->format('M d, Y') ?: 'Birth date not provided' }}</small><small class="block">Linked: {{ $checkIn->patient->patient_number }} — {{ $checkIn->patient->first_name }} {{ $checkIn->patient->last_name }}</small></td>
                                <td>{{ $checkIn->phone ?: '—' }}<small class="block">{{ $checkIn->email ?: '' }}</small></td>
                                <td>{{ $checkIn->reason_for_visit ?: 'Not provided' }}</td>
                                <td>{{ str($checkIn->status)->title() }} @if ($checkIn->queue_number)<small class="block">#{{ $checkIn->queue_number }}</small>@endif</td>
                                <td>@if ($checkIn->status === 'registered') ~{{ max(0, $checkIn->queue_number - 1) * 15 }} min<small class="block">Workflow estimate only</small>@else — @endif</td>
                                <td>
                                    @if ($checkIn->status === 'pending')
                                        <div class="flex flex-col gap-2"><button type="button" wire:click="registerInQueue({{ $checkIn->id }})" class="small-outline-button">Verify & register</button><button type="button" wire:click="rejectCheckIn({{ $checkIn->id }})" class="small-outline-button">Reject</button></div>
                                    @else
                                        <small>Reviewed by {{ $checkIn->reviewedBy?->name ?: 'Clinic staff' }}</small>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7">No check-ins in this view.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="pagination-wrap">{{ $checkIns->links() }}</div>
        </section>
    </main>
</div>
