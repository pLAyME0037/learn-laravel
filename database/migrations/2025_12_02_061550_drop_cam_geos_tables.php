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
        Schema::dropIfExists('villages');
        Schema::dropIfExists('communes');
        Schema::dropIfExists('districts');
        Schema::dropIfExists('provinces');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Create Provinces
        Schema::create('provinces', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // e.g., "12"
            $table->string('name_kh');        // Phnom Penh
            $table->string('name_en');
            $table->string('type')->nullable(); // Province or Capital
            $table->timestamps();
        });

        // 2. Create Districts (Belongs to Province)
        Schema::create('districts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('province_id')->constrained()->onDelete('cascade');
            $table->string('code')->unique(); // e.g., "1201"
            $table->string('name_kh');
            $table->string('name_en');
            $table->string('type')->nullable(); // Khan or Srok
            $table->timestamps();
        });

        // 3. Create Communes (Belongs to District)
        Schema::create('communes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('district_id')->constrained()->onDelete('cascade');
            $table->string('code')->unique(); // e.g., "120101"
            $table->string('name_kh');
            $table->string('name_en');
            $table->string('type')->nullable(); // Sangkat or Khum
            $table->timestamps();
        });

        // 4. Create Villages (Belongs to Commune)
        Schema::create('villages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commune_id')->constrained()->onDelete('cascade');
            $table->string('code')->unique(); // e.g., "12010101"
            $table->string('name_kh');
            $table->string('name_en');
            $table->timestamps();
        });
    }
};
