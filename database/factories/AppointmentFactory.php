<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Appointment>
 */
class AppointmentFactory extends Factory
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
            'appointment_code' => fake()->unique()->numerify('APT-####'),
            'scheduled_at' => fake()->dateTimeBetween('-1 month', '+1 month'),
            'provider' => fake()->name(),
            'appointment_type' => fake()->randomElement(['General Consultation', 'Follow-up', 'Laboratory Review']),
            'status' => fake()->randomElement(['pending', 'scheduled', 'in_progress', 'completed', 'cancelled']),
            'notes' => null,
        ];
    }
}
