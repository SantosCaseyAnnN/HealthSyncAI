<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\PatientConsent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PatientConsent>
 */
class PatientConsentFactory extends Factory
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
            'consent_type' => fake()->randomElement(['Treatment', 'Privacy notice', 'Telehealth']),
            'form_version' => 'demo-1',
            'status' => fake()->randomElement(['granted', 'declined', 'revoked']),
            'recorded_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'recorded_by_user_id' => null,
            'notes' => null,
        ];
    }
}
