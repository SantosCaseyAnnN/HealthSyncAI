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
        Schema::table('patients', function (Blueprint $table): void {
            $table->string('middle_name', 100)->nullable()->after('first_name');
        });

        Schema::table('check_ins', function (Blueprint $table): void {
            $table->string('middle_name', 100)->nullable()->after('first_name');
            $table->foreignId('possible_match_patient_id')
                ->nullable()
                ->after('patient_id')
                ->constrained('patients')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('check_ins', function (Blueprint $table): void {
            $table->dropForeign(['possible_match_patient_id']);
            $table->dropColumn(['middle_name', 'possible_match_patient_id']);
        });

        Schema::table('patients', function (Blueprint $table): void {
            $table->dropColumn('middle_name');
        });
    }
};
