<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anggota_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengurus_id')->constrained('pengguna')->onDelete('cascade'); // who submitted
            $table->foreignId('ormawa_id')->constrained('ormawa')->onDelete('cascade');
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->onDelete('cascade');
            $table->enum('tipe', ['tambah', 'hapus']); // add or remove
            $table->enum('status', ['pending', 'disetujui', 'ditolak'])->default('pending');
            $table->text('catatan')->nullable();        // note from pengurus
            $table->text('catatan_admin')->nullable();  // admin's response note
            $table->foreignId('admin_id')->nullable()->constrained('pengguna')->nullOnDelete(); // who processed
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anggota_requests');
    }
};
