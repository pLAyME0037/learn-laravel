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
        Schema::table('academic_records', function (Blueprint $table) {
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->foreignId('semester_id')->constrained('semesters')->onDelete('cascade');
            $table->string('grade')->nullable();
            $table->integer('credits_earned')->default(0);
            $table->unique(['student_id', 'course_id', 'semester_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('academic_records', function (Blueprint $table) {
            $table->dropForeign(['student_id']);
            $table->dropColumn('student_id');
            $table->dropForeign(['course_id']);
            $table->dropColumn('course_id');
            $table->dropForeign(['semester_id']);
            $table->dropColumn('semester_id');
            $table->dropColumn('grade');
            $table->dropColumn('credits_earned');
        });
    }
};
