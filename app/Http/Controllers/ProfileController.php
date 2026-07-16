<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        if ($user->role === 'mahasiswa_kip') {
            $user->load('mahasiswa');
        }

        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        // Base validation rules for all users
        $rules = [
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', \Illuminate\Validation\Rule::unique('pengguna')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ];

        // Specific validation rules for mahasiswa_kip
        if ($user->role === 'mahasiswa_kip') {
            $rules = array_merge($rules, [
                'nim' => ['required', 'string', 'max:255', \Illuminate\Validation\Rule::unique('mahasiswa')->ignore($user->mahasiswa->id)],
                'no_kip' => ['required', 'digits:6', \Illuminate\Validation\Rule::unique('mahasiswa')->ignore($user->mahasiswa->id)],
                'jurusan' => ['required', 'string', 'max:255'],
                'prodi' => ['required', 'string', 'max:255'],
                'angkatan' => ['required', 'integer', 'min:2000', 'max:' . (date('Y') + 1)],
                'bukti_kip' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            ]);
        }

        $validated = $request->validate($rules);

        // Update basic user information
        $user->nama = $validated['nama'];
        $user->email = $validated['email'];

        if ($request->filled('password')) {
            $user->password = \Illuminate\Support\Facades\Hash::make($validated['password']);
        }

        if ($request->hasFile('foto')) {
            // Delete old photo if exists
            if ($user->foto && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->foto)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->foto);
            }
            $path = $request->file('foto')->store('profile_photos', 'public');
            $user->foto = $path;
        }

        $user->save();

        // Update mahasiswa information if applicable
        if ($user->role === 'mahasiswa_kip') {
            $mahasiswaData = [
                'nim' => $validated['nim'],
                'no_kip' => $validated['no_kip'],
                'jurusan' => $validated['jurusan'],
                'prodi' => $validated['prodi'],
                'angkatan' => $validated['angkatan'],
            ];

            if ($request->hasFile('bukti_kip')) {
                if ($user->mahasiswa->bukti_kip && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->mahasiswa->bukti_kip)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($user->mahasiswa->bukti_kip);
                }
                $mahasiswaData['bukti_kip'] = $request->file('bukti_kip')->store('bukti_kip', 'public');
            }

            $user->mahasiswa->update($mahasiswaData);
        }

        return redirect()->route('profile.edit')->with('success', 'Profil berhasil diperbarui.');
    }
}
