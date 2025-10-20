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
Schema::create('class_schedules', function (Blueprint $table) {
    $table->id();
    $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
    $table->foreignId('professor_id')->constrained('users')->onDelete('cascade');
    $table->string('room_number');
    $table->integer('capacity');
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_schedules');
    }
};
