<?php

use App\Livewire\Appointments;
use App\Livewire\Laboratory;
use App\Livewire\MedicalRecords;
use App\Livewire\Patients;
use App\Livewire\Prescriptions;
use App\Models\Appointment;
use App\Models\LabOrder;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\User;
use Database\Seeders\DemoClinicSeeder;
use Livewire\Livewire;

it('requires a signed-in account for every clinical page', function () {
    foreach (['/patients', '/appointments', '/medical-records'] as $path) {
        $this->get($path)->assertRedirect(route('login'));
    }

    $this->get('/laboratory')->assertNotFound();

    $this->actingAs(User::factory()->unverified()->create())
        ->get('/medical-records')
        ->assertOk()
        ->assertSee('Medical Records');
});

it('renders all clinical pages for a signed-in user', function () {
    $this->actingAs(User::factory()->create());

    $this->get('/patients')->assertOk()->assertSee('Patient directory');
    $this->get('/appointments')->assertOk()->assertSee('Appointments');
    $this->get('/medical-records')->assertOk()->assertSee('Medical Records');
    $this->get('/laboratory')->assertNotFound();
});

it('filters appointments and medical records by the linked patient', function () {
    $this->actingAs(User::factory()->create());
    $patient = Patient::factory()->create();
    $otherPatient = Patient::factory()->create();
    $appointment = Appointment::factory()->for($patient)->create(['scheduled_at' => now()]);
    $otherAppointment = Appointment::factory()->for($otherPatient)->create(['scheduled_at' => now()]);
    $record = MedicalRecord::factory()->for($patient)->create();
    $otherRecord = MedicalRecord::factory()->for($otherPatient)->create();
    $this->get(route('appointments', ['patient' => $patient->id]))
        ->assertOk()
        ->assertSee($appointment->appointment_code)
        ->assertDontSee($otherAppointment->appointment_code);

    $this->get(route('medical-records', ['patient' => $patient->id]))
        ->assertOk()
        ->assertSee($record->record_number)
        ->assertDontSee($otherRecord->record_number);

});

it('escapes patient-provided content in the directory', function () {
    $this->actingAs(User::factory()->create());
    Patient::factory()->create(['first_name' => '<script>alert(1)</script>']);

    $this->get('/patients')
        ->assertOk()
        ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
        ->assertDontSee('<script>alert(1)</script>', false);
});

it('shows the selected patient summary in the directory', function () {
    $this->actingAs(User::factory()->create());
    $patient = Patient::factory()->create(['address' => 'Quezon City, Metro Manila']);
    $otherPatient = Patient::factory()->create(['address' => 'Makati City']);
    $latestVisit = MedicalRecord::factory()->for($patient)->create([
        'title' => 'General consultation',
        'recorded_at' => '2026-11-17 10:00:00',
    ]);
    MedicalRecord::factory()->for($patient)->create([
        'title' => 'Earlier consultation',
        'recorded_at' => '2026-10-17 10:00:00',
    ]);

    Livewire::test(Patients::class)
        ->call('selectPatient', $patient->id)
        ->assertSet('selectedPatientId', $patient->id)
        ->assertSee($patient->address)
        ->assertSee('Registered')
        ->assertSee('Latest visit')
        ->assertSee('General consultation')
        ->assertSee($latestVisit->recorded_at->format('d F Y'))
        ->assertSee('View prescriptions')
        ->assertDontSee('Earlier consultation')
        ->assertDontSee('Laboratory')
        ->assertDontSee($otherPatient->address);
});

it('shows six patient records per directory page', function () {
    $this->actingAs(User::factory()->create());
    Patient::factory()->count(7)->create();

    $this->get('/patients')->assertSee('Showing 1–6 of 7 patients');
});

it('filters prescription records to the patient selected from the directory', function () {
    $this->actingAs(User::factory()->create());
    $patient = Patient::factory()->create();
    $otherPatient = Patient::factory()->create();
    $prescription = Prescription::factory()->for($patient)->create(['medication' => 'Selected patient medicine']);
    $otherPrescription = Prescription::factory()->for($otherPatient)->create(['medication' => 'Other patient medicine']);

    $this->get(route('prescriptions', ['patient' => $patient->id]))
        ->assertSee($prescription->medication)
        ->assertDontSee($otherPrescription->medication);

    Livewire::test(Prescriptions::class)
        ->set('patientFilter', $patient->id)
        ->set('search', 'Other patient medicine')
        ->assertDontSee($otherPrescription->medication);
});

