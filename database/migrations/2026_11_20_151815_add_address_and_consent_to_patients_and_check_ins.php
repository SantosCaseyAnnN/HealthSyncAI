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
            $table->string('address')->nullable();
        });

        Schema::table('check_ins', function (Blueprint $table) {
            $table->string('address')->nullable();
            $table->boolean('consent_given')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn('address');
        });

        Schema::table('check_ins', function (Blueprint $table) {
            $table->dropColumn(['address', 'consent_given']);
        });
    }
};
