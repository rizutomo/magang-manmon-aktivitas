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
        Schema::create('report_contents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('report_id');
            $table->string('photo')->nullable();
            $table->string('description')->nullable();
            $table->string('longitude')->nullable();
            $table->string('latitude')->nullable();
            $table->date('date')->nullable();
            // $table->string('status')->nullable();
            $table->timestamps();

            $table->foreign('report_id')->references('id')->on('reports')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_contents');
    }
};

