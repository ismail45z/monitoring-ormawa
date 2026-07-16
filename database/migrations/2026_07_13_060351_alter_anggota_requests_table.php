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
            $table->dropForeign(['pengurus_id']);
            $table->dropForeign(['admin_id']);
            
            $table->dropColumn(['pengurus_id', 'admin_id']);
            
            $table->foreignId('pemroses_id')->nullable()->constrained('pengguna')->nullOnDelete();
            $table->renameColumn('catatan_admin', 'catatan_pengurus');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('anggota_requests', function (Blueprint $table) {
            $table->foreignId('pengurus_id')->nullable()->constrained('pengguna')->onDelete('cascade');
            $table->foreignId('admin_id')->nullable()->constrained('pengguna')->nullOnDelete();
            
            $table->dropForeign(['pemroses_id']);
            $table->dropColumn('pemroses_id');
            
            $table->renameColumn('catatan_pengurus', 'catatan_admin');
        });
    }
};
