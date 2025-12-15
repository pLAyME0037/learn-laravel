<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Replaces Enums. E.g., Category: 'gender', Key: 'male', Value: 'Male'
        Schema::create('dictionaries', function (Blueprint $table) {
            $table->id();
            $table->string('category')->index(); // e.g., 'academic_status', 'gender'
            $table->string('key');               // e.g., 'active', 'suspended'
            $table->string('label');             // e.g., 'Active', 'Suspended'
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['category', 'key']);
        });

        // System Configuration
        Schema::create('system_configs', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_configs');
        Schema::dropIfExists('dictionaries');
    }
};
