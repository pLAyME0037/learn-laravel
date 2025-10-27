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
        Schema::create('payments', function (Blueprint $table) {
            $table->increments('payment_id');
            $table->integer('student_id');
            $table->decimal('amount', 10, 2);
            $table->date('payment_date');
            $table->string('payment_period_description', 50);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('student_id')->references('student_id')->on('students');
            $table->index(['student_id']);
            $table->index(['payment_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
