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
            $table->text('medical_history')->nullable();
            $table->text('family_history')->nullable();
            $table->string('emergency_contact_name', 150)->nullable();
            $table->string('emergency_contact_relationship', 100)->nullable();
            $table->string('emergency_contact_phone', 30)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn([
                'medical_history',
                'family_history',
                'emergency_contact_name',
                'emergency_contact_relationship',
                'emergency_contact_phone',
            ]);
        });
    }
};
