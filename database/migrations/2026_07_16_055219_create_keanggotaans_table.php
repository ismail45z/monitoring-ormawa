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
        Schema::create('keanggotaans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->onDelete('cascade');
            $table->foreignId('ormawa_id')->constrained('ormawa')->onDelete('cascade');
            $table->foreignId('periode_id')->constrained('periodes')->onDelete('cascade');
            $table->foreignId('jabatan_id')->constrained('jabatans')->onDelete('cascade');
            $table->date('tgl_masuk')->nullable();
            $table->date('tgl_selesai')->nullable();
            $table->enum('status', ['Aktif', 'Nonaktif'])->default('Aktif');
            $table->timestamps();
            
            // Unique constraint to prevent duplicate memberships in same periode
            $table->unique(['mahasiswa_id', 'ormawa_id', 'periode_id']);
        });

        // Insert default Jabatan & Periode
        $jabatanId = DB::table('jabatans')->insertGetId([
            'nama_jabatan' => 'Anggota',
            'deskripsi' => 'Jabatan default hasil migrasi',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $periodeId = DB::table('periodes')->insertGetId([
            'tahun' => '2023/2024',
            'semester' => 'Genap',
            'status' => 'Aktif',
            'tanggal_mulai' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Migrate data
        if (Schema::hasTable('mahasiswa_ormawa')) {
            $oldData = DB::table('mahasiswa_ormawa')->get();
            foreach($oldData as $d) {
                DB::table('keanggotaans')->insert([
                    'mahasiswa_id' => $d->mahasiswa_id,
                    'ormawa_id' => $d->ormawa_id,
                    'periode_id' => $periodeId,
                    'jabatan_id' => $jabatanId,
                    'tgl_masuk' => $d->created_at ?? now(),
                    'status' => 'Aktif',
                    'created_at' => $d->created_at ?? now(),
                    'updated_at' => $d->updated_at ?? now(),
                ]);
            }

            Schema::dropIfExists('mahasiswa_ormawa');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('mahasiswa_ormawa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->onDelete('cascade');
            $table->foreignId('ormawa_id')->constrained('ormawa')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['mahasiswa_id', 'ormawa_id']);
        });
        
        $keanggotaans = DB::table('keanggotaans')->get();
        // Since keanggotaans has periode, one mahasiswa could have multiple records for same ormawa. 
        // We group them to prevent constraint violation on down.
        $migrated = [];
        foreach($keanggotaans as $k) {
            $key = $k->mahasiswa_id . '-' . $k->ormawa_id;
            if(!isset($migrated[$key])) {
                DB::table('mahasiswa_ormawa')->insert([
                    'mahasiswa_id' => $k->mahasiswa_id,
                    'ormawa_id' => $k->ormawa_id,
                    'created_at' => $k->created_at,
                    'updated_at' => $k->updated_at,
                ]);
                $migrated[$key] = true;
            }
        }
        
        Schema::dropIfExists('keanggotaans');
    }
};
