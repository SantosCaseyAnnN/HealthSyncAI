<div class="healthsync-dashboard">
    @include('livewire.partials.clinical-navigation', ['active' => 'appointments'])

    <main class="main-content">
        <div class="page-heading appointment-heading">
            <div>
                <h2>Appointments</h2>
                <p>Schedule and monitor patient appointments.</p>
            </div>
            <div class="heading-actions">
                <div class="view-toggle">
                    <button type="button" wire:click="$set('viewMode', 'list')" @class(['selected' => $viewMode === 'list'])>List View</button>
                    <button type="button" wire:click="$set('viewMode', 'calendar')" @class(['selected' => $viewMode === 'calendar'])>Calendar View</button>
                </div>
                <button class="primary-button" type="button" wire:click="openForm">Schedule Appointment</button>
            </div>
        </div>

        @if (session()->has('status'))
            <p class="clinical-notice" role="status">{{ session('status') }}</p>
        @endif

        <div class="date-navigation">
            <div>
                <small>SELECTED DATE</small>
                <strong>{{ \Carbon\Carbon::parse($selectedDate)->format('l, F d, Y') }}</strong>
            </div>
            <div class="date-buttons">
                <button type="button" wire:click="previousDay" aria-label="Previous day">‹</button>
                <button type="button" wire:click="today">Today</button>
                <button type="button" wire:click="nextDay" aria-label="Next day">›</button>
            </div>
        </div>

        <div class="appointment-stats">
            <div class="appointment-stat"><span class="stat-icon calendar-icon">▣</span><div><small>Today's Total</small><strong>{{ $totalCount }}</strong></div></div>
            <div class="appointment-stat"><span class="stat-icon pending-icon">♧</span><div><small>Pending</small><strong>{{ $pendingCount }}</strong></div></div>
            <div class="appointment-stat"><span class="stat-icon completed-icon">▦</span><div><small>Completed</small><strong>{{ $completedCount }}</strong></div></div>
        </div>

        @if ($showForm)
            <section class="directory-panel clinical-form-panel">
                <div class="panel-heading">
                    <strong>{{ $editingAppointmentId ? 'Edit Appointment' : 'Schedule Appointment' }}</strong>
                    <button class="small-outline-button" type="button" wire:click="cancelForm">Close</button>
                </div>
                <form class="clinical-form" wire:submit="saveAppointment">
                    <div class="clinical-form-grid">
                        <label>Patient
                            <select wire:model="patientId" required @disabled($editingAppointmentId !== null)>
                                <option value="">Select a patient</option>
                                @foreach ($patients as $patient)
                                    <option value="{{ $patient->id }}">{{ $patient->patient_number }} — {{ $patient->first_name }} {{ $patient->last_name }}</option>
                                @endforeach
                            </select>
                            @error('patientId') <small class="clinical-error">{{ $message }}</small> @enderror
                            @if ($editingAppointmentId !== null)
                                <small>Appointment patient cannot be changed after creation.</small>
                            @endif
                        </label>
                        <label>Date and time
                            <input type="datetime-local" wire:model="scheduledAt" required>
                            @error('scheduledAt') <small class="clinical-error">{{ $message }}</small> @enderror
                        </label>
                        <label>Provider
                            <input type="text" wire:model="provider" required maxlength="150">
                            @error('provider') <small class="clinical-error">{{ $message }}</small> @enderror
                        </label>
                        <label>Appointment type
                            <input type="text" wire:model="appointmentType" required maxlength="150">
                            @error('appointmentType') <small class="clinical-error">{{ $message }}</small> @enderror
                        </label>
                        <label>Status
                            <select wire:model="status" required>
                                <option value="pending">Pending</option>
                                <option value="scheduled">Scheduled</option>
                                <option value="in_progress">In Progress</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </label>
                        <label class="clinical-form-wide">Notes
                            <textarea wire:model="notes" rows="2" maxlength="5000"></textarea>
                            @error('notes') <small class="clinical-error">{{ $message }}</small> @enderror
                        </label>
                    </div>
                    <div class="clinical-form-actions">
                        <button class="primary-button" type="submit">Save Appointment</button>
                        <button class="small-outline-button" type="button" wire:click="cancelForm">Cancel</button>
                    </div>
                </form>
            </section>
        @endif

        <div class="filter-bar">
            <label>Patient
                <select wire:model.live="patientFilter" class="filter-select">
                    <option value="">All patients</option>
                    @foreach ($patients as $patient)
                        <option value="{{ $patient->id }}">{{ $patient->first_name }} {{ $patient->last_name }}</option>
                    @endforeach
                </select>
            </label>
            <input type="date" wire:model.live="selectedDate" class="filter-select" aria-label="Select appointment date">
            @error('selectedDate') <small class="clinical-error">{{ $message }}</small> @enderror
        </div>

        <section class="directory-panel">
            <div class="panel-heading">
                <div><strong>{{ $viewMode === 'list' ? 'Appointments' : 'Appointment Calendar' }}</strong><small>Patient-linked visits for {{ \Carbon\Carbon::parse($selectedDate)->format('F d, Y') }}</small></div>
                <span class="record-count">{{ $appointments->total() }} appointments</span>
            </div>

            @if ($viewMode === 'list')
                <div class="table-scroll">
                    <table class="health-table appointment-table">
                        <thead><tr><th>TIME</th><th>PATIENT</th><th>PROVIDER</th><th>TYPE</th><th>STATUS</th><th>LINKED DATA</th><th>ACTIONS</th></tr></thead>
                        <tbody>
                            @forelse ($appointments as $appointment)
                                <tr wire:key="appointment-{{ $appointment->id }}">
                                    <td>
                                        {{ $appointment->scheduled_at->format('h:i A') }}
                                        <br><small>{{ $appointment->appointment_code }}</small>
                                    </td>
                                    <td><a href="{{ route('patients') }}">{{ $appointment->patient->patient_number }} — {{ $appointment->patient->first_name }} {{ $appointment->patient->last_name }}</a></td>
                                    <td>{{ $appointment->provider }}</td>
                                    <td>{{ $appointment->appointment_type }}</td>
                                    <td><span class="status-pill {{ $appointment->status }}-status">{{ str($appointment->status)->replace('_', ' ')->title() }}</span></td>
                                    <td>
                                        <a href="{{ route('medical-records', ['patient' => $appointment->patient_id]) }}">Records ({{ $appointment->medical_records_count }})</a>
                                        <a href="{{ route('laboratory', ['patient' => $appointment->patient_id]) }}">Labs ({{ $appointment->lab_orders_count }})</a>
                                    </td>
                                    <td>
                                        <button class="small-outline-button" type="button" wire:click="editAppointment({{ $appointment->id }})">Edit</button>
                                        @if ($appointment->status !== 'cancelled')
                                            <button class="small-outline-button" type="button" wire:click="cancelAppointment({{ $appointment->id }})">Cancel</button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="empty-state">No appointments for this date.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @else
                <div class="calendar-list">
                    @forelse ($appointments as $appointment)
                        <div class="calendar-event" wire:key="calendar-{{ $appointment->id }}">
                            <strong>{{ $appointment->scheduled_at->format('h:i A') }}</strong>
                            <div>
                                <b>{{ $appointment->patient->first_name }} {{ $appointment->patient->last_name }}</b>
                                <small>{{ $appointment->appointment_type }} · {{ $appointment->provider }}</small>
                            </div>
                            <span class="status-pill {{ $appointment->status }}-status">{{ str($appointment->status)->replace('_', ' ')->title() }}</span>
                            <button class="small-outline-button" type="button" wire:click="editAppointment({{ $appointment->id }})">Edit</button>
                        </div>
                    @empty
                        <p class="empty-state">No appointments for this date.</p>
                    @endforelse
                </div>
            @endif

            <div class="table-footer">
                <span>Appointment records are connected to patient profiles.</span>
                {{ $appointments->links() }}
            </div>
        </section>
    </main>
</div>
