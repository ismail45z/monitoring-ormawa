<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KegiatanController extends Controller
{
    private function getOrmawaId()
    {
        $ormawa = Auth::user()->ormawa;
        if (!$ormawa) {
            abort(403, 'Akun Anda tidak terhubung dengan Ormawa manapun.');
        }
        return $ormawa->id;
    }

    public function create()
    {
        return redirect()->route('pengurus.kegiatan.index');
    }

    public function edit(Kegiatan $kegiatan)
    {
        return redirect()->route('pengurus.kegiatan.index');
    }

    public function index(Request $request)
    {
        $ormawaId = $this->getOrmawaId();
        
        $query = Kegiatan::where('ormawa_id', $ormawaId);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nama_kegiatan', 'like', "%{$search}%");
        }
        
        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_selesai')) {
            $query->whereBetween('tanggal', [$request->tanggal_mulai, $request->tanggal_selesai]);
        } elseif ($request->filled('tanggal_mulai')) {
            $query->where('tanggal', '>=', $request->tanggal_mulai);
        } elseif ($request->filled('tanggal_selesai')) {
            $query->where('tanggal', '<=', $request->tanggal_selesai);
        }

        $kegiatans = $query->orderBy('tanggal', 'desc')->paginate(15)->withQueryString();
        $periodes = \App\Models\Periode::all();
        
        return view('pengurus.kegiatan.index', compact('kegiatans', 'periodes'));
    }

    public function store(Request $request)
    {
        $ormawaId = $this->getOrmawaId();

        $validated = $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tanggal' => 'required|date',
            'waktu_mulai' => 'required',
            'waktu_selesai' => 'required',
            'tempat' => 'required|string|max:255',
            'jenis' => 'nullable|string|max:255',
            'periode_id' => 'required|exists:periodes,id',
            'poin' => 'required|integer|min:0',
        ]);

        $validated['ormawa_id'] = $ormawaId;

        Kegiatan::create($validated);

        return redirect()->route('pengurus.kegiatan.index')->with('success', 'Kegiatan berhasil dibuat.');
    }

    public function show(Kegiatan $kegiatan)
    {
        $ormawaId = $this->getOrmawaId();
        if ($kegiatan->ormawa_id !== $ormawaId) {
            abort(403, 'Unauthorized action.');
        }

        $kegiatan->load('kehadiran.keanggotaan.mahasiswa.pengguna');
        $totalPeserta  = $kegiatan->kehadiran->count();
        $totalHadir    = $kegiatan->kehadiran->where('status_kehadiran', 'Hadir')->where('status_verifikasi', 'Disetujui')->count();
        $totalPending  = $kegiatan->kehadiran->where('status_verifikasi', 'Pending')->count();

        return view('pengurus.kegiatan.show', compact('kegiatan', 'totalPeserta', 'totalHadir', 'totalPending'));
    }

    public function update(Request $request, Kegiatan $kegiatan)
    {
        $ormawaId = $this->getOrmawaId();
        if ($kegiatan->ormawa_id !== $ormawaId) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tanggal' => 'required|date',
            'waktu_mulai' => 'required',
            'waktu_selesai' => 'required',
            'tempat' => 'required|string|max:255',
            'jenis' => 'nullable|string|max:255',
            'periode_id' => 'required|exists:periodes,id',
            'poin' => 'required|integer|min:0',
        ]);

        $kegiatan->update($validated);

        return redirect()->route('pengurus.kegiatan.index')->with('success', 'Kegiatan berhasil diperbarui.');
    }

    public function destroy(Kegiatan $kegiatan)
    {
        $ormawaId = $this->getOrmawaId();
        if ($kegiatan->ormawa_id !== $ormawaId) {
            abort(403, 'Unauthorized action.');
        }

        $kegiatan->delete();

        return redirect()->route('pengurus.kegiatan.index')->with('success', 'Kegiatan berhasil dihapus.');
    }

    public function toggleAbsensi(Request $request, Kegiatan $kegiatan)
    {
        $ormawaId = $this->getOrmawaId();
        if ($kegiatan->ormawa_id !== $ormawaId) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'status_absensi' => 'required|in:auto,buka,tutup',
        ]);

        $kegiatan->update(['status_absensi' => $request->status_absensi]);

        return redirect()->back()->with('success', 'Status absensi berhasil diubah menjadi ' . ucfirst($request->status_absensi) . '.');
    }
}
