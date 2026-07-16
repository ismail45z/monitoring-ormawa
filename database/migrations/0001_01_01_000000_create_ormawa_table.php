<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ormawa', function (Blueprint $table) {
            $table->id();
            $table->string('nama_ormawa', 50)->unique();
            $table->string('jenis', 50); // e.g. BEM, HIMA, UKM
            $table->string('periode', 20); // e.g. 2024/2025
            $table->string('ketua', 50); // e.g. nama ketua ormawa
            $table->string('pembina', 50)->nullable();
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
