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
        Schema::table('ormawa', function (Blueprint $table) {
            $table->string('kategori_jurusan')->nullable()->after('is_open_recruitment');
            $table->string('kategori_prodi')->nullable()->after('kategori_jurusan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ormawa', function (Blueprint $table) {
            $table->dropColumn(['kategori_jurusan', 'kategori_prodi']);
        });
    }
};
