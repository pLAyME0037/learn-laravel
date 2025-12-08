<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'sqlite_locations';

    public function up(): void
    {
        // 1. PROVINCE
        Schema::create('provinces', function (Blueprint $table) {
            $table->id(); // Auto-increment ID (e.g., 1, 2, 3)
            $table->integer('prov_id')->unique(); // GeoCode (e.g., 01, 02)
            $table->string('name_kh')->nullable();
            $table->string('name_en')->nullable();
        });

        // 2. DISTRICT
        Schema::create('districts', function (Blueprint $table) {
            $table->id();
            // Note: This refers to the 'prov_id' code, NOT the province 'id'
            $table->integer('province_id')->index(); 
            $table->integer('dist_id')->unique(); // GeoCode (e.g., 102)
            $table->string('name_kh')->nullable();
            $table->string('name_en')->nullable();
            $table->string('type')->nullable();
        });

        // 3. COMMUNE
        Schema::create('communes', function (Blueprint $table) {
            $table->id();
            // Note: This refers to the 'dist_id' code
            $table->integer('district_id')->index();
            $table->integer('comm_id')->unique(); // GeoCode (e.g., 10201)
            $table->string('name_kh')->nullable();
            $table->string('name_en')->nullable();
        });

        // 4. VILLAGE
        Schema::create('villages', function (Blueprint $table) {
            $table->id();
            // Note: This refers to the 'comm_id' code
            $table->integer('commune_id')->index(); 
            $table->bigInteger('vill_id')->unique(); // GeoCode (e.g., 1020101)
            $table->string('name_kh')->nullable();
            $table->string('name_en')->nullable();
            
            // Handling the "is_not_active" from JSON
            $table->boolean('is_not_active')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('villages');
        Schema::dropIfExists('communes');
        Schema::dropIfExists('districts');
        Schema::dropIfExists('provinces');
    }
};