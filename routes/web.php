<?php

use App\Http\Controllers\PatientCheckInController;
use App\Http\Controllers\PatientQrCodeController;
use App\Livewire\AdminUsers;
use App\Livewire\Appointments;
use App\Livewire\CheckIns;
use App\Livewire\Laboratory;
use App\Livewire\MedicalRecords;
use App\Livewire\PatientProfile;
use App\Livewire\Patients;
use App\Livewire\Prescriptions;
use App\Livewire\StudentWorkspace;
use App\Models\Appointment;
use App\Models\CheckIn;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::view('/login', 'auth.login')->name('login');
Route::view('/register', 'auth.register')->name('register');
Route::get('/check-in/{patient}', [PatientCheckInController::class, 'show'])
    ->middleware('signed')
    ->name('check-ins.show');
Route::post('/check-in/{patient}', [PatientCheckInController::class, 'store'])
    ->middleware(['signed', 'throttle:10,1'])
    ->name('check-ins.store');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
])->group(function () {
    Route::get('/dashboard', function () {
        $user = request()->user();
        $stats = [];

        if ($user->hasWorkspaceAccess() && $user->isClinicStaff()) {
            $stats = [
                'Patients' => Patient::query()->count(),
                "Today's appointments" => Appointment::query()->whereDate('scheduled_at', today())->count(),
                'Check-ins to review' => CheckIn::query()->where('status', 'pending')->count(),
                'Active prescriptions' => Prescription::query()->where('status', 'active')->count(),
            ];
        } elseif ($user->hasWorkspaceAccess() && $user->isAdmin()) {
            $stats = [
                'Applications to review' => User::query()->where('approval_status', User::APPROVAL_PENDING)
                    ->whereIn('role', [User::ROLE_STUDENT, User::ROLE_CLINIC_STAFF])->count(),
                'Student accounts' => User::query()->where('role', User::ROLE_STUDENT)->count(),
                'Clinic staff accounts' => User::query()->where('role', User::ROLE_CLINIC_STAFF)->count(),
            ];
        }

        return view('dashboard', [
            'stats' => $stats,
            'notifications' => $user->unreadNotifications()->latest()->limit(5)->get(),
        ]);
    })->name('dashboard');

    Route::middleware('role:clinic_staff')->group(function () {
        Route::get('/patients', Patients::class)->name('patients');
        Route::get('/patients/{patient}', PatientProfile::class)->name('patients.show');
        Route::get('/patients/{patient}/qr', PatientQrCodeController::class)->name('patients.qr');
        Route::get('/appointments', Appointments::class)->name('appointments');
        Route::get('/medical-records', MedicalRecords::class)->name('medical-records');
        Route::get('/laboratory', Laboratory::class)->name('laboratory');
        Route::get('/prescriptions', Prescriptions::class)->name('prescriptions');
        Route::get('/check-ins', CheckIns::class)->name('check-ins.index');
    });

    Route::get('/practice', StudentWorkspace::class)
        ->middleware('role:student')
        ->name('student.practice');

    Route::get('/admin/accounts', AdminUsers::class)
        ->middleware('role:admin')
        ->name('admin.accounts');
});
