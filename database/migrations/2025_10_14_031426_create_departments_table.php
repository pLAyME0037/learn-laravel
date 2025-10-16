<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->foreignId('hod_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('office_location')->nullable();
            $table->integer('established_year')->nullable();
            $table->decimal('budget', 15, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(){
        Schema::dropIfExists('departments');
    }
};
