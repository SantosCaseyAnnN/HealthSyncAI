<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->decimal('height_cm', 5, 2)->nullable();
            $table->decimal('weight_kg', 5, 2)->nullable();
            $table->decimal('bmi', 4, 2)->nullable();
            $table->string('blood_pressure', 20)->nullable();
            $table->decimal('temperature_c', 4, 1)->nullable();
            $table->unsignedTinyInteger('pulse_rate')->nullable();
            $table->unsignedTinyInteger('respiratory_rate')->nullable();
            $table->decimal('oxygen_saturation', 5, 2)->nullable();
        });

        Schema::table('check_ins', function (Blueprint $table) {
            $table->string('gender', 20)->nullable();
            $table->decimal('height_cm', 5, 2)->nullable();
            $table->decimal('weight_kg', 5, 2)->nullable();
            $table->string('blood_pressure', 20)->nullable();
            $table->decimal('temperature_c', 4, 1)->nullable();
            $table->unsignedTinyInteger('pulse_rate')->nullable();
            $table->unsignedTinyInteger('respiratory_rate')->nullable();
            $table->decimal('oxygen_saturation', 5, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn([
                'height_cm',
                'weight_kg',
                'bmi',
                'blood_pressure',
                'temperature_c',
                'pulse_rate',
                'respiratory_rate',
                'oxygen_saturation',
            ]);
        });

        Schema::table('check_ins', function (Blueprint $table) {
            $table->dropColumn([
                'gender',
                'height_cm',
                'weight_kg',
                'blood_pressure',
                'temperature_c',
                'pulse_rate',
                'respiratory_rate',
                'oxygen_saturation',
            ]);
        });
    }
};
