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
        $ormawas = $mahasiswa->ormawas;

        if ($ormawas->isEmpty()) {
            $kegiatans = collect();
            $msg = 'Anda belum terdaftar di Ormawa manapun. Silakan minta Admin untuk menghubungkan Anda dengan Ormawa.';
            return view('mahasiswa.kegiatan.index', compact('kegiatans'))->with('info', $msg);
        }

        $ormawaIds = $ormawas->pluck('id')->toArray();

        // List activities from all joined ormawas
        $kegiatans = Kegiatan::whereIn('ormawa_id', $ormawaIds)
            ->with('ormawa')
            ->orderBy('tanggal', 'desc')
            ->get();

        // Get activities they already logged attendance for
        $alreadyLogged = Kehadiran::where('mahasiswa_id', $mahasiswa->id)
            ->pluck('kegiatan_id')
            ->toArray();

        return view('mahasiswa.kegiatan.index', compact('kegiatans', 'alreadyLogged'));
    }

    public function showCatatForm(Kegiatan $kegiatan)
    {
        $mahasiswa = $this->getMahasiswa();
        $ormawaIds = $mahasiswa->ormawas->pluck('id')->toArray();

        if (!in_array($kegiatan->ormawa_id, $ormawaIds)) {
            abort(403, 'Kegiatan ini diselenggarakan oleh Ormawa yang tidak Anda ikuti.');
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
        $ormawaIds = $mahasiswa->ormawas->pluck('id')->toArray();

        if (!in_array($kegiatan->ormawa_id, $ormawaIds)) {
            abort(403, 'Kegiatan ini diselenggarakan oleh Ormawa yang tidak Anda ikuti.');
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
            'bukti_foto'       => 'required_if:status_kehadiran,Hadir,Izin|image|mimes:jpeg,png,jpg,gif|max:2048',
            'keterangan'       => 'nullable|string',
        ]);

        $buktiFotoPath = null;
        if ($request->hasFile('bukti_foto')) {
            $buktiFotoPath = $request->file('bukti_foto')->store('bukti_kehadiran', 'public');
        }

        Kehadiran::create([
            'kegiatan_id'     => $kegiatan->id,
            'mahasiswa_id'    => $mahasiswa->id,
            'status_kehadiran'=> $validated['status_kehadiran'],
            'status_verifikasi' => 'Pending',
            'keterangan'      => $validated['keterangan'],
            'bukti_foto'      => $buktiFotoPath,
        ]);

        return redirect()->route('mahasiswa.riwayat')->with('success', 'Kehadiran berhasil dicatat dan sedang menunggu verifikasi Pengurus.');
    }

    public function riwayatIndex()
    {
        $mahasiswa = $this->getMahasiswa();
        $kehadirans = Kehadiran::with('kegiatan.ormawa')
            ->where('mahasiswa_id', $mahasiswa->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('mahasiswa.riwayat.index', compact('kehadirans'));
    }

    public function rekapDiri()
    {
        $mahasiswa = $this->getMahasiswa();
        $ormawas = $mahasiswa->ormawas;

        if ($ormawas->isEmpty()) {
            return view('mahasiswa.rekap.index', [
                'rekapPerOrmawa' => collect(),
            ]);
        }

        // Calculate rekap per ormawa separately
        $rekapPerOrmawa = $ormawas->map(function ($ormawa) use ($mahasiswa) {
            $kegiatanQuery = Kegiatan::where('ormawa_id', $ormawa->id);
            $totalKegiatan = (clone $kegiatanQuery)->count();
            $totalPoinMaksimal = (clone $kegiatanQuery)->sum('bobot_poin');
            $kehadiranDisetujui = Kehadiran::where('mahasiswa_id', $mahasiswa->id)
                ->whereHas('kegiatan', fn($q) => $q->where('ormawa_id', $ormawa->id))
                ->whereIn('status_kehadiran', ['Hadir', 'Izin'])
                ->where('status_verifikasi', 'Disetujui')
                ->with('kegiatan')
                ->get();

            $totalPoin = $kehadiranDisetujui->sum(function ($kh) {
                if ($kh->status_kehadiran === 'Hadir') {
                    return $kh->kegiatan->bobot_poin ?? 0;
                } elseif ($kh->status_kehadiran === 'Izin') {
                    return ($kh->kegiatan->bobot_poin ?? 0) / 2;
                }
                return 0;
            });

            $persentase = $totalPoinMaksimal > 0 ? round(($totalPoin / $totalPoinMaksimal) * 100) : 0;
            $statusKeaktifan = $persentase >= 75 ? 'AKTIF' : 'TIDAK AKTIF';

            return [
                'ormawa'          => $ormawa,
                'totalKegiatan'   => $totalKegiatan,
                'totalPoinMaks'   => $totalPoinMaksimal,
                'totalPoin'       => $totalPoin,
                'persentase'      => $persentase,
                'statusKeaktifan' => $statusKeaktifan,
            ];
        });

        return view('mahasiswa.rekap.index', compact('rekapPerOrmawa'));
    }

    public function timeline(Request $request)
    {
        $mahasiswa = $this->getMahasiswa();
        $ormawas = $mahasiswa->ormawas;

        if ($ormawas->isEmpty()) {
            return redirect()->route('mahasiswa.dashboard')->with('error', 'Anda belum terdaftar di Ormawa manapun.');
        }

        $ormawaIds = $ormawas->pluck('id')->toArray();

        // Filter by selected ormawa if specified, otherwise show all
        $selectedOrmawaId = $request->input('ormawa_id', null);

        // Ambil semua periode unik
        $kegiatanQuery = Kegiatan::whereIn('ormawa_id', $ormawaIds);
        if ($selectedOrmawaId) {
            $kegiatanQuery->where('ormawa_id', $selectedOrmawaId);
        }

        $periodes = (clone $kegiatanQuery)
            ->select('periode')
            ->distinct()
            ->orderBy('periode', 'desc')
            ->pluck('periode');

        $selectedPeriode = $request->input('periode', $periodes->first());

        $kegiatansQuery = (clone $kegiatanQuery)->with('ormawa')->orderBy('tanggal', 'asc')->orderBy('waktu_mulai', 'asc');

        if ($selectedPeriode) {
            $kegiatansQuery->where('periode', $selectedPeriode);
        }

        $kegiatans = $kegiatansQuery->get();

        return view('mahasiswa.timeline.index', compact('kegiatans', 'periodes', 'selectedPeriode', 'ormawas', 'selectedOrmawaId'));
    }

    public function pendaftaranIndex()
    {
        $mahasiswa = $this->getMahasiswa();
        $ormawas = \App\Models\Ormawa::all();
        
        // Get all pending requests for this student
        $pendingRequests = \App\Models\AnggotaRequest::where('mahasiswa_id', $mahasiswa->id)
            ->where('status', 'pending')
            ->get();

        return view('mahasiswa.pendaftaran.index', compact('ormawas', 'mahasiswa', 'pendingRequests'));
    }

    public function daftarOrmawa(Request $request)
    {
        $request->validate([
            'ormawa_id' => 'required|exists:ormawa,id',
            'alasan' => 'nullable|string|max:500'
        ]);

        $mahasiswa = $this->getMahasiswa();
        $ormawaId = $request->ormawa_id;

        $ormawa = \App\Models\Ormawa::findOrFail($ormawaId);
        if (!$ormawa->is_open_recruitment) {
            return back()->with('error', 'Maaf, pendaftaran anggota baru untuk Ormawa ini sedang ditutup.');
        }

        // Cek apakah sudah tergabung
        if ($mahasiswa->ormawas->contains($ormawaId)) {
            return back()->with('error', 'Anda sudah terdaftar di Ormawa ini.');
        }

        // Cek apakah sudah ada request pending
        $existingRequest = \App\Models\AnggotaRequest::where('mahasiswa_id', $mahasiswa->id)
            ->where('ormawa_id', $ormawaId)
            ->where('status', 'pending')
            ->first();

        if ($existingRequest) {
            return back()->with('error', 'Anda sudah memiliki permintaan bergabung yang sedang menunggu persetujuan untuk Ormawa ini.');
        }

        // Buat request baru
        \App\Models\AnggotaRequest::create([
            'ormawa_id' => $ormawaId,
            'mahasiswa_id' => $mahasiswa->id,
            'tipe' => 'tambah',
            'status' => 'pending',
            'catatan' => $request->alasan,
        ]);

        return back()->with('success', 'Permintaan bergabung berhasil dikirim. Menunggu persetujuan Pengurus Ormawa.');
    }
}
