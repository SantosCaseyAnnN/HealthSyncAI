<div class="healthsync-dashboard">
    @include('livewire.partials.clinical-navigation', ['active' => 'laboratory'])

    <main class="main-content">
        <div class="page-heading">
            <div>
                <h2>Laboratory</h2>
                <p>Manage patient-linked laboratory orders and results.</p>
            </div>
            <button class="primary-button" type="button" wire:click="openForm">New Lab Order</button>
        </div>

        @if (session()->has('status'))
            <p class="clinical-notice" role="status">{{ session('status') }}</p>
        @endif

        <div class="appointment-stats">
            <div class="appointment-stat"><span class="stat-icon calendar-icon">♧</span><div><small>Total Orders</small><strong>{{ $orderCount }}</strong></div></div>
            <div class="appointment-stat"><span class="stat-icon pending-icon">◷</span><div><small>Pending</small><strong>{{ $pendingCount }}</strong></div></div>
            <div class="appointment-stat"><span class="stat-icon completed-icon">▤</span><div><small>Results Available</small><strong>{{ $resultedCount }}</strong></div></div>
        </div>

        @if ($showForm)
            <section class="directory-panel clinical-form-panel">
                <div class="panel-heading">
                    <strong>{{ $editingLabOrderId ? 'Edit Lab Order' : 'Create Lab Order' }}</strong>
                    <button class="small-outline-button" type="button" wire:click="cancelForm">Close</button>
                </div>
                <form class="clinical-form" wire:submit="saveLabOrder">
                    <div class="clinical-form-grid">
                        <label>Patient
                            <select wire:model.live="patientId" required>
                                <option value="">Select a patient</option>
                                @foreach ($patients as $patient)
                                    <option value="{{ $patient->id }}">{{ $patient->patient_number }} — {{ $patient->first_name }} {{ $patient->last_name }}</option>
                                @endforeach
                            </select>
                            @error('patientId') <small class="clinical-error">{{ $message }}</small> @enderror
                        </label>
                        <label>Related appointment
                            <select wire:model="appointmentId">
                                <option value="">No linked appointment</option>
                                @foreach ($appointments as $appointment)
                                    <option value="{{ $appointment->id }}">{{ $appointment->appointment_code }} — {{ $appointment->scheduled_at->format('M d, Y h:i A') }}</option>
                                @endforeach
                            </select>
                            @error('appointmentId') <small class="clinical-error">{{ $message }}</small> @enderror
                        </label>
                        <label>Related medical record
                            <select wire:model="medicalRecordId">
                                <option value="">No linked medical record</option>
                                @foreach ($medicalRecords as $medicalRecord)
                                    <option value="{{ $medicalRecord->id }}">{{ $medicalRecord->record_number }} — {{ $medicalRecord->title }}</option>
                                @endforeach
                            </select>
                            @error('medicalRecordId') <small class="clinical-error">{{ $message }}</small> @enderror
                        </label>
                        <label>Test name
                            <input type="text" wire:model="testName" required maxlength="150">
                            @error('testName') <small class="clinical-error">{{ $message }}</small> @enderror
                        </label>
                        <label>Ordered by
                            <input type="text" wire:model="orderedBy" required maxlength="150">
                            @error('orderedBy') <small class="clinical-error">{{ $message }}</small> @enderror
                        </label>
                        <label>Order date
                            <input type="datetime-local" wire:model="orderedAt" required>
                            @error('orderedAt') <small class="clinical-error">{{ $message }}</small> @enderror
                        </label>
                        <label>Status
                            <select wire:model="status" required>
                                <option value="ordered">Ordered</option>
                                <option value="in_progress">In Progress</option>
                                <option value="resulted">Resulted</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                            @error('status') <small class="clinical-error">{{ $message }}</small> @enderror
                        </label>
                        <label>Result date
                            <input type="datetime-local" wire:model="resultAt">
                            @error('resultAt') <small class="clinical-error">{{ $message }}</small> @enderror
                        </label>
                        <label class="clinical-form-wide">Result / notes
                            <textarea wire:model="result" rows="3" maxlength="10000"></textarea>
                            @error('result') <small class="clinical-error">{{ $message }}</small> @enderror
                        </label>
                    </div>
                    <div class="clinical-form-actions">
                        <button class="primary-button" type="submit">Save Lab Order</button>
                        <button class="small-outline-button" type="button" wire:click="cancelForm">Cancel</button>
                    </div>
                </form>
            </section>
        @endif

        <div class="filter-bar">
            <div class="search-box">
                <span>⌕</span>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search patient, order ID, or test...">
            </div>
            <label>Patient
                <select wire:model.live="patientFilter" class="filter-select">
                    <option value="">All patients</option>
                    @foreach ($patients as $patient)
                        <option value="{{ $patient->id }}">{{ $patient->first_name }} {{ $patient->last_name }}</option>
                    @endforeach
                </select>
            </label>
        </div>

        <section class="directory-panel">
            <div class="panel-heading">
                <div><strong>Laboratory Orders</strong><small>Linked to patient profiles, visits, and medical records.</small></div>
                <span class="record-count">{{ $labOrders->total() }} orders</span>
            </div>
            <div class="table-scroll">
                <table class="health-table">
                    <thead><tr><th>ORDER</th><th>PATIENT</th><th>TEST</th><th>ORDERED BY</th><th>RELATED VISIT / RECORD</th><th>STATUS</th><th>RESULT</th><th>ACTIONS</th></tr></thead>
                    <tbody>
                        @forelse ($labOrders as $labOrder)
                            <tr wire:key="lab-order-{{ $labOrder->id }}">
                                <td>{{ $labOrder->order_number }}<br><small>{{ $labOrder->ordered_at->format('M d, Y') }}</small></td>
                                <td><a href="{{ route('patients') }}">{{ $labOrder->patient->first_name }} {{ $labOrder->patient->last_name }}</a><br><small>{{ $labOrder->patient->patient_number }}</small></td>
                                <td>{{ $labOrder->test_name }}</td>
                                <td>{{ $labOrder->ordered_by }}</td>
                                <td>
                                    @if ($labOrder->appointment)
                                        <a href="{{ route('appointments', ['patient' => $labOrder->patient_id]) }}">{{ $labOrder->appointment->appointment_code }}</a>
                                    @endif
                                    @if ($labOrder->medicalRecord)
                                        <a href="{{ route('medical-records', ['patient' => $labOrder->patient_id]) }}">{{ $labOrder->medicalRecord->record_number }}</a>
                                    @endif
                                    @if (! $labOrder->appointment && ! $labOrder->medicalRecord)
                                        —
                                    @endif
                                </td>
                                <td><span class="status-pill {{ $labOrder->status }}-status">{{ str($labOrder->status)->replace('_', ' ')->title() }}</span></td>
                                <td>{{ $labOrder->result ? str($labOrder->result)->limit(80) : 'Pending' }}</td>
                                <td><button class="small-outline-button" type="button" wire:click="editLabOrder({{ $labOrder->id }})">Edit</button></td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="empty-state">No laboratory orders found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="table-footer">
                <span>Lab orders and results are stored with their patient relationships.</span>
                {{ $labOrders->links() }}
            </div>
        </section>
    </main>
</div>
