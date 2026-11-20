<div class="healthsync-dashboard">
    @include('livewire.partials.clinical-navigation', ['active' => 'check-ins'])
    <main class="main-content">
        @include('livewire.partials.workspace-topbar', ['section' => 'Clinic / Patient check-in'])

        <div class="page-heading">
            <div><h2>Patient check-in</h2><p>Review new and returning patient submissions before registering them in today's queue.</p></div>
            @if ($nextPatient)
                <button type="button" wire:click="callNextPatient" class="primary-button">Call next patient</button>
            @else
                <button type="button" disabled class="primary-button opacity-60">No patient waiting</button>
            @endif
        </div>
        @if (session()->has('status')) <p class="clinical-notice" role="status">{{ session('status') }}</p> @endif
        <p class="clinical-notice" role="note">Queue waiting times are estimates for workflow planning only, not medical judgments. Review patients who may need urgent care using your clinical procedures.</p>
        <section class="directory-panel mb-4">
            <div class="panel-heading">
                <div><strong>Share the patient check-in form</strong><small>Patients can submit their information without signing in. Requests appear here for staff review.</small></div>
            </div>
            <div class="clinical-form flex flex-wrap items-center gap-5">
                <img src="{{ route('patient-check-in.qr') }}" width="128" height="128" alt="QR code for the public patient check-in form">
                <div class="min-w-0">
                    <p class="mb-2 text-sm font-semibold">Scan the QR code or share this link:</p>
                    <a class="break-all text-sm font-medium text-blue-700 underline" href="{{ route('patient-check-in') }}">{{ route('patient-check-in') }}</a>
                    <p class="mt-2 text-xs text-slate-500">Returning patients can include their patient number. Staff verify submissions before queue registration.</p>
                </div>
            </div>
        </section>
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
            <div class="panel-heading"><div><strong>{{ $statusFilter === 'queue' ? 'Live queue' : 'Check-in submissions' }}</strong><small>Review submitted information against the patient's record before registering in queue.</small></div><span class="record-count">{{ $checkIns->total() }} requests</span></div>
            <div class="table-scroll">
                <table class="health-table">
                    <thead><tr><th>SUBMITTED</th><th>PATIENT</th><th>CONTACT</th><th>VISIT REASON</th><th>STATUS / QUEUE</th><th>WAIT ESTIMATE</th><th>ACTIONS</th></tr></thead>
                    <tbody>
                        @forelse ($checkIns as $checkIn)
                            <tr wire:key="check-in-{{ $checkIn->id }}">
                                <td>{{ $checkIn->created_at->format('M d, h:i A') }}</td>
                                <td>
                                    <strong>{{ $checkIn->full_name }}</strong>
                                    <small class="block">{{ $checkIn->date_of_birth?->format('M d, Y') ?: 'Birth date not provided' }}</small>
                                    @if ($checkIn->patient)
                                        <small class="block">Linked: {{ $checkIn->patient->patient_number }} — {{ $checkIn->patient->full_name }}</small>
                                    @elseif ($checkIn->possibleMatchPatient)
                                        <small class="mt-1 block rounded-md border border-amber-200 bg-amber-50 p-2 text-amber-900" role="alert">
                                            Possible existing patient. A new request was submitted for this patient:
                                            <a class="font-semibold underline" href="{{ route('patients.show', $checkIn->possibleMatchPatient) }}">{{ $checkIn->possibleMatchPatient->patient_number }} — {{ $checkIn->possibleMatchPatient->full_name }}</a>
                                            @if ($checkIn->existing_patient_number)
                                                <span class="block">Number entered: {{ $checkIn->existing_patient_number }}. Verify the match before registration.</span>
                                            @endif
                                        </small>
                                    @elseif ($checkIn->existing_patient_number)
                                        <small class="block">
                                            Existing number: {{ $checkIn->existing_patient_number }}
                                            @if ($checkIn->requestedPatient)
                                                · Possible match: <a class="text-blue-700 underline" href="{{ route('patients.show', $checkIn->requestedPatient) }}">{{ $checkIn->requestedPatient->full_name }}</a>
                                            @else
                                                · No matching record found
                                            @endif
                                        </small>
                                    @else
                                        <small class="block">New patient request</small>
                                    @endif
                                </td>
                                <td>{{ $checkIn->phone ?: '—' }}<small class="block">{{ $checkIn->email ?: '' }}</small><small class="block">{{ $checkIn->address ?: '' }}</small></td>
                                <td>
                                    {{ $checkIn->reason_for_visit ?: 'Not provided' }}
                                    @if ($checkIn->gender || $checkIn->height_cm || $checkIn->weight_kg || $checkIn->blood_pressure)
                                        <small class="block">
                                            {{ $checkIn->gender ? 'Gender: '.$checkIn->gender : '' }}
                                            {{ $checkIn->height_cm ? ' · '.$checkIn->height_cm.' cm' : '' }}
                                            {{ $checkIn->weight_kg ? ' · '.$checkIn->weight_kg.' kg' : '' }}
                                        </small>
                                        <small class="block">
                                            {{ $checkIn->blood_pressure ? 'BP '.$checkIn->blood_pressure : '' }}
                                            {{ $checkIn->temperature_c ? ' · '.$checkIn->temperature_c.' C' : '' }}
                                            {{ $checkIn->pulse_rate ? ' · '.$checkIn->pulse_rate.' bpm' : '' }}
                                            {{ $checkIn->respiratory_rate ? ' · '.$checkIn->respiratory_rate.'/min' : '' }}
                                            {{ $checkIn->oxygen_saturation ? ' · '.$checkIn->oxygen_saturation.'% O2' : '' }}
                                        </small>
                                    @endif
                                </td>
                                <td>{{ str($checkIn->status)->replace('_', ' ')->title() }} @if ($checkIn->queue_number)<small class="block">#{{ $checkIn->queue_number }}</small>@endif</td>
                                <td>@if ($checkIn->status === 'registered') ~{{ max(0, $checkIn->queue_number - 1) * 15 }} min<small class="block">Workflow estimate only</small>@else — @endif</td>
                                <td>
                                    @if ($checkIn->status === 'pending')
                                        <div class="flex flex-col gap-2">
                                            @if ($checkIn->patient)
                                                <button type="button" wire:click="registerInQueue({{ $checkIn->id }})" class="small-outline-button">Verify &amp; register</button>
                                            @elseif ($checkIn->possibleMatchPatient || $checkIn->requestedPatient)
                                                <button type="button" wire:click="registerInQueue({{ $checkIn->id }})" class="small-outline-button">Match &amp; register</button>
                                            @elseif ($checkIn->existing_patient_number)
                                                <button type="button" wire:click="registerAsNewPatient({{ $checkIn->id }})" class="small-outline-button">Register as new patient</button>
                                            @else
                                                <button type="button" wire:click="registerInQueue({{ $checkIn->id }})" class="small-outline-button">Create patient &amp; register</button>
                                            @endif
                                            <button type="button" wire:click="rejectCheckIn({{ $checkIn->id }})" class="small-outline-button">Reject</button>
                                        </div>
                                    @elseif ($checkIn->status === 'registered')
                                        <small>Waiting for next call</small>
                                    @elseif ($checkIn->status === 'in_consultation')
                                        <button type="button" wire:click="completePatient({{ $checkIn->id }})" class="small-outline-button">Complete visit</button>
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
