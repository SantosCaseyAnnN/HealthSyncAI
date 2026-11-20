<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('check_ins', function (Blueprint $table) {
            $table->foreignId('patient_id')->nullable()->change();
            $table->string('existing_patient_number', 30)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::table('check_ins')->whereNull('patient_id')->exists()) {
            throw new RuntimeException('Cannot require a patient link while unlinked public check-in requests exist.');
        }

        Schema::table('check_ins', function (Blueprint $table) {
            $table->dropColumn('existing_patient_number');
            $table->foreignId('patient_id')->nullable(false)->change();
        });
    }
};
