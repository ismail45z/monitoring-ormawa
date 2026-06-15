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
        Schema::create('ormawa', function (Blueprint $table) {
            $table->id();
            $table->string('nama_ormawa')->unique();
            $table->string('jenis'); // e.g. BEM, HIMA, UKM
            $table->string('periode'); // e.g. 2024/2025
            $table->string('ketua'); // e.g. nama ketua ormawa
            $table->string('pembina')->nullable();
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ormawa');
    }
};
