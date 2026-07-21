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
        $kehadirans = Kehadiran::with(['kegiatan', 'keanggotaan.mahasiswa.pengguna'])
            ->where('status_verifikasi', 'Pending')
            ->whereHas('kegiatan', fn($q) => $q->where('ormawa_id', $ormawaId))
            ->get();

        return view('pengurus.kehadiran.index', compact('kehadirans'));
    }

    public function approve(Request $request, Kehadiran $kehadiran)
    {
        $ormawaId = $this->getOrmawaId();
        if ($kehadiran->kegiatan->ormawa_id !== $ormawaId) {
            abort(403, 'Unauthorized action.');
        }

        $kehadiran->update([
            'status_verifikasi' => 'Disetujui',
            'keterangan_verifikasi' => $request->input('keterangan_verifikasi')
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Kehadiran mahasiswa ' . $kehadiran->keanggotaan->mahasiswa->pengguna->nama . ' berhasil disetujui.'
            ]);
        }

        return redirect()->route('pengurus.kehadiran.index')
            ->with('success', 'Kehadiran mahasiswa ' . $kehadiran->keanggotaan->mahasiswa->pengguna->nama . ' berhasil DISETUJUI.');
    }

    public function reject(Request $request, Kehadiran $kehadiran)
    {
        $ormawaId = $this->getOrmawaId();
        if ($kehadiran->kegiatan->ormawa_id !== $ormawaId) {
            abort(403, 'Unauthorized action.');
        }
        
        $request->validate([
            'keterangan_verifikasi' => 'required|string|max:1000'
        ], [
            'keterangan_verifikasi.required' => 'Keterangan wajib diisi saat menolak.'
        ]);

        $kehadiran->update([
            'status_verifikasi' => 'Ditolak',
            'keterangan_verifikasi' => $request->input('keterangan_verifikasi')
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Kehadiran mahasiswa ' . $kehadiran->keanggotaan->mahasiswa->pengguna->nama . ' berhasil ditolak.'
            ]);
        }

        return redirect()->route('pengurus.kehadiran.index')
            ->with('success', 'Kehadiran mahasiswa ' . $kehadiran->keanggotaan->mahasiswa->pengguna->nama . ' berhasil DITOLAK.');
    }

    public function bulkApprove(Request $request)
    {
        $ormawaId = $this->getOrmawaId();
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada absensi yang dipilih.'
            ]);
        }

        $count = Kehadiran::whereIn('id', $ids)
            ->where('status_verifikasi', 'Pending')
            ->whereHas('kegiatan', fn($q) => $q->where('ormawa_id', $ormawaId))
            ->update([
                'status_verifikasi' => 'Disetujui',
                'keterangan_verifikasi' => 'Disetujui secara massal oleh Pengurus.'
            ]);

        return response()->json([
            'success' => true,
            'message' => $count . ' kehadiran mahasiswa berhasil disetujui secara massal.'
        ]);
    }
}