it('creates and updates a patient record', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(Patients::class)
        ->set('firstName', 'Jamie')
        ->set('middleName', 'Marie')
        ->set('lastName', 'Rivera')
        ->set('dateOfBirth', '1990-04-12')
        ->set('genderInput', 'Other')
        ->set('phone', '555-0101')
        ->set('address', 'Quezon City, Metro Manila')
        ->set('provider', 'Dr. Morgan Lee')
        ->set('heightCm', '170')
        ->set('weightKg', '68')
        ->set('bmi', '23.53')
        ->set('bloodPressure', '120/80')
        ->set('temperatureC', '36.7')
        ->set('pulseRate', '74')
        ->set('respiratoryRate', '16')
        ->set('oxygenSaturation', '98')
        ->call('savePatient')
        ->assertHasNoErrors();

    $patient = Patient::query()->where('first_name', 'Jamie')->firstOrFail();

    $this->assertDatabaseHas('patients', [
        'id' => $patient->id,
        'patient_number' => $patient->patient_number,
        'last_name' => 'Rivera',
        'gender' => 'Other',
        'address' => 'Quezon City, Metro Manila',
        'height_cm' => 170,
        'weight_kg' => 68,
        'bmi' => 23.53,
        'blood_pressure' => '120/80',
        'temperature_c' => 36.7,
        'pulse_rate' => 74,
        'respiratory_rate' => 16,
        'oxygen_saturation' => 98,
    ]);

    Livewire::test(Patients::class)
        ->call('editPatient', $patient->id)
        ->set('phone', '555-0102')
        ->call('savePatient')
        ->assertHasNoErrors();

    expect($patient->fresh()->phone)->toBe('555-0102');
});

it('creates an appointment and links it to a patient', function () {
    $this->actingAs(User::factory()->create());
    $patient = Patient::factory()->create();

    Livewire::test(Appointments::class)
        ->set('patientId', $patient->id)
        ->set('scheduledAt', '2026-11-07T09:30')
        ->set('provider', 'Dr. Morgan Lee')
        ->set('appointmentType', 'Follow-up')
        ->call('saveAppointment')
        ->assertHasNoErrors();

    $appointment = Appointment::query()->firstOrFail();

    expect($appointment->patient->is($patient))->toBeTrue();
    $this->assertDatabaseHas('appointments', [
        'id' => $appointment->id,
        'patient_id' => $patient->id,
        'status' => 'scheduled',
    ]);

    $otherPatient = Patient::factory()->create();

    Livewire::test(Appointments::class)
        ->call('editAppointment', $appointment->id)
        ->set('patientId', $otherPatient->id)
        ->call('saveAppointment')
        ->assertHasErrors(['patientId' => 'in']);

    expect($appointment->fresh()->patient_id)->toBe($patient->id);

    Livewire::test(Appointments::class)
        ->call('cancelAppointment', $appointment->id);

    expect($appointment->fresh()->status)->toBe('cancelled');

    Livewire::test(Appointments::class)
        ->call('editAppointment', $appointment->id)
        ->set('provider', 'Dr. Alex Chen')
        ->call('saveAppointment')
        ->assertHasNoErrors();

    expect($appointment->fresh()->provider)->toBe('Dr. Alex Chen');
});

it('stores medical notes with a matching patient appointment', function () {
    $this->actingAs(User::factory()->create());
    $patient = Patient::factory()->create();
    $appointment = Appointment::factory()->for($patient)->create();

    Livewire::test(MedicalRecords::class)
        ->set('patientId', $patient->id)
        ->set('appointmentId', $appointment->id)
        ->set('recordTitle', 'Consultation')
        ->set('provider', 'Dr. Morgan Lee')
        ->set('recordedAt', '2026-11-06T14:00')
        ->set('subjective', 'Patient history recorded.')
        ->set('objective', 'Examination documented.')
        ->set('assessment', 'Assessment recorded.')
        ->set('plan', 'Follow-up planned.')
        ->call('saveRecord')
        ->assertHasNoErrors();

    $record = MedicalRecord::query()->firstOrFail();

    expect($record->patient->is($patient))->toBeTrue();
    expect($record->appointment->is($appointment))->toBeTrue();

    $otherPatient = Patient::factory()->create();

    Livewire::test(MedicalRecords::class)
        ->call('editRecord', $record->id)
        ->set('patientId', $otherPatient->id)
        ->call('saveRecord')
        ->assertHasErrors(['patientId' => 'in']);

    expect($record->fresh()->patient_id)->toBe($patient->id);

    Livewire::test(MedicalRecords::class)
        ->call('editRecord', $record->id)
        ->set('recordTitle', 'Updated Consultation')
        ->call('saveRecord')
        ->assertHasNoErrors();

    expect($record->fresh()->title)->toBe('Updated Consultation');
});

