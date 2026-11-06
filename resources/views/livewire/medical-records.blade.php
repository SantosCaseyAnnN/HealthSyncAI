<div class="healthsync-dashboard">
    @include('livewire.partials.clinical-navigation', ['active' => 'medical-records'])

    <main class="main-content medical-content">
        <div class="page-heading">
            <div>
                <h2>Medical Records</h2>
                <p>Browse and manage patient-linked electronic health records.</p>
            </div>
            <div class="heading-actions">
                <span class="record-count">{{ $recordCount }} records</span>
                <button class="primary-button" type="button" wire:click="openForm">Add Medical Record</button>
            </div>
        </div>

        @if (session()->has('status'))
            <p class="clinical-notice" role="status">{{ session('status') }}</p>
        @endif

        @if ($showForm)
            <section class="directory-panel clinical-form-panel">
                <div class="panel-heading">
                    <strong>{{ $editingRecordId ? 'Edit Medical Record' : 'New Medical Record' }}</strong>
                    <button class="small-outline-button" type="button" wire:click="cancelForm">Close</button>
                </div>
                <form class="clinical-form" wire:submit="saveRecord">
                    <div class="clinical-form-grid">
                        <label>Patient
                            <select wire:model.live="patientId" required @disabled($editingRecordId !== null)>
                                <option value="">Select a patient</option>
                                @foreach ($patients as $patient)
                                    <option value="{{ $patient->id }}">{{ $patient->patient_number }} — {{ $patient->first_name }} {{ $patient->last_name }}</option>
                                @endforeach
                            </select>
                            @error('patientId') <small class="clinical-error">{{ $message }}</small> @enderror
                            @if ($editingRecordId !== null)
                                <small>Record patient cannot be changed after creation.</small>
                            @endif
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
                        <label>Record title
                            <input type="text" wire:model="recordTitle" required maxlength="150">
                            @error('recordTitle') <small class="clinical-error">{{ $message }}</small> @enderror
                        </label>
                        <label>Provider
                            <input type="text" wire:model="provider" required maxlength="150">
                            @error('provider') <small class="clinical-error">{{ $message }}</small> @enderror
                        </label>
                        <label>Recorded at
                            <input type="datetime-local" wire:model="recordedAt" required>
                            @error('recordedAt') <small class="clinical-error">{{ $message }}</small> @enderror
                        </label>
                        <label class="clinical-form-wide">Subjective
                            <textarea wire:model="subjective" rows="3" required maxlength="10000"></textarea>
                            @error('subjective') <small class="clinical-error">{{ $message }}</small> @enderror
                        </label>
                        <label class="clinical-form-wide">Objective
                            <textarea wire:model="objective" rows="3" required maxlength="10000"></textarea>
                            @error('objective') <small class="clinical-error">{{ $message }}</small> @enderror
                        </label>
                        <label class="clinical-form-wide">Assessment
                            <textarea wire:model="assessment" rows="3" required maxlength="10000"></textarea>
                            @error('assessment') <small class="clinical-error">{{ $message }}</small> @enderror
                        </label>
                        <label class="clinical-form-wide">Plan
                            <textarea wire:model="plan" rows="3" required maxlength="10000"></textarea>
                            @error('plan') <small class="clinical-error">{{ $message }}</small> @enderror
                        </label>
                    </div>
                    <div class="clinical-form-actions">
                        <button class="primary-button" type="submit">Save Record</button>
                        <button class="small-outline-button" type="button" wire:click="cancelForm">Cancel</button>
                    </div>
                </form>
            </section>
        @endif

        <div class="filter-bar">
            <div class="search-box">
                <span>⌕</span>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search patient, record number, or title...">
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

        <div class="records-layout">
            <section class="patient-directory">
                <div class="medical-panel-heading">
                    <strong>Patient Records</strong>
                    <small>{{ $filteredRecords->total() }} matching records</small>
                </div>
                <div class="patient-list">
                    @forelse ($filteredRecords as $record)
                        <button
                            type="button"
                            wire:key="medical-record-{{ $record->id }}"
                            wire:click="selectRecord({{ $record->id }})"
                            @class(['record-patient', 'selected-record' => $selectedRecordId === $record->id])
                        >
                            <span class="record-avatar">♙</span>
                            <span class="record-patient-info">
                                <span class="record-patient-top">
                                    <strong>{{ $record->patient->first_name }} {{ $record->patient->last_name }}</strong>
                                    <small>{{ $record->record_number }}</small>
                                </span>
                                <span class="record-updated">{{ $record->title }} · {{ $record->recorded_at->format('M d, Y h:i A') }}</span>
                                <span class="record-activity">{{ $record->appointment?->appointment_code ?? 'No linked appointment' }}</span>
                            </span>
                        </button>
                    @empty
                        <div class="no-records">No medical records found.</div>
                    @endforelse
                </div>
                <div class="table-footer">{{ $filteredRecords->links() }}</div>
            </section>

            @if ($selectedRecord)
                <section class="record-preview">
                    <div class="record-preview-header">
                        <div class="preview-patient">
                            <span class="preview-avatar">♙</span>
                            <div>
                                <div class="preview-name">
                                    <strong>{{ $selectedRecord->patient->first_name }} {{ $selectedRecord->patient->last_name }}</strong>
                                    <small>{{ $selectedRecord->patient->patient_number }}</small>
                                </div>
                                <p>{{ $selectedRecord->title }} · {{ $selectedRecord->record_number }}</p>
                            </div>
                        </div>
                        <button class="open-record-button" type="button" wire:click="editRecord({{ $selectedRecord->id }})">Edit Record</button>
                    </div>

                    <div class="patient-details">
                        <div><small>AGE</small><strong>{{ $selectedRecord->patient->date_of_birth?->age ?? '—' }}</strong></div>
                        <div><small>SEX</small><strong>{{ $selectedRecord->patient->gender ?? '—' }}</strong></div>
                        <div><small>BLOOD TYPE</small><strong>{{ $selectedRecord->patient->blood_type ?? '—' }}</strong></div>
                        <div><small>ALLERGIES</small><strong class="allergy-value">{{ $selectedRecord->patient->allergies ?: 'None recorded' }}</strong></div>
                    </div>

                    <div class="latest-activity">
                        <small>PROVIDER · {{ $selectedRecord->recorded_at->format('M d, Y h:i A') }}</small>
                        <strong>{{ $selectedRecord->provider }}</strong>
                        @if ($selectedRecord->appointment)
                            <a href="{{ route('appointments', ['patient' => $selectedRecord->patient_id]) }}">Appointment {{ $selectedRecord->appointment->appointment_code }}</a>
                        @endif
                    </div>

                    <div class="clinical-notes">
                        <div class="clinical-heading">
                            <span class="notes-icon">▤</span>
                            <div><strong>Clinical Notes</strong><small>SOAP documentation</small></div>
                        </div>
                        <div class="soap-grid">
                            <div class="soap-card"><div class="soap-label"><span>1</span> SUBJECTIVE</div><p>{{ $selectedRecord->subjective }}</p></div>
                            <div class="soap-card"><div class="soap-label"><span>2</span> OBJECTIVE</div><p>{{ $selectedRecord->objective }}</p></div>
                            <div class="soap-card"><div class="soap-label"><span>3</span> ASSESSMENT</div><p>{{ $selectedRecord->assessment }}</p></div>
                            <div class="soap-card"><div class="soap-label"><span>4</span> PLAN</div><p>{{ $selectedRecord->plan }}</p></div>
                        </div>
                    </div>

                    <div class="attached-documents">
                        <div class="documents-heading">
                            <strong>Related Laboratory Orders</strong>
                            <a href="{{ route('laboratory', ['patient' => $selectedRecord->patient_id]) }}">Open laboratory</a>
                        </div>
                        @forelse ($selectedRecord->labOrders as $labOrder)
                            <div class="document-item">
                                <span class="pdf-icon">▤</span>
                                <div><strong>{{ $labOrder->test_name }} · {{ $labOrder->order_number }}</strong><small>{{ str($labOrder->status)->replace('_', ' ')->title() }}</small></div>
                            </div>
                        @empty
                            <p class="schedule-note">No laboratory orders are linked to this record.</p>
                        @endforelse
                    </div>
                </section>
            @else
                <section class="record-preview no-selection"><p>Select a record to view its patient and clinical details.</p></section>
            @endif
        </div>
    </main>
</div>
