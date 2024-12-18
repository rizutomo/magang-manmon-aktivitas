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

        Schema::table('reports', function (Blueprint $table) {
            $table->string('report_content_id');
            $table->foreign('report_content_id')->references('id')->on('report_contents')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn('report_content_id');
        });
    }
};
