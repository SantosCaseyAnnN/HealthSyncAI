<?php

namespace Database\Factories;

use App\Models\LabOrder;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LabOrder>
 */
class LabOrderFactory extends Factory
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
            'medical_record_id' => null,
            'order_number' => fake()->unique()->numerify('LAB-####'),
            'test_name' => fake()->randomElement(['Complete Blood Count', 'Metabolic Panel', 'Lipid Panel']),
            'ordered_by' => fake()->name(),
            'status' => 'ordered',
            'ordered_at' => now(),
            'result' => null,
            'result_at' => null,
        ];
    }
}
