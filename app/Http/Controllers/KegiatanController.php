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

    public function index()
    {
        $ormawaId = $this->getOrmawaId();
        $kegiatans = Kegiatan::where('ormawa_id', $ormawaId)->get();
        return view('pengurus.kegiatan.index', compact('kegiatans'));
    }

    public function create()
    {
        return view('pengurus.kegiatan.create');
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
        ]);

        $validated['ormawa_id'] = $ormawaId;

        Kegiatan::create($validated);

        return redirect()->route('pengurus.kegiatan.index')->with('success', 'Kegiatan berhasil dibuat.');
    }

    public function edit(Kegiatan $kegiatan)
    {
        $ormawaId = $this->getOrmawaId();
        if ($kegiatan->ormawa_id !== $ormawaId) {
            abort(403, 'Unauthorized action.');
        }

        return view('pengurus.kegiatan.edit', compact('kegiatan'));
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
}
