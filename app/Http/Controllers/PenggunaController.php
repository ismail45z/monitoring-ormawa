<?php

namespace App\Http\Controllers;

use App\Models\Ormawa;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class PenggunaController extends Controller
{
    public function index(Request $request)
    {
        $query = Pengguna::with(['ormawa', 'mahasiswa.ormawas']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nama', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        }
        
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $pendingCount = Pengguna::where('status_akun', 'pending')->count();
        $ormawas = Ormawa::all(); // Fetched for the Add User modal

        $users = $query->paginate(15)->withQueryString();
        
        return view('admin.pengguna.index', compact('users', 'pendingCount', 'ormawas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:pengguna',
            'password' => 'required|string|min:8',
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

    public function update(Request $request, Pengguna $pengguna)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('pengguna')->ignore($pengguna->id)],
            'password' => 'nullable|string|min:8',
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

    public function approve(Pengguna $pengguna)
    {
        $pengguna->update(['status_akun' => 'aktif']);
        return redirect()->route('admin.pengguna.index')->with('success', 'Akun ' . $pengguna->nama . ' berhasil diaktifkan.');
    }

    public function reject(Pengguna $pengguna)
    {
        $ormawa = $pengguna->ormawa;
        $pengguna->delete();
        return redirect()->route('admin.pengguna.index')->with('success', 'Pendaftaran pengurus' . ($ormawa ? ' ' . $ormawa->nama_ormawa : '') . ' telah ditolak dan akun dihapus.');
    }
}
