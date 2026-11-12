<div class="healthsync-dashboard">
    @include('livewire.partials.clinical-navigation', ['active' => 'patients'])

    <main class="main-content">
        <div class="page-heading">
            <div>
                <h2>EHR Profile</h2>
                <p>{{ $profilePatient->patient_number }} · {{ $profilePatient->first_name }} {{ $profilePatient->last_name }}</p>
            </div>
            <a class="small-outline-button" href="{{ route('patients') }}">Back to Patients</a>
        </div>

        @if (app()->environment('local', 'testing'))
            <p class="clinical-notice" role="note">Demo/testing mode: use synthetic records only. Consent entries here are test records, not electronic signatures.</p>
        @endif

        @if (session()->has('status'))
            <p class="clinical-notice" role="status">{{ session('status') }}</p>
        @endif

        <section class="directory-panel">
            <div class="panel-heading">
                <div>
                    <strong>Patient information</strong>
                    <small>Linked appointments {{ $profilePatient->appointments_count }} · records {{ $profilePatient->medical_records_count }} · labs {{ $profilePatient->lab_orders_count }}</small>
                </div>
                <a href="{{ route('patients.qr', $profilePatient) }}" target="_blank" rel="noopener">Open patient QR</a>
            </div>
            <div class="clinical-form">
                <img
                    src="{{ route('patients.qr', $profilePatient) }}"
                    width="160"
                    height="160"
                    alt="QR code for patient {{ $profilePatient->patient_number }}"
                >
                <p>Scanning the code opens this profile and requires a signed-in account. The QR contains only the profile URL.</p>
                <div class="clinical-form-grid">
                    <div><strong>Date of birth</strong><p>{{ $profilePatient->date_of_birth?->format('M j, Y') ?? 'Not recorded' }}</p></div>
                    <div><strong>Gender</strong><p>{{ $profilePatient->gender ?? 'Not recorded' }}</p></div>
                    <div><strong>Blood type</strong><p>{{ $profilePatient->blood_type ?? 'Not recorded' }}</p></div>
                    <div><strong>Allergies</strong><p>{{ $profilePatient->allergies ?? 'None recorded' }}</p></div>
                    <div><strong>Medical history</strong><p>{{ $profilePatient->medical_history ?? 'Not recorded' }}</p></div>
                    <div><strong>Family history</strong><p>{{ $profilePatient->family_history ?? 'Not recorded' }}</p></div>
                    <div><strong>Emergency contact</strong><p>{{ $profilePatient->emergency_contact_name ?? 'Not recorded' }}{{ $profilePatient->emergency_contact_relationship ? ' · '.$profilePatient->emergency_contact_relationship : '' }}</p></div>
                    <div><strong>Emergency phone</strong><p>{{ $profilePatient->emergency_contact_phone ?? 'Not recorded' }}</p></div>
                </div>
            </div>
        </section>

        <section class="directory-panel">
            <div class="panel-heading">
                <div>
                    <strong>Vaccination records</strong>
                    <small>Record and review synthetic vaccination data in this test environment.</small>
                </div>
                <span class="record-count">{{ $profilePatient->vaccinations->count() }} records</span>
            </div>
            <form class="clinical-form" wire:submit="recordVaccination">
                <div class="clinical-form-grid">
                    <label>Vaccine
                        <input type="text" wire:model="vaccineName" maxlength="150" required>
                        @error('vaccineName') <small class="clinical-error">{{ $message }}</small> @enderror
                    </label>
                    <label>Dose
                        <input type="text" wire:model="vaccineDose" maxlength="100">
                        @error('vaccineDose') <small class="clinical-error">{{ $message }}</small> @enderror
                    </label>
                    <label>Date administered
                        <input type="date" wire:model="vaccineAdministeredAt" required>
                        @error('vaccineAdministeredAt') <small class="clinical-error">{{ $message }}</small> @enderror
                    </label>
                    <label>Administered by
                        <input type="text" wire:model="vaccineAdministeredBy" maxlength="150">
                        @error('vaccineAdministeredBy') <small class="clinical-error">{{ $message }}</small> @enderror
                    </label>
                    <label>Lot number
                        <input type="text" wire:model="vaccineLotNumber" maxlength="100">
                        @error('vaccineLotNumber') <small class="clinical-error">{{ $message }}</small> @enderror
                    </label>
                    <label class="clinical-form-wide">Notes
                        <textarea wire:model="vaccineNotes" rows="2" maxlength="5000"></textarea>
                        @error('vaccineNotes') <small class="clinical-error">{{ $message }}</small> @enderror
                    </label>
                </div>
                <div class="clinical-form-actions">
                    <button class="primary-button" type="submit">Add vaccination</button>
                </div>
            </form>
            <div class="clinical-form">
                @forelse ($profilePatient->vaccinations as $vaccination)
                    <p wire:key="vaccination-{{ $vaccination->id }}">
                        <strong>{{ $vaccination->vaccine_name }}</strong>
                        {{ $vaccination->dose ? ' · '.$vaccination->dose : '' }}
                        · {{ $vaccination->administered_at->format('M j, Y') }}
                        {{ $vaccination->administered_by ? ' · '.$vaccination->administered_by : '' }}
                        {{ $vaccination->lot_number ? ' · Lot '.$vaccination->lot_number : '' }}
                        {{ $vaccination->recordedBy ? ' · Recorded by '.$vaccination->recordedBy->name : '' }}
                        {{ $vaccination->notes ? ' · '.$vaccination->notes : '' }}
                    </p>
                @empty
                    <p>No vaccination records yet.</p>
                @endforelse
            </div>
        </section>

        <section class="directory-panel">
            <div class="panel-heading">
                <div>
                    <strong>Consent decision history</strong>
                    <small>Prototype tracking only; no signed document or legally binding signature is captured.</small>
                </div>
                <span class="record-count">{{ $profilePatient->consents->count() }} records</span>
            </div>
            <form class="clinical-form" wire:submit="recordConsent">
                <div class="clinical-form-grid">
                    <label>Consent type
                        <input type="text" wire:model="consentType" maxlength="150" required>
                        @error('consentType') <small class="clinical-error">{{ $message }}</small> @enderror
                    </label>
                    <label>Form version
                        <input type="text" wire:model="consentVersion" maxlength="50" required>
                        @error('consentVersion') <small class="clinical-error">{{ $message }}</small> @enderror
                    </label>
                    <label>Decision
                        <select wire:model="consentStatus" required>
                            <option value="granted">Granted</option>
                            <option value="declined">Declined</option>
                            <option value="revoked">Revoked</option>
                        </select>
                        @error('consentStatus') <small class="clinical-error">{{ $message }}</small> @enderror
                    </label>
                    <label>Recorded at
                        <input type="datetime-local" wire:model="consentRecordedAt" required>
                        @error('consentRecordedAt') <small class="clinical-error">{{ $message }}</small> @enderror
                    </label>
                    <label class="clinical-form-wide">Notes
                        <textarea wire:model="consentNotes" rows="2" maxlength="5000"></textarea>
                        @error('consentNotes') <small class="clinical-error">{{ $message }}</small> @enderror
                    </label>
                </div>
                <div class="clinical-form-actions">
                    <button class="primary-button" type="submit">Record consent decision</button>
                </div>
            </form>
            <div class="clinical-form">
                @forelse ($profilePatient->consents as $consent)
                    <p wire:key="consent-{{ $consent->id }}">
                        <strong>{{ $consent->consent_type }}</strong>
                        · {{ ucfirst($consent->status) }}
                        · Version {{ $consent->form_version }}
                        · {{ $consent->recorded_at->format('M j, Y g:i A') }}
                        · Recorded by {{ $consent->recordedBy?->name ?? 'Demo/system' }}
                        {{ $consent->notes ? ' · '.$consent->notes : '' }}
                    </p>
                @empty
                    <p>No consent decisions recorded.</p>
                @endforelse
            </div>
        </section>
    </main>
</div>
