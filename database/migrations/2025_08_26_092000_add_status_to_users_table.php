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
            $table->enum('status', ['active', 'banned', 'suspended'])->default('active')->after('role');
            $table->text('ban_reason')->nullable()->after('status');
            $table->timestamp('banned_at')->nullable()->after('ban_reason');
            $table->timestamp('ban_expires_at')->nullable()->after('banned_at');
            $table->timestamp('last_login_at')->nullable()->after('ban_expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['status', 'ban_reason', 'banned_at', 'ban_expires_at', 'last_login_at']);
        });
    }
};
