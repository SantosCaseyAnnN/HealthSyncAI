<?php

use App\Livewire\AdminUsers;
use App\Livewire\CheckIns;
use App\Livewire\Prescriptions;
use App\Models\CheckIn;
use App\Models\Patient;
use App\Models\User;
use App\Notifications\AccountDecisionNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;

it('rejects a clinic application and notifies the applicant without granting access', function () {
    Notification::fake();
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $staff = User::factory()->create([
        'role' => User::ROLE_CLINIC_STAFF,
        'approval_status' => User::APPROVAL_PENDING,
        'is_active' => false,
    ]);

    Livewire::actingAs($admin)->test(AdminUsers::class)
        ->call('rejectApplication', $staff->id)
        ->assertHasNoErrors();

    expect($staff->fresh())
        ->approval_status->toBe(User::APPROVAL_REJECTED)
        ->is_active->toBeFalse();
    Notification::assertSentTo($staff, AccountDecisionNotification::class, fn (AccountDecisionNotification $notification) => $notification->decision === 'rejected');

    $this->actingAs($staff->fresh())->get('/patients')->assertRedirect(route('dashboard'));
});

it('accepts a clinic application and activates access', function () {
    Notification::fake();
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $staff = User::factory()->create([
        'role' => User::ROLE_CLINIC_STAFF,
        'approval_status' => User::APPROVAL_PENDING,
        'is_active' => false,
    ]);

    Livewire::actingAs($admin)->test(AdminUsers::class)
        ->call('acceptApplication', $staff->id)
        ->assertHasNoErrors();

    expect($staff->fresh())
        ->approval_status->toBe(User::APPROVAL_APPROVED)
        ->is_active->toBeTrue();
    Notification::assertSentTo($staff, AccountDecisionNotification::class, fn (AccountDecisionNotification $notification) => $notification->decision === 'accepted');

    $this->actingAs($staff->fresh())->get('/patients')->assertOk();
});

it('does not let clinic staff open the admin account workspace', function () {
    $staff = User::factory()->create(['role' => User::ROLE_CLINIC_STAFF]);

    $this->actingAs($staff)->get('/admin/accounts')->assertForbidden();
});

it('allows an administrator to edit clinic staff account information', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $staff = User::factory()->create([
        'role' => User::ROLE_CLINIC_STAFF,
        'first_name' => 'Old',
        'last_name' => 'Name',
    ]);

    Livewire::actingAs($admin)->test(AdminUsers::class)
        ->call('editUser', $staff->id)
        ->set('firstName', 'Updated')
        ->set('middleName', 'Clinic')
        ->set('lastName', 'Staff')
        ->set('email', 'updated.staff@example.com')
        ->call('saveUser')
        ->assertHasNoErrors();

    expect($staff->fresh())
        ->first_name->toBe('Updated')
        ->middle_name->toBe('Clinic')
        ->last_name->toBe('Staff')
        ->email->toBe('updated.staff@example.com');
});

it('does not allow account activation to bypass a rejected application', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $applicant = User::factory()->create([
        'role' => User::ROLE_CLINIC_STAFF,
        'approval_status' => User::APPROVAL_REJECTED,
        'is_active' => false,
    ]);

    Livewire::actingAs($admin)->test(AdminUsers::class)
        ->call('toggleAccount', $applicant->id)
        ->assertStatus(422);

    expect($applicant->fresh())
        ->approval_status->toBe(User::APPROVAL_REJECTED)
        ->is_active->toBeFalse();
});

it('accepts basic measurements on a signed public check-in and stores it pending', function () {
    $patient = Patient::factory()->create();
    $signedUrl = URL::signedRoute('check-ins.show', ['patient' => $patient]);

    $this->get($signedUrl)
        ->assertOk()
        ->assertSee('Height (cm)')
        ->assertSee('Weight (kg)')
        ->assertSee('Basic clinic measurements');

    $this->post($signedUrl, [
        'first_name' => 'Jamie',
        'last_name' => 'Rivera',
        'date_of_birth' => '1994-05-12',
        'gender' => 'Female',
        'phone' => '555-0199',
        'email' => 'jamie@example.com',
        'address' => 'Quezon City, Metro Manila',
        'consent_given' => '1',
        'reason_for_visit' => 'Routine follow-up',
        'height_cm' => 165,
        'weight_kg' => 60,
        'blood_pressure' => '118/76',
        'temperature_c' => 36.8,
        'pulse_rate' => 72,
        'respiratory_rate' => 16,
        'oxygen_saturation' => 98.5,
    ])->assertRedirect();

    $this->assertDatabaseHas('check_ins', [
        'patient_id' => $patient->id,
        'first_name' => 'Jamie',
        'gender' => 'Female',
        'address' => 'Quezon City, Metro Manila',
        'consent_given' => true,
        'height_cm' => 165,
        'weight_kg' => 60,
        'blood_pressure' => '118/76',
        'status' => 'pending',
        'queue_number' => null,
    ]);
});

