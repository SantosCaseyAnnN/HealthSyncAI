<div class="healthsync-dashboard">
    @include('livewire.partials.clinical-navigation', ['active' => 'patients'])

    <main class="main-content">
        <div class="page-heading">
            <div>
                <h2>Patients</h2>
                <p>Manage patients and their linked clinical activity.</p>
            </div>

            <button class="primary-button" type="button" wire:click="openForm">
                + Register Patient
            </button>
        </div>

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

        <section class="directory-panel">
            <div class="panel-heading">
                <div>
                    <strong>Patient Directory</strong>
                    <small>Appointments, records, and laboratory orders are linked to each patient.</small>
                </div>
                <span class="record-count">{{ $filteredPatients->total() }} patients</span>
            </div>

            <div class="table-scroll">
                <table class="health-table">
                    <thead>
                        <tr>
                            <th>Patient ID</th>
                            <th>Full name</th>
                            <th>Age / Gender</th>
                            <th>Contact</th>
                            <th>Provider</th>
                            <th>Status</th>
                            <th>Linked records</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($filteredPatients as $patient)
                            <tr wire:key="patient-{{ $patient->id }}">
                                <td>{{ $patient->patient_number }}</td>
                                <td class="patient-name">{{ $patient->first_name }} {{ $patient->last_name }}</td>
                                <td>{{ $patient->date_of_birth?->age ?? '—' }} / {{ $patient->gender ?? '—' }}</td>
                                <td>{{ $patient->phone ?? '—' }}</td>
                                <td>{{ $patient->provider ?? 'Unassigned' }}</td>
                                <td>
                                    <span class="status-pill {{ $patient->status }}-status">{{ ucfirst($patient->status) }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('appointments', ['patient' => $patient->id]) }}">Appointments {{ $patient->appointments_count }}</a>
                                    <a href="{{ route('medical-records', ['patient' => $patient->id]) }}">Records {{ $patient->medical_records_count }}</a>
                                    <a href="{{ route('laboratory', ['patient' => $patient->id]) }}">Labs {{ $patient->lab_orders_count }}</a>
                                </td>
                                <td>
                                    <button class="small-outline-button" type="button" wire:click="editPatient({{ $patient->id }})">Edit</button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="empty-state">No patient records found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="table-footer">
                <span>Showing {{ $filteredPatients->count() }} of {{ $filteredPatients->total() }} patients</span>
                {{ $filteredPatients->links() }}
            </div>
        </section>
    </main>
</div>
