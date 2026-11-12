<?php

use App\Livewire\PatientProfile;
use App\Livewire\Patients;
use App\Models\Patient;
use App\Models\PatientConsent;
use App\Models\PatientVaccination;
use App\Models\User;
use Database\Seeders\DemoClinicSeeder;
use Livewire\Livewire;

it('requires authentication to open patient profiles and QR codes', function () {
    $patient = Patient::factory()->create();

    $this->get(route('patients.show', $patient))
        ->assertRedirect(route('login'));
    $this->get(route('patients.qr', $patient))
        ->assertRedirect(route('login'));
});

it('stores structured patient history and emergency contact information', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(Patients::class)
        ->set('firstName', 'Jamie')
        ->set('lastName', 'Rivera')
        ->set('medicalHistory', 'DEMO ONLY: Example history.')
        ->set('familyHistory', 'DEMO ONLY: Example family history.')
        ->set('emergencyContactName', 'Demo Contact')
        ->set('emergencyContactRelationship', 'Sibling')
        ->set('emergencyContactPhone', '+63 917 000 0101')
        ->call('savePatient')
        ->assertHasNoErrors();

    $patient = Patient::query()->where('first_name', 'Jamie')->firstOrFail();

    $this->assertDatabaseHas('patients', [
        'id' => $patient->id,
        'medical_history' => 'DEMO ONLY: Example history.',
        'family_history' => 'DEMO ONLY: Example family history.',
        'emergency_contact_name' => 'Demo Contact',
        'emergency_contact_relationship' => 'Sibling',
        'emergency_contact_phone' => '+63 917 000 0101',
    ]);

    $this->actingAs(User::factory()->create())
        ->get(route('patients.show', $patient))
        ->assertOk()
        ->assertSee('EHR Profile')
        ->assertSee('DEMO ONLY: Example history.');
});

it('records a vaccination and a consent decision against the selected patient', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    $patient = Patient::factory()->create();

    Livewire::test(PatientProfile::class, ['patient' => $patient])
        ->set('vaccineName', 'Influenza')
        ->set('vaccineDose', 'Annual dose')
        ->set('vaccineAdministeredAt', now()->toDateString())
        ->set('vaccineAdministeredBy', 'Demo Nurse')
        ->set('vaccineLotNumber', 'LOT-DEMO-001')
        ->set('vaccineNotes', 'Synthetic test record.')
        ->call('recordVaccination')
        ->assertHasNoErrors()
        ->set('consentType', 'Treatment')
        ->set('consentVersion', 'demo-1')
        ->set('consentStatus', 'granted')
        ->set('consentRecordedAt', now()->format('Y-m-d\TH:i'))
        ->set('consentNotes', 'Synthetic test decision.')
        ->call('recordConsent')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('patient_vaccinations', [
        'patient_id' => $patient->id,
        'vaccine_name' => 'Influenza',
        'dose' => 'Annual dose',
        'lot_number' => 'LOT-DEMO-001',
        'notes' => 'Synthetic test record.',
        'recorded_by_user_id' => $user->id,
    ]);
    $this->assertDatabaseHas('patient_consents', [
        'patient_id' => $patient->id,
        'consent_type' => 'Treatment',
        'form_version' => 'demo-1',
        'status' => 'granted',
        'notes' => 'Synthetic test decision.',
        'recorded_by_user_id' => $user->id,
    ]);
});

it('rejects future vaccination dates and incomplete consent decisions', function () {
    $this->actingAs(User::factory()->create());
    $patient = Patient::factory()->create();

    Livewire::test(PatientProfile::class, ['patient' => $patient])
        ->set('vaccineName', 'Influenza')
        ->set('vaccineAdministeredAt', now()->addDay()->toDateString())
        ->call('recordVaccination')
        ->assertHasErrors(['vaccineAdministeredAt' => 'before_or_equal'])
        ->set('vaccineAdministeredAt', now()->toDateString())
        ->set('consentVersion', '')
        ->call('recordConsent')
        ->assertHasErrors(['consentType' => 'required', 'consentVersion' => 'required'])
        ->set('consentType', 'Treatment')
        ->set('consentVersion', 'demo-1')
        ->set('consentStatus', 'unrecognized')
        ->set('consentRecordedAt', now()->format('Y-m-d\TH:i'))
        ->call('recordConsent')
        ->assertHasErrors(['consentStatus' => 'in']);

    $this->assertDatabaseCount('patient_vaccinations', 0);
    $this->assertDatabaseCount('patient_consents', 0);
});

it('escapes free-text EHR data on the patient profile', function () {
    $this->actingAs(User::factory()->create());
    $dangerousText = '<script>alert(1)</script>';
    $patient = Patient::factory()->create([
        'medical_history' => $dangerousText,
        'family_history' => $dangerousText,
        'allergies' => $dangerousText,
        'emergency_contact_name' => $dangerousText,
        'emergency_contact_relationship' => $dangerousText,
        'emergency_contact_phone' => $dangerousText,
    ]);
    PatientVaccination::factory()->for($patient)->create([
        'vaccine_name' => $dangerousText,
        'dose' => $dangerousText,
        'administered_by' => $dangerousText,
        'lot_number' => $dangerousText,
        'notes' => $dangerousText,
    ]);
    PatientConsent::factory()->for($patient)->create([
        'consent_type' => $dangerousText,
        'form_version' => 'demo-1',
        'notes' => $dangerousText,
    ]);

    $this->get(route('patients.show', $patient))
        ->assertOk()
        ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
        ->assertDontSee($dangerousText, false);
});

it('renders a local QR code for an authenticated patient profile', function () {
    $this->actingAs(User::factory()->create());
    $patient = Patient::factory()->create();

    $this->get(route('patients.qr', $patient))
        ->assertOk()
        ->assertHeader('Content-Type', 'image/svg+xml')
        ->assertSee('<svg', false);
});

it('seeds synthetic EHR examples idempotently', function () {
    $this->seed(DemoClinicSeeder::class);
    $this->assertDatabaseCount('patient_vaccinations', 1);
    $this->assertDatabaseCount('patient_consents', 1);

    $this->seed(DemoClinicSeeder::class);

    $patient = Patient::query()->where('patient_number', 'PAT-1001')->firstOrFail();

    expect($patient->medical_history)->toContain('DEMO ONLY');
    $this->assertDatabaseCount('patient_vaccinations', 1);
    $this->assertDatabaseCount('patient_consents', 1);
});
