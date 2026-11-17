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
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('role')->default('clinic_staff');
            $table->string('approval_status')->default('approved');
            $table->boolean('is_active')->default(true);
            $table->string('school_id')->nullable()->unique();
            $table->string('year_level')->nullable();
            $table->timestamp('school_id_verified_at')->nullable();
            $table->index(['role', 'approval_status', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role', 'approval_status', 'is_active']);
            $table->dropUnique(['school_id']);
            $table->dropColumn([
                'first_name',
                'middle_name',
                'last_name',
                'role',
                'approval_status',
                'is_active',
                'school_id',
                'year_level',
                'school_id_verified_at',
            ]);
        });
    }
};
