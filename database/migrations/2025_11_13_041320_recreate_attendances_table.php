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
        Schema::dropIfExists('attendances'); // Drop the existing table

        Schema::create('attendances', function (Blueprint $table) {
            $table->id('attendance_id'); // Use id() for auto-incrementing primary key
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade'); // Assuming student_id refers to users table
            $table->foreignId('class_schedule_id')->constrained('class_schedules')->onDelete('cascade');
            $table->date('date');
            $table->enum('status', ['present', 'absent', 'late'])->default('present'); // Added 'late' status
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['student_id', 'class_schedule_id', 'date']); // Ensure unique attendance per student per class per day
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
