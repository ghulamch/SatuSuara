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
            $table->string('current_session_id', 255)->nullable()->after('remember_token');
            $table->timestamp('session_last_active_at')->nullable()->after('current_session_id');
            $table->string('session_ip', 45)->nullable()->after('session_last_active_at');
            $table->text('session_user_agent')->nullable()->after('session_ip');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'current_session_id',
                'session_last_active_at',
                'session_ip',
                'session_user_agent',
            ]);
        });
    }
};

