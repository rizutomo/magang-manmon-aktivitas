<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->enum('status', ['Belum Diserahkan', 'Diserahkan', 'Diterima', 'Pending'])->default('Belum Diserahkan');
        });
    }

    public function down()
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn('status'); // Menghapus kolom 'status' saat rollback
        });
    }
};
