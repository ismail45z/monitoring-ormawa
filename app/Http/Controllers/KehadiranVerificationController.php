<?php

namespace App\Http\Controllers;

use App\Models\Kehadiran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KehadiranVerificationController extends Controller
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
        $kehadirans = Kehadiran::with(['kegiatan', 'mahasiswa.pengguna'])
            ->where('status_verifikasi', 'Pending')
            ->whereHas('kegiatan', fn($q) => $q->where('ormawa_id', $ormawaId))
            ->get();

        return view('pengurus.kehadiran.index', compact('kehadirans'));
    }

    public function approve(Kehadiran $kehadiran)
    {
        $ormawaId = $this->getOrmawaId();
        if ($kehadiran->kegiatan->ormawa_id !== $ormawaId) {
            abort(403, 'Unauthorized action.');
        }

        $kehadiran->update(['status_verifikasi' => 'Disetujui']);

        return redirect()->route('pengurus.kehadiran.index')
            ->with('success', 'Kehadiran mahasiswa ' . $kehadiran->mahasiswa->pengguna->nama . ' berhasil DISETUJUI.');
    }

    public function reject(Kehadiran $kehadiran)
    {
        $ormawaId = $this->getOrmawaId();
        if ($kehadiran->kegiatan->ormawa_id !== $ormawaId) {
            abort(403, 'Unauthorized action.');
        }

        $kehadiran->update(['status_verifikasi' => 'Ditolak']);

        return redirect()->route('pengurus.kehadiran.index')
            ->with('success', 'Kehadiran mahasiswa ' . $kehadiran->mahasiswa->pengguna->nama . ' berhasil DITOLAK.');
    }
}
