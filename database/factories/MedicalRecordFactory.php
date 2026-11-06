<?php

namespace Database\Factories;

use App\Models\MedicalRecord;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MedicalRecord>
 */
class MedicalRecordFactory extends Factory
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
            'record_number' => fake()->unique()->numerify('REC-####'),
            'title' => fake()->randomElement(['General Consultation', 'Follow-up', 'Clinical Assessment']),
            'provider' => fake()->name(),
            'recorded_at' => fake()->dateTimeBetween('-1 month', 'now'),
            'subjective' => fake()->sentence(),
            'objective' => fake()->sentence(),
            'assessment' => fake()->sentence(),
            'plan' => fake()->sentence(),
        ];
    }
}
