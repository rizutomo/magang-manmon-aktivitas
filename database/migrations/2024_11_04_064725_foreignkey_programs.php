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
        Schema::table('programs', function (Blueprint $table) {
            $table->dropForeign(['supervisor_id']);
            $table->renameColumn('supervisor_id', 'sector_id');
            $table->foreign('sector_id')->references('id')->on('sectors')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->dropForeign(['sector_id']);
            $table->renameColumn('sector_id', 'supervisor_id');
            $table->foreign('supervisor_id')->references('id')->on('sectors')->onDelete('cascade');
        });
    }
};
