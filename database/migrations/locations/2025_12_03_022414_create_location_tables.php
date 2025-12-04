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
        Schema::create('province', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('camdx_id')->nullable()->index(); // Nullable
            $table->string('code', 10)->index();                   // Changed CHAR(2) to String(10) to be safe
            $table->string('name_kh')->nullable();
            $table->string('name_en')->nullable();
            $table->timestamps();
        });

        // 2. DISTRICT
        Schema::create('district', function (Blueprint $table) {
            $table->id();
            // Remove constrained() for import safety, add index manually
            $table->foreignId('province_id')->index();
            $table->unsignedInteger('camdx_id')->nullable()->index();
            $table->string('code', 10)->index();
            $table->string('name_kh')->nullable();
            $table->string('name_en')->nullable();
            $table->string('type')->nullable();
            $table->timestamps();
        });

        // 3. COMMUNE
        Schema::create('commune', function (Blueprint $table) {
            $table->id();
            $table->foreignId('district_id')->index();
            $table->unsignedInteger('camdx_id')->nullable()->index();
            $table->string('code', 10)->index();
            $table->string('name_kh')->nullable();
            $table->string('name_en')->nullable();
            $table->timestamps();
        });

        // 4. VILLAGE
        Schema::create('village', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commune_id')->index();
            $table->unsignedBigInteger('camdx_id')->nullable()->index();
            $table->string('code', 15)->index(); // Some village codes might be longer
            $table->string('name_kh')->nullable();
            $table->string('name_en')->nullable();

            // CRITICAL FIX: Must be nullable because the dump inserts NULL here
            $table->boolean('is_not_active')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        // Drop in reverse order to avoid Foreign Key constraints
        Schema::dropIfExists('village');
        Schema::dropIfExists('commune');
        Schema::dropIfExists('district');
        Schema::dropIfExists('province');
    }
};
