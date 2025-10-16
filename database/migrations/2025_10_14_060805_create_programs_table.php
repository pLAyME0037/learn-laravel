<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->enum('level', ['certificate', 'diploma', 'associate', 'bachelor', 'master', 'doctoral'])->default('bachelor');
            $table->integer('duration_years')->default(4);
            $table->integer('total_semesters')->default(8);
            $table->integer('total_credits_required')->default(120);
            $table->decimal('tuition_fee', 10, 2)->nullable();
            $table->json('curriculum')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};