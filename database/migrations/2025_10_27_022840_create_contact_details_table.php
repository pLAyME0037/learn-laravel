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
        Schema::create('contact_details', function (Blueprint $table) {
            $table->unsignedBigInteger('person_id');
            $table->string('email', 50)->unique();
            $table->string('address', 100);
            $table->string('phone_number', 20)->unique();
            $table->timestamps();
            $table->softDeletes();
            $table->primary('person_id');
            $table->foreign('person_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_details');
    }
};
