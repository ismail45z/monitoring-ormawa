<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Show login form.
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectDashboard(Auth::user()->role);
        }
        return view('auth.login');
    }

    /**
     * Show register form.
     */
    public function showRegister()
    {
        if (Auth::check()) {
            return $this->redirectDashboard(Auth::user()->role);
        }
        $jurusans = \App\Models\Jurusan::all();
        return view('auth.register', compact('jurusans'));
    }

    /**
     * Show forgot password form.
     */
    public function showForgotPassword()
    {
        if (Auth::check()) {
            return $this->redirectDashboard(Auth::user()->role);
        }
        return view('auth.forgot-password');
    }

    /**
     * Process forgot password request.
     */
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        
        $status = \Illuminate\Support\Facades\Password::broker()->sendResetLink(
            $request->only('email')
        );

        if ($status === \Illuminate\Support\Facades\Password::RESET_LINK_SENT) {
            return back()->with('success', 'Tautan reset password telah dikirim ke email Anda.');
        }

        return back()->withErrors(['email' => __($status)]);
    }

    /**
     * Show reset password form.
     */
    public function showResetPassword(Request $request, $token)
    {
        return view('auth.reset-password', ['token' => $token, 'email' => $request->email]);
    }

    /**
     * Process reset password request.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);

        $status = \Illuminate\Support\Facades\Password::broker()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => \Illuminate\Support\Facades\Hash::make($password)
                ])->setRememberToken(\Illuminate\Support\Str::random(60));
                
                $user->save();
            }
        );

        if ($status === \Illuminate\Support\Facades\Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('success', 'Password Anda berhasil direset. Silakan login.');
        }

        return back()->withErrors(['email' => __($status)]);
    }

    /**
     * Process register request.
     */
    public function register(Request $request)
    {
        $rules = [
            'name'      => 'required|string|max:255',
            'email'     => 'required|string|email|max:255|unique:pengguna',
            'password'  => 'required|string|min:6|confirmed',
            'nim'       => 'required|string|max:10|unique:mahasiswa,nim',
            'nomor_kip' => 'required|string|max:255',
            'jurusan'   => 'required|string|max:255',
            'prodi'     => 'required|string|max:255',
            'bukti_kip' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ];

        $messages = [
            'name.required'      => 'Nama lengkap wajib diisi.',
            'email.required'     => 'Email wajib diisi.',
            'email.email'        => 'Format email tidak valid.',
            'email.unique'       => 'Email ini sudah terdaftar. Gunakan email lain atau login.',
            'password.required'  => 'Kata sandi wajib diisi.',
            'password.min'       => 'Kata sandi minimal harus 6 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'nim.required'       => 'NIM wajib diisi.',
            'nim.max'            => 'NIM tidak boleh lebih dari 10 karakter.',
            'nim.unique'         => 'NIM ini sudah terdaftar di sistem. Silakan gunakan NIM lain.',
            'nomor_kip.required' => 'Nomor KIP-K wajib diisi.',
            'bukti_kip.required' => 'Bukti KIP wajib diunggah.',
            'bukti_kip.image'    => 'Bukti harus berupa file gambar.',
            'bukti_kip.mimes'    => 'Format gambar harus jpeg, png, atau jpg.',
            'bukti_kip.max'      => 'Ukuran maksimal gambar adalah 2MB.',
        ];

        $request->validate($rules, $messages);

        $penggunaData = [
            'nama' => $request->name,
            'email' => $request->email,
            'password' => $request->password, // Model auto-hash via 'hashed' cast
            'role' => 'mahasiswa_kip',
            'status_akun' => 'pending',
        ];

        $pengguna = \App\Models\Pengguna::create($penggunaData);

        $buktiPath = null;
        if ($request->hasFile('bukti_kip')) {
            $buktiPath = $request->file('bukti_kip')->store('bukti_kip', 'public');
        }

        \App\Models\Mahasiswa::create([
            'pengguna_id' => $pengguna->id,
            'nim' => $request->nim,
            'no_kip' => $request->nomor_kip ?? '-',
            'jurusan' => $request->jurusan,
            'prodi' => $request->prodi,
            'angkatan' => date('Y'),
            'status_kip' => 'Aktif',
            'bukti_kip' => $buktiPath
        ]);

        return redirect()->route('login')->with('success', 'Pendaftaran berhasil! Akun Anda sedang menunggu persetujuan Admin. Anda akan mendapat konfirmasi setelah akun diaktifkan.');
    }

    /**
     * Process login request.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            // Block login if account is still pending
            if ($user->status_akun === 'pending') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Akun Anda masih menunggu persetujuan Admin. Silakan hubungi administrator.',
                ])->onlyInput('email');
            }

            return $this->redirectDashboard($user->role)->with('success', 'Selamat datang kembali, ' . $user->nama . '!');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    /**
     * Process logout request.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Anda telah berhasil logout.');
    }

    /**
     * Helper to redirect based on role.
     */
    private function redirectDashboard($role)
    {
        return match ($role) {
            'admin' => redirect()->route('admin.dashboard'),
            'pengurus_ormawa' => redirect()->route('pengurus.dashboard'),
            'mahasiswa_kip' => redirect()->route('mahasiswa.dashboard'),
            'wadir' => redirect()->route('wadir.dashboard'),
            default => redirect()->route('login'),
        };
    }
}
