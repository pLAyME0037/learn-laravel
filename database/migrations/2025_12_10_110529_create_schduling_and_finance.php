<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // The Instance (Specific Class time)
        Schema::create('class_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('semester_id')->constrained()->cascadeOnDelete();
            $table->foreignId('instructor_id')->nullable()->constrained('users')->nullOnDelete();
            
            $table->string('section_name')->default('A');
            $table->integer('capacity')->default(40);
            
            // Simple Schedule
            $table->string('day_of_week')->nullable(); 
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('room')->nullable();
            
            $table->string('status')->default('open');
            $table->timestamps();
            $table->softDeletes();
        });

        // Enrollments
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_session_id')->constrained()->cascadeOnDelete();
            // Grades
            $table->decimal('final_grade', 5, 2)->nullable();
            $table->string('grade_letter')->nullable()->after('final_grade');
            $table->decimal('grade_points', 3, 2)->nullable()->after('grade_letter');
            
            $table->string('status')->default('enrolled'); // enrolled, dropped
            
            $table->timestamp('enrollment_date')->useCurrent();
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['student_id', 'class_session_id']);
        });

        // Finance
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->string('semester_name');
            $table->decimal('amount', 10, 2);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->string('status')->default('unpaid');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('enrollments');
        Schema::dropIfExists('class_sessions');
    }
};