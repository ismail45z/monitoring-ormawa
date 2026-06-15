<?php

namespace App\Http\Controllers;

use App\Models\Ormawa;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class PenggunaController extends Controller
{
    public function index()
    {
        $users = Pengguna::with('ormawa')->get();
        return view('admin.pengguna.index', compact('users'));
    }

    public function create()
    {
        $ormawas = Ormawa::all();
        return view('admin.pengguna.create', compact('ormawas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:pengguna',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,pengurus_ormawa,mahasiswa_kip,wadir',
            'ormawa_id' => 'required_if:role,pengurus_ormawa|nullable|exists:ormawa,id',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        if ($validated['role'] !== 'pengurus_ormawa') {
            $validated['ormawa_id'] = null;
        }

        Pengguna::create($validated);

        return redirect()->route('admin.pengguna.index')->with('success', 'Akun pengguna berhasil ditambahkan.');
    }

    public function show(Pengguna $pengguna)
    {
        return view('admin.pengguna.show', compact('pengguna'));
    }

    public function edit(Pengguna $pengguna)
    {
        $ormawas = Ormawa::all();
        return view('admin.pengguna.edit', compact('pengguna', 'ormawas'));
    }

    public function update(Request $request, Pengguna $pengguna)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('pengguna')->ignore($pengguna->id)],
            'password' => 'nullable|string|min:6',
            'role' => 'required|in:admin,pengurus_ormawa,mahasiswa_kip,wadir',
            'ormawa_id' => 'required_if:role,pengurus_ormawa|nullable|exists:ormawa,id',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        if ($validated['role'] !== 'pengurus_ormawa') {
            $validated['ormawa_id'] = null;
        }

        $pengguna->update($validated);

        return redirect()->route('admin.pengguna.index')->with('success', 'Akun pengguna berhasil diperbarui.');
    }

    public function destroy(Pengguna $pengguna)
    {
        $pengguna->delete();
        return redirect()->route('admin.pengguna.index')->with('success', 'Akun pengguna berhasil dihapus.');
    }
}
