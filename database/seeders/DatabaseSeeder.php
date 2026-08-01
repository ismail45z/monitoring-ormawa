<?php

namespace Database\Seeders;

use App\Models\Kegiatan;
use App\Models\Kehadiran;
use App\Models\Mahasiswa;
use App\Models\Pengguna;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Ormawas
        $this->call(OrmawaSeeder::class);

        // 2. Seed Users

        // Admin Account
        Pengguna::create([
            'nama' => 'Administrator KIP-K',
            'email' => 'admin@mail.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'ormawa_id' => null,
        ]);

        // Pengurus Account (e.g., President of MPM)
        Pengguna::create([
            'nama' => 'Andi Setiawan (Ketua MPM)',
            'email' => 'pengurus@mail.com',
            'password' => Hash::make('password'),
            'role' => 'pengurus_ormawa',
            'ormawa_id' => 1, // MPM
        ]);

        // Create Pengurus accounts for all remaining Ormawas
        $allOrmawas = \App\Models\Ormawa::all();
        foreach ($allOrmawas as $ormawa) {
            // Skip the first one which is already created above as pengurus@mail.com
            if ($ormawa->id === 1) {
                continue;
            }

            $slug = strtolower(trim($ormawa->nama_ormawa));
            $slug = preg_replace('/\(.*?\)/', '', $slug);
            $slug = str_replace(
                ['ukm', 'hmj', 'majelis permusyawaratan mahasiswa', 'komisi pemilihan raya mahasiswa', 'keluarga mahasiswa katolik', 'badan koordinasi mahasiswa kristen'], 
                ['', '', 'mpm', 'kprm', 'kmk', 'bkmk'], 
                $slug
            );
            $slug = trim($slug);
            $slug = str_replace([' & ', ' dan ', ' unit polnep', ' unit', ' '], ['', '', '', '', '.'], $slug);
            $slug = preg_replace('/\.+/', '.', $slug);
            $slug = preg_replace('/[^a-z0-9\.]/', '', $slug);
            $slug = trim($slug, '.');
            if (empty($slug)) {
                $slug = 'ormawa' . $ormawa->id;
            }

            $email = 'pengurus.' . $slug . '@mail.com';

            Pengguna::create([
                'nama' => 'Pengurus ' . $ormawa->nama_ormawa,
                'email' => $email,
                'password' => Hash::make('password'),
                'role' => 'pengurus_ormawa',
                'ormawa_id' => $ormawa->id,
            ]);
        }

        // Mahasiswa Account (KIP-K Student)
        $mhsUser = Pengguna::create([
            'nama' => 'Rina Wijaya',
            'email' => 'mahasiswa@mail.com',
            'password' => Hash::make('password'),
            'role' => 'mahasiswa_kip',
            'ormawa_id' => null,
        ]);

        // Detail Mahasiswa record (Registered in IKMADIKSI - ormawa: id 2)
        $mahasiswa = Mahasiswa::create([
            'pengguna_id' => $mhsUser->id,
            'nim'         => '220102001',
            'no_kip'      => 'KIP20240982',
            'jurusan'     => 'Teknologi Informasi',
            'prodi'       => 'D3 Teknik Informatika',
            'angkatan'    => 2024,
            'status_kip'  => 'Aktif',
        ]);
        $mahasiswa->ormawas()->attach(2); // IKMADIKSI

        // Another Mahasiswa KIP-K student for testing watchlist (Inactive student, ormawa: IKMADIKSI)
        $mhsUser2 = Pengguna::create([
            'nama' => 'Budi Santoso',
            'email' => 'budi@mail.com',
            'password' => Hash::make('password'),
            'role' => 'mahasiswa_kip',
            'ormawa_id' => null,
        ]);

        $mahasiswa2 = Mahasiswa::create([
            'pengguna_id' => $mhsUser2->id,
            'nim'         => '220102002',
            'no_kip'      => 'KIP20240983',
            'jurusan'     => 'Teknologi Informasi',
            'prodi'       => 'D3 Teknik Informatika',
            'angkatan'    => 2024,
            'status_kip'  => 'Aktif',
        ]);
        $mahasiswa2->ormawas()->attach(2); // IKMADIKSI

        // Wadir Account (Vice Director)
        Pengguna::create([
            'nama' => 'Dr. H. Mulyono, M.T. (Wadir Kemahasiswaan)',
            'email' => 'wadir@mail.com',
            'password' => Hash::make('password'),
            'role' => 'wadir',
            'ormawa_id' => null,
        ]);

        // 3. Seed Sample Activities (Kegiatan)
        // Activities under IKMADIKSI (ormawa_id: 2)
        $kegiatan1 = Kegiatan::create([
            'ormawa_id' => 2, // IKMADIKSI
            'nama_kegiatan' => 'Seminar Nasional Cybersecurity',
            'deskripsi' => 'Seminar nasional membahas keamanan siber di era digital.',
            'tanggal' => '2026-06-10',
            'waktu_mulai' => '09:00:00',
            'waktu_selesai' => '12:00:00',
            'tempat' => 'Aula Utama Kampus',
        ]);

        $kegiatan2 = Kegiatan::create([
            'ormawa_id' => 2, // IKMADIKSI
            'nama_kegiatan' => 'IKMADIKSI Share & Care (Bakti Sosial)',
            'deskripsi' => 'Kegiatan pengabdian masyarakat dari himpunan IKMADIKSI.',
            'tanggal' => '2026-06-12',
            'waktu_mulai' => '08:00:00',
            'waktu_selesai' => '14:00:00',
            'tempat' => 'Panti Asuhan Kasih Ibu',
        ]);

        $kegiatan3 = Kegiatan::create([
            'ormawa_id' => 2, // IKMADIKSI
            'nama_kegiatan' => 'Workshop Laravel 12 & Live Coding',
            'deskripsi' => 'Pelatihan pembuatan web app berbasis Laravel 12.',
            'tanggal' => '2026-06-20',
            'waktu_mulai' => '13:00:00',
            'waktu_selesai' => '17:00:00',
            'tempat' => 'Lab Komputer Terpadu',
        ]);

        // Activities under MPM (ormawa_id: 1)
        Kegiatan::create([
            'ormawa_id' => 1, // MPM
            'nama_kegiatan' => 'Latihan Kepemimpinan Mahasiswa (LKM)',
            'deskripsi' => 'Pelatihan kepemimpinan dan manajemen organisasi tingkat dasar.',
            'tanggal' => '2026-06-15',
            'waktu_mulai' => '08:00:00',
            'waktu_selesai' => '16:00:00',
            'tempat' => 'Auditorium Lantai 2',
        ]);

        // 4. Seed Attendance (Kehadiran)
        // Lookup keanggotaan records (created via attach above)
        $keanggotaanRina = \App\Models\Keanggotaan::where('mahasiswa_id', $mahasiswa->id)
            ->where('ormawa_id', 2)->first();
        $keanggotaanBudi = \App\Models\Keanggotaan::where('mahasiswa_id', $mahasiswa2->id)
            ->where('ormawa_id', 2)->first();

        // Rina (mahasiswa1) attendances: 2 Disetujui, 1 Pending (Total 3 activities)
        Kehadiran::create([
            'kegiatan_id'       => $kegiatan1->id,
            'keanggotaan_id'    => $keanggotaanRina->id,
            'status_kehadiran'  => 'Hadir',
            'status_verifikasi' => 'Disetujui',
            'keterangan'        => 'Hadir tepat waktu dan mengikuti seluruh acara.',
        ]);

        Kehadiran::create([
            'kegiatan_id'       => $kegiatan2->id,
            'keanggotaan_id'    => $keanggotaanRina->id,
            'status_kehadiran'  => 'Hadir',
            'status_verifikasi' => 'Disetujui',
            'keterangan'        => 'Hadir tepat waktu.',
        ]);

        Kehadiran::create([
            'kegiatan_id'       => $kegiatan3->id,
            'keanggotaan_id'    => $keanggotaanRina->id,
            'status_kehadiran'  => 'Hadir',
            'status_verifikasi' => 'Pending',
            'keterangan'        => 'Mohon disetujui, sudah mengisi form kehadiran.',
        ]);

        // Budi (mahasiswa2) attendances: 1 Disetujui (Izin), 1 Ditolak, 1 Pending -> TIDAK AKTIF
        Kehadiran::create([
            'kegiatan_id'       => $kegiatan1->id,
            'keanggotaan_id'    => $keanggotaanBudi->id,
            'status_kehadiran'  => 'Izin',
            'status_verifikasi' => 'Disetujui',
            'keterangan'        => 'Sakit demam tinggi.',
        ]);

        Kehadiran::create([
            'kegiatan_id'       => $kegiatan2->id,
            'keanggotaan_id'    => $keanggotaanBudi->id,
            'status_kehadiran'  => 'Tidak Hadir',
            'status_verifikasi' => 'Ditolak',
            'keterangan'        => 'Tanpa keterangan.',
        ]);
    }
}
