<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\PatientVaccination;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PatientVaccination>
 */
class PatientVaccinationFactory extends Factory
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
            'vaccine_name' => fake()->randomElement(['Influenza', 'Hepatitis B', 'Tetanus']),
            'dose' => fake()->randomElement(['Dose 1', 'Dose 2', 'Booster']),
            'administered_at' => fake()->dateTimeBetween('-5 years', 'today')->format('Y-m-d'),
            'administered_by' => fake()->name(),
            'lot_number' => fake()->optional()->bothify('LOT-????-####'),
            'notes' => null,
            'recorded_by_user_id' => null,
        ];
    }
}
