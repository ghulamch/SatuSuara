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
        // 1. Update votes table for cryptographic receipt & Kotak Kosong
        Schema::table('votes', function (Blueprint $table) {
            $table->string('receipt_hash', 64)->nullable()->unique()->after('voted_at');
            $table->boolean('is_kotak_kosong')->default(false)->after('candidate_id');
            $table->foreignId('candidate_id')->nullable()->change();
        });

        // 2. Update users table for TPS Check-in & QR Attendance Token
        Schema::table('users', function (Blueprint $table) {
            $table->string('attendance_token', 64)->nullable()->unique()->after('email');
            $table->timestamp('checked_in_at')->nullable()->after('attendance_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('votes', function (Blueprint $table) {
            $table->dropColumn(['receipt_hash', 'is_kotak_kosong']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['attendance_token', 'checked_in_at']);
        });
    }
};

