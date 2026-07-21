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
        Schema::table('anggota_requests', function (Blueprint $table) {
            $table->integer('cooldown_hari')->nullable()->after('catatan_pengurus');
            $table->boolean('is_permanen')->default(false)->after('cooldown_hari');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('anggota_requests', function (Blueprint $table) {
            $table->dropColumn(['cooldown_hari', 'is_permanen']);
        });
    }
};
