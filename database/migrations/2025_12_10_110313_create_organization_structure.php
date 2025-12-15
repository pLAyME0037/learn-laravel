<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // "2025-2026"
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_current')->default(false);
            $table->timestamps();
        });

        Schema::create('semesters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // "Fall", "Spring"
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        Schema::create('faculties', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('faculty_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('code')->unique();
            // JSON for contact info (email, phone, location)
            $table->json('contact_info')->nullable(); 
            $table->timestamps();
        });

        Schema::create('degrees', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Bachelor, Master
            $table->timestamps();
        });

        Schema::create('majors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->foreignId('degree_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->decimal('cost_per_term', 10, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('major_id')->constrained()->cascadeOnDelete();
            $table->foreignId('degree_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // "Bachelor of Computer Science"
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('programs');
        Schema::dropIfExists('majors');
        Schema::dropIfExists('degrees');
        Schema::dropIfExists('departments');
        Schema::dropIfExists('faculties');
        Schema::dropIfExists('semesters');
        Schema::dropIfExists('academic_years');
    }
};