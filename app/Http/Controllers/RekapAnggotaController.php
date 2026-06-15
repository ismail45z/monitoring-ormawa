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
        $students = Mahasiswa::with('pengguna')->where('ormawa_id', $ormawaId)->get();

        $totalKegiatan = Kegiatan::where('ormawa_id', $ormawaId)->count();
        $rekapData = [];

        foreach ($students as $student) {
            $totalHadir = Kehadiran::where('mahasiswa_id', $student->id)
                ->whereHas('kegiatan', fn($q) => $q->where('ormawa_id', $ormawaId))
                ->where('status_kehadiran', 'Hadir')
                ->where('status_verifikasi', 'Disetujui')
                ->count();

            $persentase = $totalKegiatan > 0 ? round(($totalHadir / $totalKegiatan) * 100, 1) : 0;
            $statusKeaktifan = $persentase >= 60 ? 'AKTIF' : 'TIDAK AKTIF';

            $rekapData[] = [
                'nama' => $student->pengguna->nama,
                'nim' => $student->nim,
                'prodi' => $student->prodi,
                'total_hadir' => $totalHadir,
                'total_kegiatan' => $totalKegiatan,
                'persentase' => $persentase,
                'status' => $statusKeaktifan,
            ];
        }

        return view('pengurus.rekap.index', compact('rekapData'));
    }
}
