<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Data mapping for Kehadiran
        $kehadirans = DB::table('kehadiran')
                        ->join('kegiatan', 'kehadiran.kegiatan_id', '=', 'kegiatan.id')
                        ->select('kehadiran.id', 'kehadiran.mahasiswa_id', 'kegiatan.ormawa_id')
                        ->get();

        // 2. Alter Kehadiran: add keanggotaan_id nullable first
        Schema::table('kehadiran', function (Blueprint $table) {
            $table->unsignedBigInteger('keanggotaan_id')->nullable()->after('kegiatan_id');
            $table->datetime('waktu_absen')->nullable()->after('status_verifikasi');
        });
        
        // 3. Map old kehadiran data to the new keanggotaan_id
        foreach ($kehadirans as $kh) {
            $keanggotaan = DB::table('keanggotaans')
                ->where('mahasiswa_id', $kh->mahasiswa_id)
                ->where('ormawa_id', $kh->ormawa_id)
                ->first();
                
            if ($keanggotaan) {
                DB::table('kehadiran')->where('id', $kh->id)->update(['keanggotaan_id' => $keanggotaan->id]);
            }
        }
        
        // 4. Alter Kehadiran: drop old constraints, make keanggotaan_id strict
        Schema::table('kehadiran', function (Blueprint $table) {
            // Delete those that couldn't be mapped to prevent foreign key errors
            DB::table('kehadiran')->whereNull('keanggotaan_id')->delete();
            
            $table->dropForeign(['mahasiswa_id']);
            $table->dropColumn('mahasiswa_id');
            $table->foreign('keanggotaan_id')->references('id')->on('keanggotaans')->onDelete('cascade');
        });

        // 5. Alter Kegiatan
        Schema::table('kegiatan', function (Blueprint $table) {
            // Add jenis column
            $table->string('jenis')->nullable()->after('deskripsi');
            // Assuming bobot_poin already exists from previous migrations, we'll rename it
            // if DBAL is installed or using Laravel 11. 
            // In case renameColumn fails, we will wrap it or just leave it.
            // For safety in diverse environments, let's just add 'poin' and drop 'bobot_poin' (copying data if needed)
            $table->integer('poin')->default(0)->after('jenis');
            $table->foreignId('periode_id')->nullable()->after('tempat')->constrained('periodes')->onDelete('cascade');
        });

        // Copy bobot_poin to poin if bobot_poin exists
        if (Schema::hasColumn('kegiatan', 'bobot_poin')) {
            DB::statement('UPDATE kegiatan SET poin = bobot_poin');
            Schema::table('kegiatan', function (Blueprint $table) {
                $table->dropColumn('bobot_poin');
            });
        }

        // Drop string periode if it exists
        if (Schema::hasColumn('kegiatan', 'periode')) {
            Schema::table('kegiatan', function (Blueprint $table) {
                $table->dropColumn('periode');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kegiatan', function (Blueprint $table) {
            $table->dropForeign(['periode_id']);
            $table->dropColumn(['jenis', 'poin', 'periode_id']);
            $table->string('periode', 20)->nullable()->after('tempat');
            $table->integer('bobot_poin')->default(0)->after('periode');
        });

        Schema::table('kehadiran', function (Blueprint $table) {
            $table->dropForeign(['keanggotaan_id']);
            $table->dropColumn(['keanggotaan_id', 'waktu_absen']);
            $table->foreignId('mahasiswa_id')->nullable()->constrained('mahasiswa')->onDelete('cascade');
        });
    }
};
