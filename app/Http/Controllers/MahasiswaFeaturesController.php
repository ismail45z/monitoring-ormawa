<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Models\Kehadiran;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MahasiswaFeaturesController extends Controller
{
    private function getMahasiswa()
    {
        $mahasiswa = Auth::user()->mahasiswa;
        if (!$mahasiswa) {
            abort(403, 'Profil mahasiswa KIP Anda belum terdaftar.');
        }
        return $mahasiswa;
    }

    public function kegiatanIndex()
    {
        $mahasiswa = $this->getMahasiswa();
        $ormawaId = $mahasiswa->ormawa_id;

        if (!$ormawaId) {
            $kegiatans = collect();
            $msg = 'Anda belum terdaftar di Ormawa manapun. Silakan minta Admin untuk menghubungkan Anda dengan Ormawa.';
            return view('mahasiswa.kegiatan.index', compact('kegiatans'))->with('info', $msg);
        }

        // List activities in their Ormawa
        $kegiatans = Kegiatan::where('ormawa_id', $ormawaId)->orderBy('tanggal', 'desc')->get();

        // Get activities they already logged attendance for
        $alreadyLogged = Kehadiran::where('mahasiswa_id', $mahasiswa->id)
            ->pluck('kegiatan_id')
            ->toArray();

        return view('mahasiswa.kegiatan.index', compact('kegiatans', 'alreadyLogged'));
    }

    public function showCatatForm(Kegiatan $kegiatan)
    {
        $mahasiswa = $this->getMahasiswa();
        if ($kegiatan->ormawa_id !== $mahasiswa->ormawa_id) {
            abort(403, 'Kegiatan ini diselenggarakan oleh Ormawa lain.');
        }

        // Check if attendance is already recorded
        $exists = Kehadiran::where('mahasiswa_id', $mahasiswa->id)
            ->where('kegiatan_id', $kegiatan->id)
            ->first();

        if ($exists) {
            return redirect()->route('mahasiswa.riwayat')->with('error', 'Anda sudah mencatat kehadiran untuk kegiatan ini.');
        }

        return view('mahasiswa.kehadiran.catat', compact('kegiatan'));
    }

    public function storeKehadiran(Request $request, Kegiatan $kegiatan)
    {
        $mahasiswa = $this->getMahasiswa();
        if ($kegiatan->ormawa_id !== $mahasiswa->ormawa_id) {
            abort(403, 'Kegiatan ini diselenggarakan oleh Ormawa lain.');
        }

        // Check again
        $exists = Kehadiran::where('mahasiswa_id', $mahasiswa->id)
            ->where('kegiatan_id', $kegiatan->id)
            ->first();

        if ($exists) {
            return redirect()->route('mahasiswa.riwayat')->with('error', 'Anda sudah mencatat kehadiran untuk kegiatan ini.');
        }

        $validated = $request->validate([
            'status_kehadiran' => 'required|in:Hadir,Tidak Hadir,Izin',
            'keterangan' => 'nullable|string',
        ]);

        Kehadiran::create([
            'kegiatan_id' => $kegiatan->id,
            'mahasiswa_id' => $mahasiswa->id,
            'status_kehadiran' => $validated['status_kehadiran'],
            'status_verifikasi' => 'Pending',
            'keterangan' => $validated['keterangan'],
        ]);

        return redirect()->route('mahasiswa.riwayat')->with('success', 'Kehadiran berhasil dicatat dan sedang menunggu verifikasi Pengurus.');
    }

    public function riwayatIndex()
    {
        $mahasiswa = $this->getMahasiswa();
        $kehadirans = Kehadiran::with('kegiatan')
            ->where('mahasiswa_id', $mahasiswa->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('mahasiswa.riwayat.index', compact('kehadirans'));
    }

    public function rekapDiri()
    {
        $mahasiswa = $this->getMahasiswa();
        $ormawaId = $mahasiswa->ormawa_id;

        if (!$ormawaId) {
            return view('mahasiswa.rekap.index', [
                'totalKegiatan' => 0,
                'totalHadir' => 0,
                'persentase' => 0,
                'statusKeaktifan' => 'TIDAK AKTIF',
            ]);
        }

        $totalKegiatan = Kegiatan::where('ormawa_id', $ormawaId)->count();
        $totalHadir = Kehadiran::where('mahasiswa_id', $mahasiswa->id)
            ->whereHas('kegiatan', fn($q) => $q->where('ormawa_id', $ormawaId))
            ->where('status_kehadiran', 'Hadir')
            ->where('status_verifikasi', 'Disetujui')
            ->count();

        $persentase = $totalKegiatan > 0 ? round(($totalHadir / $totalKegiatan) * 100, 1) : 0;
        $statusKeaktifan = $persentase >= 60 ? 'AKTIF' : 'TIDAK AKTIF';

        return view('mahasiswa.rekap.index', compact('totalKegiatan', 'totalHadir', 'persentase', 'statusKeaktifan'));
    }
}
