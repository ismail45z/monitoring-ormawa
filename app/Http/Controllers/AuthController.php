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
            $role = Auth::user()->role;
            return $this->redirectDashboard($role)->with('success', 'Selamat datang kembali, ' . Auth::user()->nama . '!');
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
