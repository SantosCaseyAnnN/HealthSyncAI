<div class="healthsync-dashboard">
    @include('livewire.partials.clinical-navigation', ['active' => 'prescriptions'])
    <main class="main-content">
        @include('livewire.partials.workspace-topbar', ['section' => 'Clinic / Prescription records'])

        <div class="page-heading">
            <div><h2>Prescription records</h2><p>Authorized clinic workspace for maintaining patient-linked prescription records.</p></div>
            <button type="button" class="primary-button" wire:click="openForm">Add prescription</button>
        </div>
        @if (session()->has('status')) <p class="clinical-notice" role="status">{{ session('status') }}</p> @endif
        @if ($showForm)
            <section class="directory-panel clinical-form-panel">
                <div class="panel-heading"><strong>{{ $editingPrescriptionId ? 'Edit prescription' : 'New prescription record' }}</strong><button type="button" wire:click="cancelForm" class="small-outline-button">Close</button></div>
                <form wire:submit="savePrescription" class="clinical-form">
                    <div class="clinical-form-grid">
                        <label>Patient
                            <select wire:model.live="patientId" required><option value="">Select patient</option>@foreach ($patients as $patient)<option value="{{ $patient->id }}">{{ $patient->patient_number }} — {{ $patient->first_name }} {{ $patient->last_name }}</option>@endforeach</select>
                            @error('patientId') <small class="clinical-error">{{ $message }}</small> @enderror
                        </label>
                        <label>Related appointment
                            <select wire:model="appointmentId"><option value="">No appointment linked</option>@foreach ($appointments as $appointment)<option value="{{ $appointment->id }}">{{ $appointment->appointment_code }} — {{ $appointment->scheduled_at->format('M d, Y h:i A') }}</option>@endforeach</select>
                            @error('appointmentId') <small class="clinical-error">{{ $message }}</small> @enderror
                        </label>
                        <label>Medication<input wire:model="medication" maxlength="150" required>@error('medication') <small class="clinical-error">{{ $message }}</small> @enderror</label>
                        <label>Strength<input wire:model="strength" maxlength="100">@error('strength') <small class="clinical-error">{{ $message }}</small> @enderror</label>
                        <label>Dosage<input wire:model="dosage" maxlength="150" required>@error('dosage') <small class="clinical-error">{{ $message }}</small> @enderror</label>
                        <label>Route<input wire:model="prescriptionRoute" maxlength="50" placeholder="e.g. oral">@error('prescriptionRoute') <small class="clinical-error">{{ $message }}</small> @enderror</label>
                        <label>Frequency<input wire:model="frequency" maxlength="150" required>@error('frequency') <small class="clinical-error">{{ $message }}</small> @enderror</label>
                        <label>Duration<input wire:model="duration" maxlength="100">@error('duration') <small class="clinical-error">{{ $message }}</small> @enderror</label>
                        <label>Issued at<input type="datetime-local" wire:model="issuedAt" required>@error('issuedAt') <small class="clinical-error">{{ $message }}</small> @enderror</label>
                        <label>Status<select wire:model="status"><option value="active">Active</option><option value="discontinued">Discontinued</option></select></label>
                        <label class="clinical-form-wide">Instructions<textarea wire:model="instructions" rows="3" maxlength="5000"></textarea>@error('instructions') <small class="clinical-error">{{ $message }}</small> @enderror</label>
                    </div>
                    <div class="clinical-form-actions"><button type="submit" class="primary-button">Save prescription</button><button type="button" wire:click="cancelForm" class="small-outline-button">Cancel</button></div>
                </form>
            </section>
        @endif
        <div class="filter-bar"><input wire:model.live.debounce.300ms="search" type="search" class="filter-select" placeholder="Search medication or patient"></div>
        <section class="directory-panel">
            <div class="panel-heading"><div><strong>Prescription records</strong><small>Access restricted to approved clinic staff.</small></div><span class="record-count">{{ $prescriptions->total() }} records</span></div>
            <div class="table-scroll">
                <table class="health-table">
                    <thead><tr><th>ISSUED</th><th>PATIENT</th><th>MEDICATION</th><th>DIRECTIONS</th><th>STATUS</th><th>PRESCRIBED BY</th><th>ACTIONS</th></tr></thead>
                    <tbody>
                        @forelse ($prescriptions as $prescription)
                            <tr wire:key="prescription-{{ $prescription->id }}">
                                <td>{{ $prescription->issued_at->format('M d, Y') }}</td>
                                <td>{{ $prescription->patient->patient_number }} — {{ $prescription->patient->first_name }} {{ $prescription->patient->last_name }}</td>
                                <td><strong>{{ $prescription->medication }}</strong><small class="block">{{ $prescription->strength }}</small></td>
                                <td>{{ $prescription->dosage }} · {{ $prescription->route }} · {{ $prescription->frequency }} · {{ $prescription->duration }}</td>
                                <td><span class="status-pill {{ $prescription->status }}-status">{{ str($prescription->status)->title() }}</span></td>
                                <td>{{ $prescription->prescribedBy->name }}</td>
                                <td><div class="flex flex-wrap gap-2"><button type="button" wire:click="editPrescription({{ $prescription->id }})" class="small-outline-button">Edit</button>@if ($prescription->status === 'active') <button type="button" wire:click="discontinuePrescription({{ $prescription->id }})" class="small-outline-button">Discontinue</button>@endif</div></td>
                            </tr>
                        @empty
                            <tr><td colspan="7">No prescription records found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="pagination-wrap">{{ $prescriptions->links() }}</div>
        </section>
    </main>
</div>
