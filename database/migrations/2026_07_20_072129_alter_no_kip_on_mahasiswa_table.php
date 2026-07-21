<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Drop the unique constraint
        Schema::table('mahasiswa', function (Blueprint $table) {
            $table->dropUnique(['no_kip']);
        });

        // 2. Change column type to TEXT to accommodate encrypted string
        Schema::table('mahasiswa', function (Blueprint $table) {
            $table->text('no_kip')->change();
        });

        // 3. Encrypt existing data
        $mahasiswas = DB::table('mahasiswa')->get();
        foreach ($mahasiswas as $mhs) {
            // Check if it's already encrypted (starts with 'eyJ')
            if (!\Illuminate\Support\Str::startsWith($mhs->no_kip, 'eyJ')) {
                DB::table('mahasiswa')
                    ->where('id', $mhs->id)
                    ->update([
                        'no_kip' => Crypt::encryptString($mhs->no_kip)
                    ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Decrypt data
        $mahasiswas = DB::table('mahasiswa')->get();
        foreach ($mahasiswas as $mhs) {
            if (\Illuminate\Support\Str::startsWith($mhs->no_kip, 'eyJ')) {
                try {
                    $decrypted = Crypt::decryptString($mhs->no_kip);
                    DB::table('mahasiswa')
                        ->where('id', $mhs->id)
                        ->update([
                            'no_kip' => $decrypted
                        ]);
                } catch (\Exception $e) {
                    // Ignore decryption errors on rollback
                }
            }
        }

        // 2. Revert column back to string(50)
        Schema::table('mahasiswa', function (Blueprint $table) {
            $table->string('no_kip', 50)->change();
        });

        // 3. Add back unique constraint
        Schema::table('mahasiswa', function (Blueprint $table) {
            $table->unique('no_kip');
        });
    }
};
