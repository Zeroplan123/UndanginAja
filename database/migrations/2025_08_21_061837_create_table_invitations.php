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
        Schema::create('invitations', function (Blueprint $table) {
            $table->id();
            $table->string('bride_name');      
            $table->string('groom_name');     
            $table->date('wedding_date');
            $table->string('wedding_time')->nullable();
            $table->string('venue')->nullable();
            $table->text('location')->nullable(); // For backward compatibility
            $table->text('additional_notes')->nullable();
            $table->string('slug')->unique();
            $table->string('cover_image')->nullable();
            $table->timestamps();

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            $table->foreignId('template_id')
                  ->constrained('templates')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invitations');
    }
};
