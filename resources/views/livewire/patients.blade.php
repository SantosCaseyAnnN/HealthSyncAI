<div class="healthsync-dashboard">
    @include('livewire.partials.clinical-navigation', ['active' => 'patients'])

    <main class="main-content">
        @include('livewire.partials.workspace-topbar', ['section' => 'Clinic / Patient records'])

        <div class="page-heading">
            <div>
                <h2>Patient records</h2>
                <p>Manage patients and their linked clinical activity.</p>
            </div>

            <button class="primary-button" type="button" wire:click="openForm">
                + Register Patient
            </button>
        </div>

        @if (app()->environment('local', 'testing'))
            <p class="clinical-notice" role="note">Demo/testing mode: use synthetic records only. Do not enter real patient information.</p>
        @endif

        @if (session()->has('status'))
            <p class="clinical-notice" role="status">{{ session('status') }}</p>
        @endif

        @if ($showForm)
            <section class="directory-panel clinical-form-panel">
                <div class="panel-heading">
                    <strong>{{ $editingPatientId ? 'Edit Patient' : 'Register Patient' }}</strong>
                    <button class="small-outline-button" type="button" wire:click="cancelForm">Close</button>
                </div>

                <form class="clinical-form" wire:submit="savePatient">
                    <div class="clinical-form-grid">
                        <label>First name
                            <input type="text" wire:model="firstName" required maxlength="100">
                            @error('firstName') <small class="clinical-error">{{ $message }}</small> @enderror
                        </label>
                        <label>Last name
                            <input type="text" wire:model="lastName" required maxlength="100">
                            @error('lastName') <small class="clinical-error">{{ $message }}</small> @enderror
                        </label>
                        <label>Date of birth
                            <input type="date" wire:model="dateOfBirth">
                            @error('dateOfBirth') <small class="clinical-error">{{ $message }}</small> @enderror
                        </label>
                        <label>Gender
                            <select wire:model="genderInput">
                                <option value="">Not specified</option>
                                <option value="Female">Female</option>
                                <option value="Male">Male</option>
                                <option value="Other">Other</option>
                            </select>
                            @error('genderInput') <small class="clinical-error">{{ $message }}</small> @enderror
                        </label>
                        <label>Phone
                            <input type="text" wire:model="phone" maxlength="30">
                            @error('phone') <small class="clinical-error">{{ $message }}</small> @enderror
                        </label>
                        <label>Email
                            <input type="email" wire:model="email" maxlength="255">
                            @error('email') <small class="clinical-error">{{ $message }}</small> @enderror
                        </label>
                        <label class="clinical-form-wide">Address
                            <input type="text" wire:model="address" maxlength="255">
                            @error('address') <small class="clinical-error">{{ $message }}</small> @enderror
                        </label>
                        <label>Assigned provider
                            <input type="text" wire:model="provider" maxlength="150">
                            @error('provider') <small class="clinical-error">{{ $message }}</small> @enderror
                        </label>
                        <label>Status
                            <select wire:model="patientStatus" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </label>
                        <label>Blood type
                            <select wire:model="bloodType">
                                <option value="">Not recorded</option>
                                @foreach (['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bloodTypeOption)
                                    <option value="{{ $bloodTypeOption }}">{{ $bloodTypeOption }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="clinical-form-wide">Allergies
                            <textarea wire:model="allergies" rows="2" maxlength="5000"></textarea>
                            @error('allergies') <small class="clinical-error">{{ $message }}</small> @enderror
                        </label>
                        <label class="clinical-form-wide">Medical history
                            <textarea wire:model="medicalHistory" rows="3" maxlength="10000"></textarea>
                            @error('medicalHistory') <small class="clinical-error">{{ $message }}</small> @enderror
                        </label>
                        <label class="clinical-form-wide">Family history
                            <textarea wire:model="familyHistory" rows="3" maxlength="10000"></textarea>
                            @error('familyHistory') <small class="clinical-error">{{ $message }}</small> @enderror
                        </label>
                        <label>Emergency contact name
                            <input type="text" wire:model="emergencyContactName" maxlength="150">
                            @error('emergencyContactName') <small class="clinical-error">{{ $message }}</small> @enderror
                        </label>
                        <label>Relationship
                            <input type="text" wire:model="emergencyContactRelationship" maxlength="100">
                            @error('emergencyContactRelationship') <small class="clinical-error">{{ $message }}</small> @enderror
                        </label>
                        <label>Emergency contact phone
                            <input type="text" wire:model="emergencyContactPhone" maxlength="30">
                            @error('emergencyContactPhone') <small class="clinical-error">{{ $message }}</small> @enderror
                        </label>
                        <label>Height (cm)
                            <input type="number" step="0.1" min="30" max="250" wire:model="heightCm">
                            @error('heightCm') <small class="clinical-error">{{ $message }}</small> @enderror
                        </label>
                        <label>Weight (kg)
                            <input type="number" step="0.1" min="1" max="500" wire:model="weightKg">
                            @error('weightKg') <small class="clinical-error">{{ $message }}</small> @enderror
                        </label>
                        <label>BMI
                            <input type="number" step="0.01" min="5" max="100" wire:model="bmi">
                            @error('bmi') <small class="clinical-error">{{ $message }}</small> @enderror
                        </label>
                        <label>Blood pressure
                            <input type="text" maxlength="20" placeholder="120/80" wire:model="bloodPressure">
                            @error('bloodPressure') <small class="clinical-error">{{ $message }}</small> @enderror
                        </label>
                        <label>Temperature (C)
                            <input type="number" step="0.1" min="25" max="45" wire:model="temperatureC">
                            @error('temperatureC') <small class="clinical-error">{{ $message }}</small> @enderror
                        </label>
                        <label>Pulse (bpm)
                            <input type="number" min="20" max="300" wire:model="pulseRate">
                            @error('pulseRate') <small class="clinical-error">{{ $message }}</small> @enderror
                        </label>
                        <label>Respiratory rate
                            <input type="number" min="4" max="80" wire:model="respiratoryRate">
                            @error('respiratoryRate') <small class="clinical-error">{{ $message }}</small> @enderror
                        </label>
                        <label>Oxygen saturation (%)
                            <input type="number" step="0.1" min="50" max="100" wire:model="oxygenSaturation">
                            @error('oxygenSaturation') <small class="clinical-error">{{ $message }}</small> @enderror
                        </label>
                    </div>
                    <div class="clinical-form-actions">
                        <button class="primary-button" type="submit">Save Patient</button>
                        <button class="small-outline-button" type="button" wire:click="cancelForm">Cancel</button>
                    </div>
                </form>
            </section>
        @endif

        <div class="filter-bar">
            <div class="search-box">
                <span>⌕</span>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search patients by name, ID, or phone...">
            </div>
            <select wire:model.live="status" class="filter-select">
                <option value="">Status: All</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
            <select wire:model.live="gender" class="filter-select">
                <option value="">Gender: All</option>
                <option value="Female">Female</option>
                <option value="Male">Male</option>
                <option value="Other">Other</option>
            </select>
        </div>

        <div class="patient-records-grid">
            <section class="directory-panel">
                <div class="panel-heading">
                    <div>
                        <strong>Patient Directory</strong>
                        <small>Search, review, and maintain clinic patient records.</small>
                    </div>
                    <span class="record-count">{{ $filteredPatients->total() }} patients</span>
                </div>

                <div class="table-scroll">
                    <table class="health-table">
                        <thead>
                            <tr>
                                <th>Patient / Record no.</th>
                                <th>Age / Gender</th>
                                <th>Last updated</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($filteredPatients as $patient)
                                <tr wire:key="patient-{{ $patient->id }}" @class(['selected-patient-row' => $selectedPatient?->id === $patient->id])>
                                    <td>
                                        <button class="patient-select-button" type="button" wire:click="selectPatient({{ $patient->id }})">
                                            <strong>{{ $patient->first_name }} {{ $patient->last_name }}</strong>
                                            <small>{{ $patient->patient_number }}</small>
                                        </button>
                                    </td>
                                    <td>{{ $patient->date_of_birth?->age ?? '—' }} / {{ $patient->gender ?? '—' }}</td>
                                    <td>{{ $patient->updated_at->format('d M Y') }}</td>
                                    <td><span class="status-pill {{ $patient->status }}-status">{{ ucfirst($patient->status) }}</span></td>
                                    <td>
                                        <a class="small-outline-button" href="{{ route('patients.show', $patient) }}">View</a>
                                        <button class="small-outline-button" type="button" wire:click="editPatient({{ $patient->id }})">Edit</button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="empty-state">No patient records found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="table-footer">
                    <span>Showing {{ $filteredPatients->count() }} of {{ $filteredPatients->total() }} patients</span>
                    {{ $filteredPatients->links() }}
                </div>
            </section>

            <aside class="patient-summary-panel">
                @if ($selectedPatient)
                    <div class="patient-summary-header">
                        <p>Patient summary</p>
                        <span class="status-pill {{ $selectedPatient->status }}-status">{{ ucfirst($selectedPatient->status) }}</span>
                        <h3>{{ $selectedPatient->first_name }} {{ $selectedPatient->last_name }}</h3>
                        <small>{{ $selectedPatient->patient_number }} · Updated {{ $selectedPatient->updated_at->format('d M Y') }}</small>
                    </div>

                    <dl class="patient-summary-list">
                        <div><dt>Date of birth</dt><dd>{{ $selectedPatient->date_of_birth?->format('d M Y') ?? 'Not recorded' }}{{ $selectedPatient->date_of_birth ? ' · '.$selectedPatient->date_of_birth->age.' years' : '' }}</dd></div>
                        <div><dt>Gender</dt><dd>{{ $selectedPatient->gender ?? 'Not recorded' }}</dd></div>
                        <div><dt>Contact number</dt><dd>{{ $selectedPatient->phone ?? 'Not recorded' }}</dd></div>
                        <div><dt>Address</dt><dd>{{ $selectedPatient->address ?? 'Not recorded' }}</dd></div>
                        <div><dt>Blood type</dt><dd>{{ $selectedPatient->blood_type ?? 'Not recorded' }}</dd></div>
                    </dl>

                    <div class="patient-vitals-grid">
                        <div><small>HEIGHT</small><strong>{{ $selectedPatient->height_cm !== null ? $selectedPatient->height_cm.' cm' : '—' }}</strong></div>
                        <div><small>WEIGHT</small><strong>{{ $selectedPatient->weight_kg !== null ? $selectedPatient->weight_kg.' kg' : '—' }}</strong></div>
                        <div><small>BLOOD PRESSURE</small><strong>{{ $selectedPatient->blood_pressure ?? '—' }}</strong></div>
                        <div><small>TEMPERATURE</small><strong>{{ $selectedPatient->temperature_c !== null ? $selectedPatient->temperature_c.' °C' : '—' }}</strong></div>
                    </div>

                    <div class="patient-summary-actions">
                        <button class="primary-button" type="button" wire:click="editPatient({{ $selectedPatient->id }})">Edit patient information</button>
                        <a class="small-outline-button" href="{{ route('patients.show', $selectedPatient) }}">View full profile</a>
                    </div>

                    <div class="patient-summary-links">
                        <a href="{{ route('appointments', ['patient' => $selectedPatient->id]) }}">Appointments <span>{{ $selectedPatient->appointments_count }}</span></a>
                        <a href="{{ route('medical-records', ['patient' => $selectedPatient->id]) }}">Medical records <span>{{ $selectedPatient->medical_records_count }}</span></a>
                        <a href="{{ route('laboratory', ['patient' => $selectedPatient->id]) }}">Laboratory orders <span>{{ $selectedPatient->lab_orders_count }}</span></a>
                    </div>

                    <p class="patient-summary-footnote">Access restricted to authorized clinic personnel.</p>
                @else
                    <p class="empty-state">Select a patient to review their summary.</p>
                @endif
            </aside>
        </div>
    </main>
</div>