it('rejects a medical record appointment that belongs to another patient', function () {
    $this->actingAs(User::factory()->create());
    $patient = Patient::factory()->create();
    $otherPatientAppointment = Appointment::factory()->create();

    Livewire::test(MedicalRecords::class)
        ->set('patientId', $patient->id)
        ->set('appointmentId', $otherPatientAppointment->id)
        ->set('recordTitle', 'Consultation')
        ->set('provider', 'Dr. Morgan Lee')
        ->set('recordedAt', '2026-11-06T14:00')
        ->set('subjective', 'Patient history recorded.')
        ->set('objective', 'Examination documented.')
        ->set('assessment', 'Assessment recorded.')
        ->set('plan', 'Follow-up planned.')
        ->call('saveRecord')
        ->assertHasErrors(['appointmentId' => 'exists']);

    $this->assertDatabaseCount('medical_records', 0);
});

it('stores a lab order and result with the same patient as its linked record', function () {
    $this->actingAs(User::factory()->create());
    $patient = Patient::factory()->create();
    $record = MedicalRecord::factory()->for($patient)->create();
    $appointment = Appointment::factory()->for($patient)->create();

    Livewire::test(Laboratory::class)
        ->set('patientId', $patient->id)
        ->set('appointmentId', $appointment->id)
        ->set('medicalRecordId', $record->id)
        ->set('testName', 'Blood panel')
        ->set('orderedBy', 'Dr. Morgan Lee')
        ->set('status', 'resulted')
        ->set('orderedAt', '2026-11-06T09:00')
        ->set('result', 'Result recorded for demonstration.')
        ->set('resultAt', '2026-11-06T11:00')
        ->call('saveLabOrder')
        ->assertHasNoErrors();

    $labOrder = LabOrder::query()->firstOrFail();

    expect($labOrder->patient->is($patient))->toBeTrue();
    expect($labOrder->appointment->is($appointment))->toBeTrue();
    expect($labOrder->medicalRecord->is($record))->toBeTrue();

    Livewire::test(Laboratory::class)
        ->call('editLabOrder', $labOrder->id)
        ->set('testName', 'Updated blood panel')
        ->call('saveLabOrder')
        ->assertHasNoErrors();

    expect($labOrder->fresh()->test_name)->toBe('Updated blood panel');
});

it('rejects a lab order linked to another patients appointment or medical record', function () {
    $this->actingAs(User::factory()->create());
    $patient = Patient::factory()->create();
    $otherPatient = Patient::factory()->create();
    $otherPatientAppointment = Appointment::factory()->for($otherPatient)->create();
    $otherPatientRecord = MedicalRecord::factory()->for($otherPatient)->create();

    Livewire::test(Laboratory::class)
        ->set('patientId', $patient->id)
        ->set('appointmentId', $otherPatientAppointment->id)
        ->set('medicalRecordId', $otherPatientRecord->id)
        ->set('testName', 'Blood panel')
        ->set('orderedBy', 'Dr. Morgan Lee')
        ->set('orderedAt', '2026-11-06T09:00')
        ->call('saveLabOrder')
        ->assertHasErrors(['appointmentId' => 'exists'])
        ->assertHasErrors(['medicalRecordId' => 'exists']);

    $this->assertDatabaseCount('lab_orders', 0);
});

it('requires a result before a lab order can be marked resulted', function () {
    $this->actingAs(User::factory()->create());
    $patient = Patient::factory()->create();

    Livewire::test(Laboratory::class)
        ->set('patientId', $patient->id)
        ->set('testName', 'Blood panel')
        ->set('orderedBy', 'Dr. Morgan Lee')
        ->set('status', 'resulted')
        ->set('orderedAt', '2026-11-06T09:00')
        ->call('saveLabOrder')
        ->assertHasErrors(['result' => 'required']);

    $this->assertDatabaseCount('lab_orders', 0);
});

it('seeds the linked demo data without duplicating it on reruns', function () {
    $this->seed(DemoClinicSeeder::class);
    $this->seed(DemoClinicSeeder::class);

    $this->assertDatabaseCount('patients', 7);
    $this->assertDatabaseCount('appointments', 7);
    $this->assertDatabaseCount('medical_records', 5);
    $this->assertDatabaseCount('lab_orders', 2);

    $patient = Patient::query()->where('patient_number', 'PAT-1005')->firstOrFail();
    $record = MedicalRecord::query()->where('record_number', 'REC-0892')->firstOrFail();

    expect($record->patient->is($patient))->toBeTrue();
    expect($patient->labOrders()->where('order_number', 'LAB-0037')->exists())->toBeTrue();
});
