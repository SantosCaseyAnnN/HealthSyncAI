<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\Prescription;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Prescription>
 */
class PrescriptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'appointment_id' => null,
            'prescribed_by_user_id' => User::factory(),
            'medication' => fake()->word(),
            'strength' => '250 mg',
            'dosage' => '1 tablet',
            'route' => 'Oral',
            'frequency' => 'Twice daily',
            'duration' => '7 days',
            'instructions' => fake()->sentence(),
            'status' => 'active',
            'issued_at' => now(),
        ];
    }
}
