<?php

use App\Livewire\Appointments;
use App\Livewire\Laboratory;
use App\Livewire\MedicalRecords;
use App\Livewire\Patients;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::view('/login', 'auth.login')->name('login');
Route::view('/register', 'auth.register')->name('register');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/patients', Patients::class)->name('patients');
    Route::get('/appointments', Appointments::class)->name('appointments');
    Route::get('/medical-records', MedicalRecords::class)
        ->name('medical-records');
    Route::get('/laboratory', Laboratory::class)->name('laboratory');
});
