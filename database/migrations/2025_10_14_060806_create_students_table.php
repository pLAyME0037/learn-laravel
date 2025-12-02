<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('department_id')->constrained()->onDelete('restrict');
            $table->foreignId('program_id')->nullable()->constrained()->onDelete('set null');
            
            // Student Identification
            $table->string('student_id')->unique();
            $table->string('registration_number')->unique()->nullable();
            
            // Personal Information
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('nationality')->nullable();
            $table->string('id_card_number')->nullable();
            $table->string('passport_number')->nullable();
            
            // contact in contact detail
            // Address Information in addresses table
            
            // Academic Information
            $table->date('admission_date');
            $table->date('expected_graduation')->nullable();
            $table->integer('current_semester')->default(1);
            $table->decimal('cgpa', 3, 2)->default(0.00);
            $table->integer('total_credits_earned')->default(0);
            
            // Academic Status
            $table->enum('academic_status', ['active', 'probation', 'suspended', 'graduated', 'withdrawn', 'transfered'])->default('active');
            $table->enum('enrollment_status', ['full_time', 'part_time', 'exchange', 'study_abroad'])->default('full_time');
            $table->integer('year_level')->default(1)->after('user_id');
            $table->enum('semester', ['semester_one', 'semester_two']);

            // Financial Information
            $table->enum('fee_category', ['regular', 'scholarship', 'financial_aid', 'self_financed'])->default('regular');
            $table->boolean('has_outstanding_balance')->default(false);
            
            // Additional Information
            $table->text('previous_education')->nullable();
            $table->string('blood_group')->nullable();
            $table->boolean('has_disability')->default(false);
            $table->text('disability_details')->nullable();
            $table->json('metadata')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['student_id']);
            $table->index(['academic_status']);
            $table->index(['department_id', 'academic_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};