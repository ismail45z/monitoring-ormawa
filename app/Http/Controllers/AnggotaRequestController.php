<?php

namespace App\Http\Controllers;

use App\Models\AnggotaRequest;
use App\Models\Mahasiswa;
use App\Models\Ormawa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AnggotaRequestController extends Controller
{
    private function getOrmawa()
    {
        $ormawa = Auth::user()->ormawa;
        if (!$ormawa) {
            abort(403, 'Akun Anda tidak terhubung dengan Ormawa manapun.');
        }
        return $ormawa;
    }

    /**
     * Pengurus: list current members and pending requests from Mahasiswa.
     */
    public function index()
    {
        $ormawa = $this->getOrmawa();

        // Current members
        $anggota = $ormawa->mahasiswas()->with('pengguna')->get();

        // Pending requests from Mahasiswa
        $pendingRequests = AnggotaRequest::with('mahasiswa.pengguna')
            ->where('ormawa_id', $ormawa->id)
            ->where('status', 'pending')
            ->where('tipe', 'tambah')
            ->orderBy('created_at', 'asc')
            ->get();

        // Processed requests
        $processedRequests = AnggotaRequest::with(['mahasiswa.pengguna', 'pemroses'])
            ->where('ormawa_id', $ormawa->id)
            ->whereIn('status', ['disetujui', 'ditolak'])
            ->orderBy('processed_at', 'desc')
            ->take(30)
            ->get();

        return view('pengurus.anggota.index', compact(
            'ormawa', 'anggota', 'pendingRequests', 'processedRequests'
        ));
    }

    /**
     * Pengurus: approve a request from Mahasiswa.
     */
    public function approve(Request $request, AnggotaRequest $anggotaRequest)
    {
        $ormawa = $this->getOrmawa();

        if ($anggotaRequest->ormawa_id !== $ormawa->id) {
            abort(403, 'Unauthorized action.');
        }

        if (!$anggotaRequest->isPending()) {
            return back()->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        $request->validate(['catatan_pengurus' => 'nullable|string|max:500']);

        DB::transaction(function () use ($request, $anggotaRequest, $ormawa) {
            $anggotaRequest->update([
                'status'           => 'disetujui',
                'catatan_pengurus' => $request->catatan_pengurus,
                'pemroses_id'      => Auth::id(),
                'processed_at'     => now(),
            ]);

            $mahasiswa = $anggotaRequest->mahasiswa;

            // Add to pivot table (avoid duplicates)
            if (!$mahasiswa->ormawas()->where('ormawa_id', $ormawa->id)->exists()) {
                $activePeriode = \App\Models\Periode::where('status', 'Aktif')->orderBy('tanggal_mulai', 'desc')->first();
                $jabatan = \App\Models\Jabatan::where('nama_jabatan', 'Anggota')->first();

                $mahasiswa->ormawas()->attach($ormawa->id, [
                    'periode_id' => $activePeriode ? $activePeriode->id : 1,
                    'jabatan_id' => $jabatan ? $jabatan->id : 1,
                    'tgl_masuk'  => now(),
                    'status'     => 'Aktif',
                ]);
            }
        });

        return back()->with('success', 'Pendaftaran berhasil disetujui, mahasiswa kini menjadi anggota.');
    }

    /**
     * Pengurus: reject a request from Mahasiswa.
     */
    public function reject(Request $request, AnggotaRequest $anggotaRequest)
    {
        $ormawa = $this->getOrmawa();

        if ($anggotaRequest->ormawa_id !== $ormawa->id) {
            abort(403, 'Unauthorized action.');
        }

        if (!$anggotaRequest->isPending()) {
            return back()->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        $request->validate([
            'catatan_pengurus' => 'nullable|string|max:500',
            'is_permanen' => 'nullable|boolean',
            'cooldown_hari' => 'nullable|integer|min:0',
        ]);

        $isPermanen = $request->boolean('is_permanen');
        $cooldownHari = $isPermanen ? null : $request->input('cooldown_hari');

        $anggotaRequest->update([
            'status'           => 'ditolak',
            'catatan_pengurus' => $request->catatan_pengurus,
            'pemroses_id'      => Auth::id(),
            'processed_at'     => now(),
            'is_permanen'      => $isPermanen,
            'cooldown_hari'    => $cooldownHari,
        ]);

        return back()->with('success', 'Pendaftaran berhasil ditolak.');
    }

    /**
     * Pengurus: remove an existing member directly.
     */
    public function removeMember(Request $request, Mahasiswa $mahasiswa)
    {
        $ormawa = $this->getOrmawa();

        // Check membership
        if (!$ormawa->mahasiswas()->where('mahasiswa_id', $mahasiswa->id)->exists()) {
            return back()->with('error', 'Mahasiswa tersebut bukan anggota Ormawa ini.');
        }

        $mahasiswa->ormawas()->detach($ormawa->id);

        return back()->with('success', 'Anggota berhasil dihapus dari Ormawa.');
    }
}
