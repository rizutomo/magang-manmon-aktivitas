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
        Schema::create('reports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('task_id');
            $table->string('photo')->nullable();
            $table->string('description')->nullable();
            $table->string('longitude')->nullable();
            $table->string('latitude')->nullable();
            $table->date('date')->nullable();
            $table->enum('status', ['Diserahkan', 'Diterima', 'Dikembalikan'])->default('Diserahkan');
            $table->string('modified_by')->nullable();
            $table->string('comment')->nullable();
            $table->timestamps();

            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
            $table->foreign('modified_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
