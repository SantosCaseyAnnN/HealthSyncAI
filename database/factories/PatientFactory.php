<?php

namespace Database\Factories;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Patient>
 */
class PatientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'patient_number' => fake()->unique()->numerify('PAT-####'),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'date_of_birth' => fake()->dateTimeBetween('-90 years', '-18 years')->format('Y-m-d'),
            'gender' => fake()->randomElement(['Female', 'Male', 'Other']),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->safeEmail(),
            'provider' => fake()->name(),
            'status' => 'active',
            'blood_type' => fake()->randomElement(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']),
            'allergies' => null,
        ];
    }
}
