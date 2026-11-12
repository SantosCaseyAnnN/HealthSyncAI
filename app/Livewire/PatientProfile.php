<?php

namespace App\Livewire;

use App\Models\Patient;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Component;

class PatientProfile extends Component
{
    public Patient $patient;

    public string $vaccineName = '';

    public string $vaccineDose = '';

    public string $vaccineAdministeredAt = '';

    public string $vaccineAdministeredBy = '';

    public string $vaccineLotNumber = '';

    public string $vaccineNotes = '';

    public string $consentType = '';

    public string $consentVersion = 'demo-1';

    public string $consentStatus = 'granted';

    public string $consentRecordedAt = '';

    public string $consentNotes = '';

    public function mount(Patient $patient): void
    {
        $this->patient = $patient;
        $this->vaccineAdministeredAt = now()->toDateString();
        $this->consentRecordedAt = now()->format('Y-m-d\TH:i');
    }

    public function recordVaccination(): void
    {
        $validated = $this->validate([
            'vaccineName' => ['required', 'string', 'max:150'],
            'vaccineDose' => ['nullable', 'string', 'max:100'],
            'vaccineAdministeredAt' => ['required', 'date', 'before_or_equal:today'],
            'vaccineAdministeredBy' => ['nullable', 'string', 'max:150'],
            'vaccineLotNumber' => ['nullable', 'string', 'max:100'],
            'vaccineNotes' => ['nullable', 'string', 'max:5000'],
        ]);

        $this->patient->vaccinations()->create([
            'vaccine_name' => $validated['vaccineName'],
            'dose' => $validated['vaccineDose'] ?: null,
            'administered_at' => $validated['vaccineAdministeredAt'],
            'administered_by' => $validated['vaccineAdministeredBy'] ?: null,
            'lot_number' => $validated['vaccineLotNumber'] ?: null,
            'notes' => $validated['vaccineNotes'] ?: null,
            'recorded_by_user_id' => auth()->id(),
        ]);

        session()->flash('status', 'Vaccination record was added.');
        $this->resetVaccinationForm();
    }

    public function recordConsent(): void
    {
        $validated = $this->validate([
            'consentType' => ['required', 'string', 'max:150'],
            'consentVersion' => ['required', 'string', 'max:50'],
            'consentStatus' => ['required', Rule::in(['granted', 'declined', 'revoked'])],
            'consentRecordedAt' => ['required', 'date'],
            'consentNotes' => ['nullable', 'string', 'max:5000'],
        ]);

        $this->patient->consents()->create([
            'consent_type' => $validated['consentType'],
            'form_version' => $validated['consentVersion'],
            'status' => $validated['consentStatus'],
            'recorded_at' => $validated['consentRecordedAt'],
            'recorded_by_user_id' => auth()->id(),
            'notes' => $validated['consentNotes'] ?: null,
        ]);

        session()->flash('status', 'Consent decision was recorded.');
        $this->resetConsentForm();
    }

    public function render(): View
    {
        $profilePatient = Patient::query()
            ->with([
                'vaccinations' => fn ($query) => $query->orderByDesc('administered_at')->orderByDesc('id'),
                'vaccinations.recordedBy',
                'consents' => fn ($query) => $query->orderByDesc('recorded_at')->orderByDesc('id'),
                'consents.recordedBy',
            ])
            ->withCount(['appointments', 'medicalRecords', 'labOrders'])
            ->findOrFail($this->patient->getKey());

        return view('livewire.patient-profile', [
            'profilePatient' => $profilePatient,
        ]);
    }

    private function resetVaccinationForm(): void
    {
        $this->reset('vaccineName', 'vaccineDose', 'vaccineAdministeredBy', 'vaccineLotNumber', 'vaccineNotes');
        $this->vaccineAdministeredAt = now()->toDateString();
        $this->resetValidation();
    }

    private function resetConsentForm(): void
    {
        $this->reset('consentType', 'consentNotes');
        $this->consentVersion = 'demo-1';
        $this->consentStatus = 'granted';
        $this->consentRecordedAt = now()->format('Y-m-d\TH:i');
        $this->resetValidation();
    }
}
