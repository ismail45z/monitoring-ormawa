<?php

namespace App\Http\Controllers;

use App\Models\Ormawa;
use Illuminate\Http\Request;

class OrmawaController extends Controller
{
    public function index()
    {
        $ormawas = Ormawa::all();
        return view('admin.ormawa.index', compact('ormawas'));
    }

    public function create()
    {
        return view('admin.ormawa.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_ormawa' => 'required|string|max:255|unique:ormawa,nama_ormawa',
            'jenis' => 'required|in:BEM,HIMA,UKM',
            'periode' => 'required|string|max:255',
            'ketua' => 'required|string|max:255',
            'pembina' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
        ]);

        Ormawa::create($validated);

        return redirect()->route('admin.ormawa.index')->with('success', 'Data Ormawa berhasil ditambahkan.');
    }

    public function show(Ormawa $ormawa)
    {
        return view('admin.ormawa.show', compact('ormawa'));
    }

    public function edit(Ormawa $ormawa)
    {
        return view('admin.ormawa.edit', compact('ormawa'));
    }

    public function update(Request $request, Ormawa $ormawa)
    {
        $validated = $request->validate([
            'nama_ormawa' => 'required|string|max:255|unique:ormawa,nama_ormawa,' . $ormawa->id,
            'jenis' => 'required|in:BEM,HIMA,UKM',
            'periode' => 'required|string|max:255',
            'ketua' => 'required|string|max:255',
            'pembina' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
        ]);

        $ormawa->update($validated);

        return redirect()->route('admin.ormawa.index')->with('success', 'Data Ormawa berhasil diperbarui.');
    }

    public function destroy(Ormawa $ormawa)
    {
        $ormawa->delete();
        return redirect()->route('admin.ormawa.index')->with('success', 'Data Ormawa berhasil dihapus.');
    }
}
