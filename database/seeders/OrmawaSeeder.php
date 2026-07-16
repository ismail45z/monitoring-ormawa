<?php

namespace Database\Seeders;

use App\Models\Ormawa;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrmawaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks to truncate safely
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Ormawa::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $ormawas = [
            [
                'nama_ormawa' => 'Majelis Permusyawaratan Mahasiswa (MPM)',
                'jenis' => 'MPM',
                'periode' => '2025/2026',
                'ketua' => 'Budi Hermanto',
                'pembina' => 'Drs. H. Syarifuddin, M.Si.',
                'deskripsi' => 'Lembaga legislatif tertinggi mahasiswa tingkat Politeknik Negeri Pontianak.',
            ],
            [
                'nama_ormawa' => 'IKMADIKSI',
                'jenis' => 'IKMADIKSI',
                'periode' => '2025/2026',
                'ketua' => 'Ahmad Rian',
                'pembina' => 'Novianti, S.E., M.M.',
                'deskripsi' => 'Ikatan Mahasiswa Penerima Bidikmisi dan KIP-Kuliah Polnep.',
            ],
            [
                'nama_ormawa' => 'Komisi Pemilihan Raya Mahasiswa (KPRM)',
                'jenis' => 'KPRM',
                'periode' => '2025/2026',
                'ketua' => 'Yusuf Habibi',
                'pembina' => 'Supriadi, S.T., M.T.',
                'deskripsi' => 'Komisi penyelenggara pemilihan umum raya mahasiswa Polnep.',
            ],
            [
                'nama_ormawa' => 'HMJ Teknik Mesin',
                'jenis' => 'HMJ',
                'periode' => '2025/2026',
                'ketua' => 'Reza Pahlevi',
                'pembina' => 'Ir. Muhammad Ali, M.T.',
                'deskripsi' => 'Himpunan Mahasiswa Jurusan Teknik Mesin.',
            ],
            [
                'nama_ormawa' => 'HMJ Teknik Sipil',
                'jenis' => 'HMJ',
                'periode' => '2025/2026',
                'ketua' => 'Faisal Anwar',
                'pembina' => 'Hj. Fitriani, S.T., M.Sc.',
                'deskripsi' => 'Himpunan Mahasiswa Jurusan Teknik Sipil.',
            ],
            [
                'nama_ormawa' => 'HMJ Teknik Elektro',
                'jenis' => 'HMJ',
                'periode' => '2025/2026',
                'ketua' => 'Galang Pratama',
                'pembina' => 'Herianto, S.T., M.Eng.',
                'deskripsi' => 'Himpunan Mahasiswa Jurusan Teknik Elektro.',
            ],
            [
                'nama_ormawa' => 'HMJ Teknik Arsitektur',
                'jenis' => 'HMJ',
                'periode' => '2025/2026',
                'ketua' => 'Siti Aminah',
                'pembina' => 'Eko Prasetyo, S.T., M.T.',
                'deskripsi' => 'Himpunan Mahasiswa Jurusan Teknik Arsitektur.',
            ],
            [
                'nama_ormawa' => 'HMJ Administrasi Bisnis',
                'jenis' => 'HMJ',
                'periode' => '2025/2026',
                'ketua' => 'Dewi Lestari',
                'pembina' => 'Dr. Marlina, S.Sos., M.Si.',
                'deskripsi' => 'Himpunan Mahasiswa Jurusan Administrasi Bisnis.',
            ],
            [
                'nama_ormawa' => 'HMJ Akuntansi',
                'jenis' => 'HMJ',
                'periode' => '2025/2026',
                'ketua' => 'Ferry Irawan',
                'pembina' => 'Sri Wahyuni, S.E., M.Ak.',
                'deskripsi' => 'Himpunan Mahasiswa Jurusan Akuntansi.',
            ],
            [
                'nama_ormawa' => 'HMJ Teknologi Pertanian',
                'jenis' => 'HMJ',
                'periode' => '2025/2026',
                'ketua' => 'Imron Rosyadi',
                'pembina' => 'Dr. Ir. Sunardi, M.Si.',
                'deskripsi' => 'Himpunan Mahasiswa Jurusan Teknologi Pertanian.',
            ],
            [
                'nama_ormawa' => 'HMJ Ilmu Kelautan dan Perikanan',
                'jenis' => 'HMJ',
                'periode' => '2025/2026',
                'ketua' => 'Riko Sanjaya',
                'pembina' => 'Ir. Hartono, M.Si.',
                'deskripsi' => 'Himpunan Mahasiswa Jurusan Ilmu Kelautan dan Perikanan.',
            ],
            [
                'nama_ormawa' => 'IMMSAH',
                'jenis' => 'IMMSAH',
                'periode' => '2025/2026',
                'ketua' => 'Hendra Wijaya',
                'pembina' => 'Ahmad Dahlan, S.Ag., M.Pd.I.',
                'deskripsi' => 'Ikatan Mahasiswa Muslim Syiar dan Dakwah.',
            ],
            [
                'nama_ormawa' => 'Keluarga Mahasiswa Katolik (KMK)',
                'jenis' => 'KMK',
                'periode' => '2025/2026',
                'ketua' => 'Petrus Kanisius',
                'pembina' => 'Ignatius Setiawan, S.T.',
                'deskripsi' => 'Wadah pembinaan rohani mahasiswa Katolik Polnep.',
            ],
            [
                'nama_ormawa' => 'Badan Koordinasi Mahasiswa Kristen (BKMK)',
                'jenis' => 'BKMK',
                'periode' => '2025/2026',
                'ketua' => 'Christian Ronaldo',
                'pembina' => 'Yohanes Saputra, M.Pd.',
                'deskripsi' => 'Wadah pembinaan rohani mahasiswa Kristen Protestan Polnep.',
            ],
            [
                'nama_ormawa' => 'UKM Karate',
                'jenis' => 'UKM',
                'periode' => '2025/2026',
                'ketua' => 'Dicky Wahyudi',
                'pembina' => 'Senpai Bambang Hermawan',
                'deskripsi' => 'Unit Kegiatan Mahasiswa di bidang bela diri Karate.',
            ],
            [
                'nama_ormawa' => 'UKM Taekwondo',
                'jenis' => 'UKM',
                'periode' => '2025/2026',
                'ketua' => 'Roni Doang',
                'pembina' => 'Sabeum Nim Joko Susilo',
                'deskripsi' => 'Unit Kegiatan Mahasiswa di bidang bela diri Taekwondo.',
            ],
            [
                'nama_ormawa' => 'UKM Pencak Silat',
                'jenis' => 'UKM',
                'periode' => '2025/2026',
                'ketua' => 'Wahyu Hidayat',
                'pembina' => 'Ki Hajar Dewantoro',
                'deskripsi' => 'Unit Kegiatan Mahasiswa di bidang seni bela diri asli Indonesia, Pencak Silat.',
            ],
            [
                'nama_ormawa' => 'UKM Futsal dan Sepak Bola',
                'jenis' => 'UKM',
                'periode' => '2025/2026',
                'ketua' => 'Alif Rahman',
                'pembina' => 'Rudi Hartono, S.Pd.',
                'deskripsi' => 'Unit Kegiatan Mahasiswa di bidang olahraga futsal dan sepak bola.',
            ],
            [
                'nama_ormawa' => 'UKM Basket',
                'jenis' => 'UKM',
                'periode' => '2025/2026',
                'ketua' => 'Kevin Sanjaya',
                'pembina' => 'Erick Thohir, M.B.A.',
                'deskripsi' => 'Unit Kegiatan Mahasiswa pengembangan bakat olahraga basket.',
            ],
            [
                'nama_ormawa' => 'UKM Catur',
                'jenis' => 'UKM',
                'periode' => '2025/2026',
                'ketua' => 'Utut Adianto',
                'pembina' => 'Susanto Megaranto',
                'deskripsi' => 'Unit Kegiatan Mahasiswa olahraga asah otak catur.',
            ],
            [
                'nama_ormawa' => 'UKM Tenis Meja',
                'jenis' => 'UKM',
                'periode' => '2025/2026',
                'ketua' => 'Fadli Zon',
                'pembina' => 'Anton Susilo',
                'deskripsi' => 'Unit Kegiatan Mahasiswa cabang olahraga tenis meja.',
            ],
            [
                'nama_ormawa' => 'UKM Badminton',
                'jenis' => 'UKM',
                'periode' => '2025/2026',
                'ketua' => 'Taufik Hidayat',
                'pembina' => 'Hendra Setiawan',
                'deskripsi' => 'Unit Kegiatan Mahasiswa di bidang olahraga bulu tangkis.',
            ],
            [
                'nama_ormawa' => 'UKM Voli',
                'jenis' => 'UKM',
                'periode' => '2025/2026',
                'ketua' => 'Rivan Nurmulki',
                'pembina' => 'Yolla Yuliana',
                'deskripsi' => 'Unit Kegiatan Mahasiswa di bidang olahraga bola voli.',
            ],
            [
                'nama_ormawa' => 'UKM Panahan',
                'jenis' => 'UKM',
                'periode' => '2025/2026',
                'ketua' => 'Ratih Kumala',
                'pembina' => 'Donald Pandiangan',
                'deskripsi' => 'Unit Kegiatan Mahasiswa pengembangan fokus dan olahraga panahan.',
            ],
            [
                'nama_ormawa' => 'UKM MAPA',
                'jenis' => 'UKM',
                'periode' => '2025/2026',
                'ketua' => 'Soe Hok Gie',
                'pembina' => 'Taufiq Ismail',
                'deskripsi' => 'Unit Kegiatan Mahasiswa pencinta alam dan petualangan lingkungan.',
            ],
            [
                'nama_ormawa' => 'UKM IPTEK',
                'jenis' => 'UKM',
                'periode' => '2025/2026',
                'ketua' => 'Ainun Habibie',
                'pembina' => 'Prof. Dr. Warsito',
                'deskripsi' => 'Unit Kegiatan Mahasiswa pengembangan ilmu pengetahuan dan teknologi.',
            ],
            [
                'nama_ormawa' => 'UKM Japanese Club',
                'jenis' => 'UKM',
                'periode' => '2025/2026',
                'ketua' => 'Kenshin Himura',
                'pembina' => 'Dr. Tanaka',
                'deskripsi' => 'Unit Kegiatan Mahasiswa pertukaran budaya dan bahasa Jepang.',
            ],
            [
                'nama_ormawa' => 'UKM Drumband',
                'jenis' => 'UKM',
                'periode' => '2025/2026',
                'ketua' => 'Dwi Handoyo',
                'pembina' => 'Mayor Inf. Soetjipto',
                'deskripsi' => 'Unit Kegiatan Mahasiswa seni musik drumband/marching band.',
            ],
            [
                'nama_ormawa' => 'UKM POWERS',
                'jenis' => 'UKM',
                'periode' => '2025/2026',
                'ketua' => 'Megaawati',
                'pembina' => 'Puan Maharani',
                'deskripsi' => 'Unit Kegiatan Mahasiswa pengembangan kepemimpinan dan relasi sosial.',
            ],
            [
                'nama_ormawa' => 'UKM Modelling dan Photography',
                'jenis' => 'UKM',
                'periode' => '2025/2026',
                'ketua' => 'Rio Motret',
                'pembina' => 'Darwis Triadi',
                'deskripsi' => 'Unit Kegiatan Mahasiswa seni fotografi dan pemodelan.',
            ],
            [
                'nama_ormawa' => 'UKM PIK-M Unit Polnep',
                'jenis' => 'UKM',
                'periode' => '2025/2026',
                'ketua' => 'Siska Amelia',
                'pembina' => 'Dra. Endang Lestari',
                'deskripsi' => 'Pusat Informasi dan Konseling Mahasiswa Polnep.',
            ],
            [
                'nama_ormawa' => 'UKM KSR PMI Unit Polnep',
                'jenis' => 'UKM',
                'periode' => '2025/2026',
                'ketua' => 'Gerry Pratama',
                'pembina' => 'dr. Adnan Spesialis',
                'deskripsi' => 'Korps Sukarela Palang Merah Indonesia unit Polnep.',
            ],
            [
                'nama_ormawa' => 'UKM Seni',
                'jenis' => 'UKM',
                'periode' => '2025/2026',
                'ketua' => 'Didik Nini Thowok',
                'pembina' => 'Sujiwo Tejo',
                'deskripsi' => 'Unit Kegiatan Mahasiswa pengembangan bakat seni tari, musik, dan teater.',
            ],
            [
                'nama_ormawa' => 'UKM LPM Terkam',
                'jenis' => 'UKM',
                'periode' => '2025/2026',
                'ketua' => 'Najwa Shihab',
                'pembina' => 'Rosianna Silalahi',
                'deskripsi' => 'Lembaga Pers Mahasiswa Penerbitan Teropong Kampus (Terkam).',
            ],
            [
                'nama_ormawa' => 'UKM E-Sport',
                'jenis' => 'UKM',
                'periode' => '2025/2026',
                'ketua' => 'Jess No Limit',
                'pembina' => 'Lemon Esport',
                'deskripsi' => 'Unit Kegiatan Mahasiswa pengembangan bakat gaming kompetitif.',
            ],
        ];

        foreach ($ormawas as $ormawa) {
            Ormawa::create($ormawa);
        }
    }
}
