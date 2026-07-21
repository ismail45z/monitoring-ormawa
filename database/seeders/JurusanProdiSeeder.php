<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JurusanProdiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            'Akuntansi' => [
                'D3 Akuntansi',
                'D4 Sektor Publik',
                'D4 Perpajakan',
                'D4 Perbankan dan Keuangan Digital'
            ],
            'Administrasi Bisnis' => [
                'D3 Administrasi Bisnis',
                'D4 Administrasi Negara',
                'D4 Administrasi Bisnis Otomotif',
                'D4 Pengelolaan Usaha Rekreasi'
            ],
            'Teknik Arsitektur' => [
                'D3 Arsitektur',
                'D4 Desain kawasan Binaan',
                'D4 Arsitektur Bangunan Gedung'
            ],
            'Teknik Sipil' => [
                'D3 Teknik Sipil',
                'D4 Perencanaan Perumahan dan Pemukiman',
                'D4 Teknologi Rekayasa Jalan & Jembatan'
            ],
            'Teknik Mesin' => [
                'D1 Operator & Peralatan Alat Berat',
                'D3 Teknik Mesin',
                'D4 Teknik Mesin (Konversi Energi)'
            ],
            'Teknik Elektro' => [
                'D3 Teknik Listrik',
                'D3 Teknik Informatika',
                'D4 Teknologi Rekayasa Sistem Elektronika'
            ],
            'Teknologi Pertanian' => [
                'D4 Pengelolaan Hasil Perkebunan Terpadu',
                'D4 Manajemen Perkebunan',
                'D4 Budidaya Tanaman Perkebunan'
            ],
            'Ilmu Kelautan dan Perikanan' => [
                'D3 Budidaya Perikanan',
                'D3 Teknologi Penangkapan Ikan',
                'D4 Pengelolaan dan Penyimpanan Hasil perikanan'
            ]
        ];

        foreach ($data as $jurusanName => $prodis) {
            $jurusan = \App\Models\Jurusan::firstOrCreate(['nama' => $jurusanName]);
            foreach ($prodis as $prodiName) {
                \App\Models\Prodi::firstOrCreate([
                    'jurusan_id' => $jurusan->id,
                    'nama' => $prodiName
                ]);
            }
        }
    }
}
