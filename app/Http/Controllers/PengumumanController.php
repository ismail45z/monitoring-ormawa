<?php

namespace App\Http\Controllers;

use App\Models\Pengumuman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengumumanController extends Controller
{
    private function getOrmawa()
    {
        $ormawa = Auth::user()->ormawa;
        if (!$ormawa) {
            abort(403, 'Akun Anda tidak terhubung dengan Ormawa manapun.');
        }
        return $ormawa;
    }

    public function index()
    {
        $ormawa = $this->getOrmawa();
        $pengumuman = Pengumuman::where('ormawa_id', $ormawa->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pengurus.pengumuman.index', compact('pengumuman'));
    }

    public function create()
    {
        return view('pengurus.pengumuman.create');
    }

    public function store(Request $request)
    {
        $ormawa = $this->getOrmawa();

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            'lampiran' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'is_aktif' => 'nullable|boolean'
        ]);

        if ($request->hasFile('lampiran')) {
            $validated['lampiran'] = $request->file('lampiran')->store('pengumuman', 'public');
        }

        $validated['ormawa_id'] = $ormawa->id;
        $validated['is_aktif'] = $request->has('is_aktif');

        Pengumuman::create($validated);

        return redirect()->route('pengurus.pengumuman.index')->with('success', 'Pengumuman berhasil ditambahkan.');
    }

    public function edit(Pengumuman $pengumuman)
    {
        $ormawa = $this->getOrmawa();
        if ($pengumuman->ormawa_id !== $ormawa->id) {
            abort(403);
        }

        return view('pengurus.pengumuman.edit', compact('pengumuman'));
    }

    public function update(Request $request, Pengumuman $pengumuman)
    {
        $ormawa = $this->getOrmawa();
        if ($pengumuman->ormawa_id !== $ormawa->id) {
            abort(403);
        }

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            'lampiran' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'is_aktif' => 'nullable|boolean'
        ]);

        if ($request->hasFile('lampiran')) {
            if ($pengumuman->lampiran) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($pengumuman->lampiran);
            }
            $validated['lampiran'] = $request->file('lampiran')->store('pengumuman', 'public');
        }

        $validated['is_aktif'] = $request->has('is_aktif');

        $pengumuman->update($validated);

        return redirect()->route('pengurus.pengumuman.index')->with('success', 'Pengumuman berhasil diperbarui.');
    }

    public function destroy(Pengumuman $pengumuman)
    {
        $ormawa = $this->getOrmawa();
        if ($pengumuman->ormawa_id !== $ormawa->id) {
            abort(403);
        }

        if ($pengumuman->lampiran) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($pengumuman->lampiran);
        }

        $pengumuman->delete();

        return redirect()->route('pengurus.pengumuman.index')->with('success', 'Pengumuman berhasil dihapus.');
    }
}
