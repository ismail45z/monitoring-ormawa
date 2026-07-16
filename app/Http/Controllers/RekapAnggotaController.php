<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Models\Kehadiran;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RekapAnggotaController extends Controller
{
    private function getOrmawaId()
    {
        $ormawa = Auth::user()->ormawa;
        if (!$ormawa) {
            abort(403, 'Akun Anda tidak terhubung dengan Ormawa manapun.');
        }
        return $ormawa->id;
    }

    public function index()
    {
        $ormawaId = $this->getOrmawaId();
        $ormawa = Auth::user()->ormawa;
        // Get students registered to this ormawa via pivot table
        $students = $ormawa->mahasiswas()->with('pengguna')->get();

        $kegiatanQuery = Kegiatan::where('ormawa_id', $ormawaId);
        $totalKegiatan = (clone $kegiatanQuery)->count();
        $totalPoinMaksimal = (clone $kegiatanQuery)->sum('bobot_poin');
        $rekapData = [];

        foreach ($students as $student) {
            $totalPoin = Kehadiran::where('mahasiswa_id', $student->id)
                ->whereHas('kegiatan', fn($q) => $q->where('ormawa_id', $ormawaId))
                ->whereIn('status_kehadiran', ['Hadir', 'Izin'])
                ->where('status_verifikasi', 'Disetujui')
                ->with('kegiatan')
                ->get()
                ->sum(function ($kehadiran) {
                    if ($kehadiran->status_kehadiran === 'Hadir') {
                        return $kehadiran->kegiatan->bobot_poin ?? 0;
                    } elseif ($kehadiran->status_kehadiran === 'Izin') {
                        return ($kehadiran->kegiatan->bobot_poin ?? 0) / 2;
                    }
                    return 0;
                });

            $persentase = $totalPoinMaksimal > 0 ? round(($totalPoin / $totalPoinMaksimal) * 100) : 0;
            $statusKeaktifan = $persentase >= 75 ? 'AKTIF' : 'TIDAK AKTIF';

            $rekapData[] = [
                'nama' => $student->pengguna->nama,
                'nim' => $student->nim,
                'prodi' => $student->prodi,
                'total_poin_maks' => $totalPoinMaksimal,
                'total_poin' => $totalPoin,
                'total_kegiatan' => $totalKegiatan,
                'persentase' => $persentase,
                'status' => $statusKeaktifan,
            ];
        }

        return view('pengurus.rekap.index', compact('rekapData'));
    }
}
