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
        // 1. Create the pivot table
        Schema::create('mahasiswa_ormawa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->onDelete('cascade');
            $table->foreignId('ormawa_id')->constrained('ormawa')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['mahasiswa_id', 'ormawa_id']);
        });

        // 2. Migrate existing ormawa_id data from mahasiswa table to pivot table
        $mahasiswas = DB::table('mahasiswa')->whereNotNull('ormawa_id')->get();
        foreach ($mahasiswas as $m) {
            DB::table('mahasiswa_ormawa')->insert([
                'mahasiswa_id' => $m->id,
                'ormawa_id' => $m->ormawa_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 3. Drop the old ormawa_id column from mahasiswa table
        Schema::table('mahasiswa', function (Blueprint $table) {
            $table->dropForeign(['ormawa_id']);
            $table->dropColumn('ormawa_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore ormawa_id column to mahasiswa
        Schema::table('mahasiswa', function (Blueprint $table) {
            $table->foreignId('ormawa_id')->nullable()->constrained('ormawa')->nullOnDelete();
        });

        // Migrate data back (take first ormawa for each student)
        $pivots = DB::table('mahasiswa_ormawa')->get();
        foreach ($pivots as $pivot) {
            DB::table('mahasiswa')
                ->where('id', $pivot->mahasiswa_id)
                ->whereNull('ormawa_id')
                ->update(['ormawa_id' => $pivot->ormawa_id]);
        }

        Schema::dropIfExists('mahasiswa_ormawa');
    }
};
