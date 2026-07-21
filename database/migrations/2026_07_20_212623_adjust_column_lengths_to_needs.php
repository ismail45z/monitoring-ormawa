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
        Schema::table('pengguna', function (Blueprint $table) {
            $table->string('nama', 60)->change();
            $table->string('email', 100)->change();
        });

        Schema::table('mahasiswa', function (Blueprint $table) {
            $table->string('nim', 20)->change();
            // no_kip is encrypted text, cannot be reduced to 50
            $table->string('jurusan', 60)->change();
            $table->string('prodi', 60)->change();
            $table->string('status_kip', 30)->change();
        });

        Schema::table('ormawa', function (Blueprint $table) {
            $table->string('nama_ormawa', 60)->change();
            $table->string('jenis', 50)->change();
            $table->string('periode', 20)->change();
            $table->string('ketua', 60)->nullable()->change();
            $table->string('pembina', 60)->nullable()->change();
            $table->string('kategori_jurusan', 60)->nullable()->change();
            $table->string('kategori_prodi', 60)->nullable()->change();
        });

        Schema::table('kegiatan', function (Blueprint $table) {
            $table->string('nama_kegiatan', 150)->change();
            $table->string('jenis', 50)->nullable()->change();
            $table->string('tempat', 150)->change();
        });

        Schema::table('periodes', function (Blueprint $table) {
            $table->string('tahun', 20)->change();
            $table->string('semester', 20)->nullable()->change();
        });

        Schema::table('jurusans', function (Blueprint $table) {
            $table->string('nama', 60)->change();
        });

        Schema::table('prodis', function (Blueprint $table) {
            $table->string('nama', 60)->change();
        });

        Schema::table('jabatans', function (Blueprint $table) {
            $table->string('nama_jabatan', 50)->change();
        });

        Schema::table('pengumuman', function (Blueprint $table) {
            $table->string('judul', 150)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengguna', function (Blueprint $table) {
            $table->string('nama', 255)->change();
            $table->string('email', 255)->change();
        });

        Schema::table('mahasiswa', function (Blueprint $table) {
            $table->string('nim', 255)->change();
            $table->string('jurusan', 255)->change();
            $table->string('prodi', 255)->change();
            $table->string('status_kip', 255)->change();
        });

        Schema::table('ormawa', function (Blueprint $table) {
            $table->string('nama_ormawa', 255)->change();
            $table->string('jenis', 255)->change();
            $table->string('periode', 255)->change();
            $table->string('ketua', 255)->nullable()->change();
            $table->string('pembina', 255)->nullable()->change();
            $table->string('kategori_jurusan', 255)->nullable()->change();
            $table->string('kategori_prodi', 255)->nullable()->change();
        });

        Schema::table('kegiatan', function (Blueprint $table) {
            $table->string('nama_kegiatan', 255)->change();
            $table->string('jenis', 255)->nullable()->change();
            $table->string('tempat', 255)->change();
        });

        Schema::table('periodes', function (Blueprint $table) {
            $table->string('tahun', 255)->change();
            $table->string('semester', 255)->nullable()->change();
        });

        Schema::table('jurusans', function (Blueprint $table) {
            $table->string('nama', 255)->change();
        });

        Schema::table('prodis', function (Blueprint $table) {
            $table->string('nama', 255)->change();
        });

        Schema::table('jabatans', function (Blueprint $table) {
            $table->string('nama_jabatan', 255)->change();
        });

        Schema::table('pengumuman', function (Blueprint $table) {
            $table->string('judul', 255)->change();
        });
    }
};
