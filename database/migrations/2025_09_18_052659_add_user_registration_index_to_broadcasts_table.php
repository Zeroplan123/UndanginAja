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
        Schema::table('broadcasts', function (Blueprint $table) {
            // Add index for sent_at to optimize user registration date filtering
            $table->index(['sent_at', 'is_active', 'status'], 'broadcasts_sent_active_status_index');
            
            // Add composite index for target filtering
            $table->index(['target_type', 'is_active', 'status'], 'broadcasts_target_active_status_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('broadcasts', function (Blueprint $table) {
            $table->dropIndex('broadcasts_sent_active_status_index');
            $table->dropIndex('broadcasts_target_active_status_index');
        });
    }
};
