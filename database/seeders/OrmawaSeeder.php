<?php

namespace Database\Seeders;

use App\Models\Ormawa;
use Illuminate\Database\Seeder;

class OrmawaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Ormawa::create([
            'nama_ormawa' => 'Badan Eksekutif Mahasiswa (BEM)',
            'jenis' => 'BEM',
            'periode' => '2025/2026',
            'ketua' => 'Fajar Nugraha',
            'pembina' => 'Dr. H. Ahmad Fauzi, M.T.',
            'deskripsi' => 'Organisasi eksekutif tertinggi mahasiswa di tingkat institut/universitas.',
        ]);

        Ormawa::create([
            'nama_ormawa' => 'Himpunan Mahasiswa Jurusan TI (HIMA TI)',
            'jenis' => 'HIMA',
            'periode' => '2025/2026',
            'ketua' => 'Rian Hidayat',
            'pembina' => 'Dewi Lestari, M.Kom.',
            'deskripsi' => 'Wadah kegiatan dan aspirasi mahasiswa program studi Teknologi Informasi.',
        ]);

        Ormawa::create([
            'nama_ormawa' => 'Unit Kegiatan Mahasiswa Olahraga (UKM OR)',
            'jenis' => 'UKM',
            'periode' => '2025/2026',
            'ketua' => 'Dimas Pratama',
            'pembina' => 'Prof. Dr. Ir. Budi Santoso',
            'deskripsi' => 'Unit kegiatan pengembangan minat dan bakat mahasiswa di bidang keolahragaan.',
        ]);
    }
}
