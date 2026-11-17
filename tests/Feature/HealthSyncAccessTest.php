<?php

use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Livewire\AdminUsers;
use App\Livewire\CheckIns;
use App\Livewire\Prescriptions;
use App\Models\CheckIn;
use App\Models\Patient;
use App\Models\User;
use App\Notifications\AccountDecisionNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;

it('keeps students in the fictional practice workspace and blocks clinic records', function () {
    $student = User::factory()->create([
        'role' => User::ROLE_STUDENT,
        'school_id' => 'QC-2048',
        'year_level' => 'Third',
    ]);

    $this->actingAs($student)->get('/practice')
        ->assertOk()
        ->assertSee('Fictional practice cases')
        ->assertSee('Simulation only');

    $this->get('/patients')->assertForbidden();
    $this->get('/appointments')->assertForbidden();
    $this->get('/medical-records')->assertForbidden();
    $this->get('/prescriptions')->assertForbidden();
});

it('requires a verified student school ID before approval and notifies the applicant', function () {
    Notification::fake();
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $student = User::factory()->create([
        'role' => User::ROLE_STUDENT,
        'approval_status' => User::APPROVAL_PENDING,
        'is_active' => false,
        'school_id' => 'QC-4021',
        'year_level' => 'Second',
    ]);

    Livewire::actingAs($admin)->test(AdminUsers::class)
        ->call('acceptApplication', $student->id)
        ->assertHasErrors('approval')
        ->call('verifySchoolId', $student->id)
        ->call('acceptApplication', $student->id)
        ->assertHasNoErrors();

    expect($student->fresh())
        ->approval_status->toBe(User::APPROVAL_APPROVED)
        ->is_active->toBeTrue()
        ->school_id_verified_at->not->toBeNull();
    Notification::assertSentTo($student, AccountDecisionNotification::class, fn (AccountDecisionNotification $notification) => $notification->decision === 'accepted');

    $this->actingAs($student->fresh())->get('/practice')->assertOk();
});

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

it('does not let clinic staff open the admin account workspace', function () {
    $staff = User::factory()->create(['role' => User::ROLE_CLINIC_STAFF]);

    $this->actingAs($staff)->get('/admin/accounts')->assertForbidden();
});

it('sends an account back for review when an administrator changes its role or school ID', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $staff = User::factory()->create(['role' => User::ROLE_CLINIC_STAFF]);
    $student = User::factory()->create([
        'role' => User::ROLE_STUDENT,
        'school_id' => 'QC-6081',
        'year_level' => 'Second',
        'school_id_verified_at' => now(),
    ]);

    Livewire::actingAs($admin)->test(AdminUsers::class)
        ->call('editUser', $staff->id)
        ->set('role', User::ROLE_STUDENT)
        ->set('schoolId', 'QC-6082')
        ->set('yearLevel', 'Third')
        ->call('saveUser')
        ->assertHasNoErrors()
        ->assertSee($staff->email)
        ->set('statusFilter', 'pending')
        ->assertSee($staff->email)
        ->call('editUser', $student->id)
        ->set('schoolId', 'QC-6083')
        ->call('saveUser')
        ->assertHasNoErrors();

    expect($staff->fresh())
        ->role->toBe(User::ROLE_STUDENT)
        ->approval_status->toBe(User::APPROVAL_PENDING)
        ->is_active->toBeFalse()
        ->school_id_verified_at->toBeNull();
    expect($student->fresh())
        ->school_id->toBe('QC-6083')
        ->approval_status->toBe(User::APPROVAL_PENDING)
        ->is_active->toBeFalse()
        ->school_id_verified_at->toBeNull();
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

it('accepts a signed public check-in and keeps the request pending until clinic review', function () {
    $patient = Patient::factory()->create();
    $signedUrl = URL::signedRoute('check-ins.show', ['patient' => $patient]);

    $this->get($signedUrl)->assertOk()->assertSee('Share your check-in details');

    $this->post($signedUrl, [
        'first_name' => 'Jamie',
        'last_name' => 'Rivera',
        'date_of_birth' => '1994-05-12',
        'phone' => '555-0199',
        'email' => 'jamie@example.com',
        'reason_for_visit' => 'Routine follow-up',
    ])->assertRedirect();

    $this->assertDatabaseHas('check_ins', [
        'patient_id' => $patient->id,
        'first_name' => 'Jamie',
        'status' => 'pending',
        'queue_number' => null,
    ]);

    $otherPatient = Patient::factory()->create();
    $tamperedUrl = str_replace('/check-in/'.$patient->id, '/check-in/'.$otherPatient->id, $signedUrl);

    $this->get($tamperedUrl)->assertForbidden();
});

it('registers a reviewed check-in in the clinic queue and estimates its wait', function () {
    $staff = User::factory()->create(['role' => User::ROLE_CLINIC_STAFF]);
    $patient = Patient::factory()->create();
    $checkIn = CheckIn::factory()->for($patient)->create();

    Livewire::actingAs($staff)->test(CheckIns::class)
        ->set('statusFilter', 'queue')
        ->call('registerInQueue', $checkIn->id)
        ->assertHasNoErrors()
        ->assertSee('~0 min');

    expect($checkIn->fresh())
        ->status->toBe('registered')
        ->queue_number->toBe(1)
        ->reviewed_by_user_id->toBe($staff->id);
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

it('does not let students change their own role or school ID through profile updates', function () {
    $student = User::factory()->create([
        'role' => User::ROLE_STUDENT,
        'school_id' => 'QC-5091',
        'year_level' => 'First',
    ]);

    app(UpdateUserProfileInformation::class)->update($student, [
        'name' => 'Updated Student',
        'email' => $student->email,
        'role' => User::ROLE_CLINIC_STAFF,
        'school_id' => 'QC-9999',
    ]);

    expect($student->fresh())
        ->name->toBe('Updated Student')
        ->role->toBe(User::ROLE_STUDENT)
        ->school_id->toBe('QC-5091');
});
