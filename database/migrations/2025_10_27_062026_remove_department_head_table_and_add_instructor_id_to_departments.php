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
        Schema::dropIfExists('department_head');

        Schema::table('departments', function (Blueprint $table) {
            $table->foreignId('instructor_id')
                ->nullable()
                ->constrained('instructors')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->dropForeign(['instructor_id']);
            $table->dropColumn('instructor_id');
        });

        Schema::create('department_head', function (Blueprint $table) {
            $table->integer('dept_number');
            $table->integer('head');
            $table->timestamps();

            $table->primary('dept_number');
            $table->foreign('dept_number')->references('dept_number')->on('departments')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('head')->references('instructor_id')->on('instructors');
        });
    }
};
