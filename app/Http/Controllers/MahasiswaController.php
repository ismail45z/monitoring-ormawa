<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\Ormawa;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class MahasiswaController extends Controller
{
    public function index()
    {
        $students = Mahasiswa::with(['pengguna', 'ormawa'])->get();
        return view('admin.mahasiswa.index', compact('students'));
    }

    public function create()
    {
        $ormawas = Ormawa::all();
        return view('admin.mahasiswa.create', compact('ormawas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:pengguna',
            'password' => 'required|string|min:6',
            'nim' => 'required|string|max:20|unique:mahasiswa',
            'no_kip' => 'required|string|max:50|unique:mahasiswa',
            'prodi' => 'required|string|max:255',
            'jurusan' => 'required|string|max:255',
            'angkatan' => 'required|integer',
            'status_kip' => 'required|string|max:50',
            'ormawa_id' => 'nullable|exists:ormawa,id',
        ]);

        DB::transaction(function () use ($request) {
            $user = Pengguna::create([
                'nama' => $request->nama,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'mahasiswa_kip',
                'ormawa_id' => $request->ormawa_id, // Save ormawa_id here too for consistency
            ]);

            Mahasiswa::create([
                'pengguna_id' => $user->id,
                'nim' => $request->nim,
                'no_kip' => $request->no_kip,
                'prodi' => $request->prodi,
                'jurusan' => $request->jurusan,
                'angkatan' => $request->angkatan,
                'status_kip' => $request->status_kip,
                'ormawa_id' => $request->ormawa_id,
            ]);
        });

        return redirect()->route('admin.mahasiswa.index')->with('success', 'Data Mahasiswa KIP berhasil ditambahkan.');
    }

    public function show(Mahasiswa $mahasiswa)
    {
        $mahasiswa->load(['pengguna', 'ormawa']);
        return view('admin.mahasiswa.show', compact('mahasiswa'));
    }

    public function edit(Mahasiswa $mahasiswa)
    {
        $ormawas = Ormawa::all();
        $mahasiswa->load('pengguna');
        return view('admin.mahasiswa.edit', compact('mahasiswa', 'ormawas'));
    }

    public function update(Request $request, Mahasiswa $mahasiswa)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('pengguna')->ignore($mahasiswa->pengguna_id)],
            'password' => 'nullable|string|min:6',
            'nim' => ['required', 'string', 'max:20', Rule::unique('mahasiswa')->ignore($mahasiswa->id)],
            'no_kip' => ['required', 'string', 'max:50', Rule::unique('mahasiswa')->ignore($mahasiswa->id)],
            'prodi' => 'required|string|max:255',
            'jurusan' => 'required|string|max:255',
            'angkatan' => 'required|integer',
            'status_kip' => 'required|string|max:50',
            'ormawa_id' => 'nullable|exists:ormawa,id',
        ]);

        DB::transaction(function () use ($request, $mahasiswa) {
            $userData = [
                'nama' => $request->nama,
                'email' => $request->email,
                'ormawa_id' => $request->ormawa_id,
            ];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $mahasiswa->pengguna->update($userData);

            $mahasiswa->update([
                'nim' => $request->nim,
                'no_kip' => $request->no_kip,
                'prodi' => $request->prodi,
                'jurusan' => $request->jurusan,
                'angkatan' => $request->angkatan,
                'status_kip' => $request->status_kip,
                'ormawa_id' => $request->ormawa_id,
            ]);
        });

        return redirect()->route('admin.mahasiswa.index')->with('success', 'Data Mahasiswa KIP berhasil diperbarui.');
    }

    public function destroy(Mahasiswa $mahasiswa)
    {
        // Deleting the user account will delete the student details due to onDelete cascade
        $mahasiswa->pengguna->delete();
        return redirect()->route('admin.mahasiswa.index')->with('success', 'Data Mahasiswa KIP berhasil dihapus.');
    }
}
