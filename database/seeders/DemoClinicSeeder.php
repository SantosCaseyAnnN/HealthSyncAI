<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\LabOrder;
use App\Models\MedicalRecord;
use App\Models\Patient;
use Illuminate\Database\Seeder;

class DemoClinicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $demoPatients = [
            ['patient_number' => 'PAT-1001', 'first_name' => 'Maria', 'last_name' => 'Santos', 'age' => 34, 'gender' => 'Female', 'phone' => '+63 917 123 4567', 'provider' => 'Dr. Adrian Reyes', 'blood_type' => 'B+'],
            ['patient_number' => 'PAT-1002', 'first_name' => 'John', 'last_name' => 'Dela Cruz', 'age' => 42, 'gender' => 'Male', 'phone' => '+63 918 234 5678', 'provider' => 'Dr. Camille Garcia', 'blood_type' => 'O+'],
            ['patient_number' => 'PAT-1003', 'first_name' => 'Angela', 'last_name' => 'Ramos', 'age' => 29, 'gender' => 'Female', 'phone' => '+63 919 345 6789', 'provider' => 'Dr. Miguel Torres', 'blood_type' => 'AB+'],
            ['patient_number' => 'PAT-1004', 'first_name' => 'Carlos', 'last_name' => 'Mendoza', 'age' => 51, 'gender' => 'Male', 'phone' => '+63 920 456 7890', 'provider' => 'Dr. Sofia Navarro', 'blood_type' => 'A+'],
            ['patient_number' => 'PAT-1005', 'first_name' => 'Patricia', 'last_name' => 'Flores', 'age' => 38, 'gender' => 'Female', 'phone' => '+63 921 567 8901', 'provider' => 'Dr. Daniel Cruz', 'blood_type' => 'O+'],
            ['patient_number' => 'PAT-1006', 'first_name' => 'Ruth', 'last_name' => 'Adeyemi', 'age' => 31, 'gender' => 'Female', 'phone' => '+63 923 789 0123', 'provider' => 'Dr. Adrian Reyes', 'blood_type' => null],
            ['patient_number' => 'PAT-1007', 'first_name' => 'Luke', 'last_name' => 'Henderson', 'age' => 45, 'gender' => 'Male', 'phone' => '+63 922 678 9012', 'provider' => null, 'blood_type' => null],
        ];

        $patients = [];

        foreach ($demoPatients as $demoPatient) {
            $patients[$demoPatient['patient_number']] = Patient::firstOrCreate(
                ['patient_number' => $demoPatient['patient_number']],
                [
                    'first_name' => $demoPatient['first_name'],
                    'last_name' => $demoPatient['last_name'],
                    'date_of_birth' => now()->subYears($demoPatient['age'])->toDateString(),
                    'gender' => $demoPatient['gender'],
                    'phone' => $demoPatient['phone'],
                    'provider' => $demoPatient['provider'],
                    'status' => 'active',
                    'blood_type' => $demoPatient['blood_type'],
                    'allergies' => $demoPatient['patient_number'] === 'PAT-1005' ? 'Penicillin' : null,
                ],
            );
        }

        $demoAppointments = [
            ['code' => 'APT-1001', 'patient' => 'PAT-1001', 'time' => '08:30:00', 'provider' => 'Dr. Adrian Reyes', 'type' => 'General Consultation', 'status' => 'completed'],
            ['code' => 'APT-1002', 'patient' => 'PAT-1002', 'time' => '09:00:00', 'provider' => 'Dr. Sofia Navarro', 'type' => 'Follow-up', 'status' => 'in_progress'],
            ['code' => 'APT-1003', 'patient' => 'PAT-1003', 'time' => '09:30:00', 'provider' => 'Dr. Camille Garcia', 'type' => 'Laboratory Review', 'status' => 'scheduled'],
            ['code' => 'APT-1004', 'patient' => 'PAT-1004', 'time' => '10:00:00', 'provider' => 'Dr. Miguel Torres', 'type' => 'General Consultation', 'status' => 'scheduled'],
            ['code' => 'APT-1005', 'patient' => 'PAT-1005', 'time' => '10:30:00', 'provider' => 'Dr. Sofia Navarro', 'type' => 'Follow-up', 'status' => 'scheduled'],
            ['code' => 'APT-1006', 'patient' => 'PAT-1006', 'time' => '11:00:00', 'provider' => 'Dr. Daniel Cruz', 'type' => 'Laboratory Review', 'status' => 'pending'],
            ['code' => 'APT-1007', 'patient' => 'PAT-1007', 'time' => '11:30:00', 'provider' => 'Dr. Adrian Reyes', 'type' => 'Initial Assessment', 'status' => 'scheduled'],
        ];

        $appointments = [];

        foreach ($demoAppointments as $demoAppointment) {
            $appointments[$demoAppointment['code']] = Appointment::firstOrCreate(
                ['appointment_code' => $demoAppointment['code']],
                [
                    'patient_id' => $patients[$demoAppointment['patient']]->id,
                    'scheduled_at' => now()->setTimeFromTimeString($demoAppointment['time']),
                    'provider' => $demoAppointment['provider'],
                    'appointment_type' => $demoAppointment['type'],
                    'status' => $demoAppointment['status'],
                ],
            );
        }

        $demoRecords = [
            [
                'record_number' => 'REC-0892',
                'patient' => 'PAT-1005',
                'appointment' => null,
                'title' => 'Cardiac Follow-up',
                'provider' => 'Dr. Daniel Cruz',
                'hours_ago' => 1,
                'subjective' => 'Patient reports mild chest tightness during exertion.',
                'objective' => 'BP 120/80 mmHg, HR 72 bpm. Cardiac notes updated.',
                'assessment' => 'Stable cardiac follow-up.',
                'plan' => 'Continue current medication regimen. Re-evaluate in 4 weeks.',
            ],
            [
                'record_number' => 'REC-0891',
                'patient' => 'PAT-1004',
                'appointment' => null,
                'title' => 'Follow-up & Discharge Summary',
                'provider' => 'Dr. Sofia Navarro',
                'hours_ago' => 3,
                'subjective' => 'Follow-up & Discharge summary',
                'objective' => 'Clinical observations are available in the complete patient record.',
                'assessment' => 'Record review in progress.',
                'plan' => 'Continue with the documented care plan and scheduled follow-up.',
            ],
            [
                'record_number' => 'REC-0890',
                'patient' => 'PAT-1001',
                'appointment' => 'APT-1001',
                'title' => 'General Consultation',
                'provider' => 'Dr. Adrian Reyes',
                'hours_ago' => 8,
                'subjective' => 'General Consultation SOAP note',
                'objective' => 'Clinical observations are available in the complete patient record.',
                'assessment' => 'Record review in progress.',
                'plan' => 'Continue with the documented care plan and scheduled follow-up.',
            ],
            [
                'record_number' => 'REC-0889',
                'patient' => 'PAT-1002',
                'appointment' => null,
                'title' => 'Laboratory Review',
                'provider' => 'Dr. Sofia Navarro',
                'hours_ago' => 2,
                'subjective' => 'Patient consultation record.',
                'objective' => 'Laboratory results are awaiting review.',
                'assessment' => 'Pending clinical assessment.',
                'plan' => 'Review laboratory results during the next consultation.',
            ],
            [
                'record_number' => 'REC-0888',
                'patient' => 'PAT-1003',
                'appointment' => null,
                'title' => 'Wound Dressing & Clinical Assessment',
                'provider' => 'Dr. Miguel Torres',
                'hours_ago' => 10,
                'subjective' => 'Patient attended wound dressing and assessment.',
                'objective' => 'Clinical observations recorded by the attending provider.',
                'assessment' => 'Wound assessment documented.',
                'plan' => 'Continue wound care and monitor healing progress.',
            ],
        ];

        $records = [];

        foreach ($demoRecords as $demoRecord) {
            $records[$demoRecord['record_number']] = MedicalRecord::firstOrCreate(
                ['record_number' => $demoRecord['record_number']],
                [
                    'patient_id' => $patients[$demoRecord['patient']]->id,
                    'appointment_id' => $demoRecord['appointment'] === null
                        ? null
                        : $appointments[$demoRecord['appointment']]->id,
                    'title' => $demoRecord['title'],
                    'provider' => $demoRecord['provider'],
                    'recorded_at' => now()->subHours($demoRecord['hours_ago']),
                    'subjective' => $demoRecord['subjective'],
                    'objective' => $demoRecord['objective'],
                    'assessment' => $demoRecord['assessment'],
                    'plan' => $demoRecord['plan'],
                ],
            );
        }

        LabOrder::firstOrCreate(
            ['order_number' => 'LAB-0037'],
            [
                'patient_id' => $patients['PAT-1005']->id,
                'medical_record_id' => $records['REC-0892']->id,
                'test_name' => 'Lab Panel',
                'ordered_by' => 'Dr. Daniel Cruz',
                'status' => 'resulted',
                'ordered_at' => now()->subDay(),
                'result' => 'Demo data only; no verified clinical result.',
                'result_at' => now()->subHours(2),
            ],
        );

        LabOrder::firstOrCreate(
            ['order_number' => 'LAB-0038'],
            [
                'patient_id' => $patients['PAT-1002']->id,
                'appointment_id' => $appointments['APT-1002']->id,
                'test_name' => 'Laboratory Review',
                'ordered_by' => 'Dr. Sofia Navarro',
                'status' => 'ordered',
                'ordered_at' => now(),
            ],
        );
    }
}
