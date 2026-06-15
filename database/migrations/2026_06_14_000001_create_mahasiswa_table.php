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
        Schema::create('mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengguna_id')->constrained('pengguna')->onDelete('cascade');
            $table->string('nim')->unique();
            $table->string('no_kip')->unique(); // e.g. nomor KIP-Kuliah
            $table->string('jurusan');
            $table->string('prodi');
            $table->integer('angkatan');
            $table->string('status_kip')->default('Aktif');
            $table->foreignId('ormawa_id')->nullable()->constrained('ormawa')->nullOnDelete(); // ormawa yang diikuti
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mahasiswa');
    }
};
