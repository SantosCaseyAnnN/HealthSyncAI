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
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('appointment_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('prescribed_by_user_id')->constrained('users')->restrictOnDelete();
            $table->string('medication', 150);
            $table->string('strength', 100)->nullable();
            $table->string('dosage', 150);
            $table->string('route', 50)->nullable();
            $table->string('frequency', 150);
            $table->string('duration', 100)->nullable();
            $table->text('instructions')->nullable();
            $table->string('status', 20)->default('active');
            $table->dateTime('issued_at');
            $table->timestamps();

            $table->index(['patient_id', 'issued_at']);
            $table->index(['status', 'issued_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescriptions');
    }
};
