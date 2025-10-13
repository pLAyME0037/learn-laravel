<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('office_location')->nullable();
            $table->foreignId('head_of_department_id')->nullable()->constrained('users')->onDelete('set null');
            $table->integer('founded_year')->nullable();
            $table->decimal('budget', 15, 2)->nullable();
            $table->integer('total_faculty')->default(0);
            $table->integer('total_students')->default(0);
            $table->string('website')->nullable();
            $table->json('contact_info')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('display_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        // Insert sample departments
        DB::table('departments')->insert([
            [
                'name' => 'Computer Science',
                'code' => 'CS',
                'description' => 'Department of Computer Science and Information Technology',
                'email' => 'cs@university.edu',
                'phone' => '+1-555-0101',
                'office_location' => 'Tech Building, Room 301',
                'founded_year' => 1990,
                'budget' => 1500000.00,
                'website' => 'https://cs.university.edu',
                'is_active' => true,
                'display_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Electrical Engineering',
                'code' => 'EE',
                'description' => 'Department of Electrical and Electronics Engineering',
                'email' => 'ee@university.edu',
                'phone' => '+1-555-0102',
                'office_location' => 'Engineering Building, Room 201',
                'founded_year' => 1985,
                'budget' => 1200000.00,
                'website' => 'https://ee.university.edu',
                'is_active' => true,
                'display_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Business Administration',
                'code' => 'BA',
                'description' => 'Department of Business Administration and Management',
                'email' => 'ba@university.edu',
                'phone' => '+1-555-0103',
                'office_location' => 'Business Building, Room 101',
                'founded_year' => 1975,
                'budget' => 1800000.00,
                'website' => 'https://ba.university.edu',
                'is_active' => true,
                'display_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