it('registers a reviewed check-in and updates the linked patient measurements', function () {
    $staff = User::factory()->create(['role' => User::ROLE_CLINIC_STAFF]);
    $patient = Patient::factory()->create();
    $checkIn = CheckIn::factory()->for($patient)->create([
        'consent_given' => true,
        'gender' => 'Female',
        'height_cm' => 165,
        'weight_kg' => 60,
        'blood_pressure' => '118/76',
        'temperature_c' => 36.8,
        'pulse_rate' => 72,
        'respiratory_rate' => 16,
        'oxygen_saturation' => 98.5,
    ]);

    Livewire::actingAs($staff)->test(CheckIns::class)
        ->set('statusFilter', 'queue')
        ->call('registerInQueue', $checkIn->id)
        ->assertHasNoErrors()
        ->assertSee('~0 min');

    expect($checkIn->fresh())
        ->status->toBe('registered')
        ->queue_number->toBe(1)
        ->reviewed_by_user_id->toBe($staff->id);

    expect($patient->fresh())
        ->height_cm->toBe('165.00')
        ->weight_kg->toBe('60.00')
        ->blood_pressure->toBe('118/76')
        ->bmi->toBe('22.04');
});

it('calls the next queue patient and completes the consultation', function () {
    $staff = User::factory()->create(['role' => User::ROLE_CLINIC_STAFF]);
    $patient = Patient::factory()->create();
    $checkIn = CheckIn::factory()->for($patient)->create([
        'status' => 'registered',
        'queue_number' => 1,
    ]);

    Livewire::actingAs($staff)->test(CheckIns::class)
        ->call('callNextPatient')
        ->assertHasNoErrors();

    expect($checkIn->fresh())
        ->status->toBe('in_consultation')
        ->reviewed_by_user_id->toBe($staff->id);

    Livewire::actingAs($staff)->test(CheckIns::class)
        ->call('completePatient', $checkIn->id)
        ->assertHasNoErrors();

    expect($checkIn->fresh()->status)->toBe('completed');
});

it('requires consent and validates basic measurement ranges on public check-in', function () {
    $patient = Patient::factory()->create();
    $signedUrl = URL::signedRoute('check-ins.store', ['patient' => $patient]);

    $this->post($signedUrl, [
        'first_name' => 'Jamie',
        'last_name' => 'Rivera',
        'height_cm' => 999,
        'pulse_rate' => 500,
    ])->assertSessionHasErrors(['consent_given', 'height_cm', 'pulse_rate']);

    $this->assertDatabaseCount('check_ins', 0);
});

it('allows clinic staff to create patient-linked prescription records', function () {
    $staff = User::factory()->create(['role' => User::ROLE_CLINIC_STAFF]);
    $patient = Patient::factory()->create();

    Livewire::actingAs($staff)->test(Prescriptions::class)
        ->set('patientId', $patient->id)
        ->set('medication', 'Example medicine')
        ->set('strength', '250 mg')
        ->set('dosage', '1 tablet')
        ->set('prescriptionRoute', 'Oral')
        ->set('frequency', 'Twice daily')
        ->set('duration', '7 days')
        ->set('issuedAt', '2026-11-17T10:00')
        ->call('savePrescription')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('prescriptions', [
        'patient_id' => $patient->id,
        'prescribed_by_user_id' => $staff->id,
        'medication' => 'Example medicine',
        'status' => 'active',
    ]);
});

it('returns 404 for the removed student workspace route', function () {
    $this->actingAs(User::factory()->create(['role' => User::ROLE_CLINIC_STAFF]))
        ->get('/practice')
        ->assertNotFound();
});

it('keeps deactivated legacy accounts out of all workspaces', function () {
    $legacyAccount = User::factory()->create([
        'role' => User::ROLE_DISABLED,
        'approval_status' => User::APPROVAL_REJECTED,
        'is_active' => false,
    ]);

    $this->actingAs($legacyAccount)->get('/patients')->assertRedirect(route('dashboard'));
    expect($legacyAccount->roleLabel())->toBe('Deactivated');
});

it('does not retain student account identity fields in the current schema', function () {
    expect(Schema::hasColumn('users', 'school_id'))->toBeFalse()
        ->and(Schema::hasColumn('users', 'year_level'))->toBeFalse()
        ->and(Schema::hasColumn('users', 'school_id_verified_at'))->toBeFalse();
});
