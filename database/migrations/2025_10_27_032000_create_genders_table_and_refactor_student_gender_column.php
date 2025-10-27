<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('genders', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        // Insert initial data
        DB::table('genders')->insert([
            ['name' => 'male', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'female', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'other', 'created_at' => now(), 'updated_at' => now()],
        ]);

        Schema::table('students', function (Blueprint $table) {
            // Drop the existing enum column
            $table->dropColumn('gender');
        });

        Schema::table('students', function (Blueprint $table) {
            // Add the new foreign key column
            $table->foreignId('gender_id')->nullable()->constrained('genders')->onDelete('set null')->after('date_of_birth');

            // Removed database-level check constraint for date_of_birth due to SQLite limitations.
            // This should be handled at the application level (e.g., in model validation rules).
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['gender_id']);
            $table->dropColumn('gender_id');
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('date_of_birth');
        });
        Schema::dropIfExists('genders');
    }
};
