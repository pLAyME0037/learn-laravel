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
        Schema::create('majors', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->foreignId('department_id')->constrained('departments')->onDelete('cascade');
            $table->foreignId('degree_id')->constrained('degrees')->onDelete('cascade');
            $table->decimal('cost', 10, 2);
            $table->enum('payment_frequency', ['term', 'year']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('majors');
    }
};
