<?php

namespace Database\Factories;

use App\Models\CheckIn;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CheckIn>
 */
class CheckInFactory extends Factory
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
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'date_of_birth' => fake()->date(),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->safeEmail(),
            'reason_for_visit' => fake()->sentence(),
            'status' => 'pending',
            'check_in_date' => now()->toDateString(),
            'queue_number' => null,
            'reviewed_by_user_id' => null,
            'reviewed_at' => null,
        ];
    }
}
