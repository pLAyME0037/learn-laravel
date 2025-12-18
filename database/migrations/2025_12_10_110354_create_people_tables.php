<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Students
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('program_id')->nullable()->constrained()->nullOnDelete();
            $table->string('student_id')->unique(); // Generated ID

            // Academic Progress
            $table->integer('year_level')->default(1);
            $table->integer('current_term')->default(1); // 1-8
            $table->decimal('cgpa', 3, 2)->default(0.00);
            $table->integer('total_credits_earned')->default(0)->after('cgpa');
            // active, probation (Strings are safer than Enums for portability)
            $table->string('academic_status')->default('active'); 
            $table->boolean('has_outstanding_balance')->default(false)->after('academic_status');
            // diability
            $table->boolean('has_disability')->default(false);
            $table->text('disability_details')->nullable()->after('has_disability');

            // This stores: { "blood_group": "A+", "passport": "...", "nationality": "..." }
            $table->json('attributes')->nullable();

            // Encrypted JSON: { "id_card": "..." } Data (Stored as TEXT to handle long encrypted strings)
            $table->text('sensitive_data')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        // Instructors
        Schema::create('instructors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->string('staff_id')->unique()->nullable();
            $table->json('attributes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Polymorphic Addresses
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->morphs('addressable'); // student_id, instructor_id

            $table->text('current_address')->nullable(); // House number, Street name
            $table->string('postal_code')->nullable();

            // LINK TO EXTERNAL DATABASE
            // We store the ID from the 'villages' table in the 'sqlite_locations' DB.
            // Since it's a different DB, we CANNOT use foreign key constraints.
            $table->unsignedBigInteger('village_id')->nullable()->index();

            $table->timestamps();
            $table->softDeletes();
        });
        // Contacts
        Schema::create('contact_details', function (Blueprint $table) {
            $table->id();
            $table->morphs('contactable');
            $table->string('phone')->nullable();
            $table->string('emergency_name')->nullable();
            $table->string('emergency_phone')->nullable();
            $table->string('emergency_relation')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_details');
        Schema::dropIfExists('addresses');
        Schema::dropIfExists('instructors');
        Schema::dropIfExists('students');
    }
};
