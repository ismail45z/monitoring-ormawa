<?php

namespace App\Services;

use App\Models\Kegiatan;
use App\Models\Kehadiran;

class KeaktifanService
{
    /**
     * Hitung rekap keaktifan seorang mahasiswa pada suatu ormawa.
     * Hanya memasukkan kegiatan yang tanggalnya <= hari ini.
     * 
     * @param \App\Models\Mahasiswa $mahasiswa
     * @param \App\Models\Ormawa $ormawa
     * @return array
     */
    public function hitungRekap($mahasiswa, $ormawa, $startDate = null, $endDate = null)
    {
        // Hanya ambil kegiatan yang sudah lewat atau terjadi hari ini (kecuali endDate ditentukan dan kurang dari hari ini)
        $effectiveEndDate = $endDate ? min($endDate, now()->toDateString()) : now()->toDateString();
        
        $kegiatanQuery = Kegiatan::where('ormawa_id', $ormawa->id)
            ->where('tanggal', '<=', $effectiveEndDate);
            
        if ($startDate) {
            $kegiatanQuery->where('tanggal', '>=', $startDate);
        }

        $totalKegiatan = (clone $kegiatanQuery)->count();
        $totalPoinMaksimal = (clone $kegiatanQuery)->sum('poin');

        $kehadiranDisetujui = Kehadiran::whereHas('keanggotaan', fn($q) => $q->where('mahasiswa_id', $mahasiswa->id))
            ->whereHas('kegiatan', function($q) use ($ormawa, $startDate, $effectiveEndDate) {
                $q->where('ormawa_id', $ormawa->id)->where('tanggal', '<=', $effectiveEndDate);
                if ($startDate) {
                    $q->where('tanggal', '>=', $startDate);
                }
            })
            ->whereIn('status_kehadiran', ['Hadir', 'Izin', 'Sakit'])
            ->where('status_verifikasi', 'Disetujui')
            ->with('kegiatan')
            ->get();

        $totalPoin = $kehadiranDisetujui->sum(function ($kh) {
            if ($kh->status_kehadiran === 'Hadir') {
                return $kh->kegiatan->poin ?? 0;
            } elseif (in_array($kh->status_kehadiran, ['Izin', 'Sakit'])) {
                return ($kh->kegiatan->poin ?? 0) / 2;
            }
            return 0;
        });

        $persentase = $totalPoinMaksimal > 0 ? round(($totalPoin / $totalPoinMaksimal) * 100) : 0;
        
        if ($persentase >= 80) {
            $statusKeaktifan = 'Sangat Aktif';
        } elseif ($persentase >= 60) {
            $statusKeaktifan = 'Aktif';
        } elseif ($persentase >= 40) {
            $statusKeaktifan = 'Cukup';
        } else {
            $statusKeaktifan = 'Tidak Aktif';
        }

        return [
            'ormawa'          => $ormawa,
            'totalKegiatan'   => $totalKegiatan,
            'totalPoinMaks'   => $totalPoinMaksimal,
            'totalPoin'       => $totalPoin,
            'persentase'      => $persentase,
            'statusKeaktifan' => $statusKeaktifan,
        ];
    }
}
