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
        DB::table('users')
            ->where('role', 'student')
            ->update([
                'role' => 'disabled',
                'approval_status' => 'rejected',
                'is_active' => false,
            ]);

        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['school_id']);
            $table->dropColumn(['school_id', 'year_level', 'school_id_verified_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('school_id')->nullable()->unique();
            $table->string('year_level')->nullable();
            $table->timestamp('school_id_verified_at')->nullable();
        });

        DB::table('users')
            ->where('role', 'disabled')
            ->update(['role' => 'student']);
    }
};
