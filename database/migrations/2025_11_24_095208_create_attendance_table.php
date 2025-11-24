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
        Schema::create('attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('date');
            $table->time('time');
            $table->enum('status', ['hadir', 'terlambat', 'alpha'])->default('hadir');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade'); // Teacher who recorded
            $table->text('notes')->nullable(); // Optional notes
            $table->timestamps();

            // Ensure one attendance per student per day
            $table->unique(['user_id', 'date']);

            // Index for better performance
            $table->index(['date', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance');
    }
};
