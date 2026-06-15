<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Models\Kehadiran;
use App\Models\Mahasiswa;
use App\Models\Ormawa;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Redirect root request.
     */
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        return match (Auth::user()->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'pengurus_ormawa' => redirect()->route('pengurus.dashboard'),
            'mahasiswa_kip' => redirect()->route('mahasiswa.dashboard'),
            'wadir' => redirect()->route('wadir.dashboard'),
            default => redirect()->route('login'),
        };
    }

    /**
     * Admin Dashboard.
     */
    public function adminDashboard()
    {
        $stats = [
            'total_pengguna' => Pengguna::count(),
            'total_mahasiswa' => Mahasiswa::count(),
            'total_ormawa' => Ormawa::count(),
            'total_kegiatan' => Kegiatan::count(),
        ];

        $recentUsers = Pengguna::with('ormawa')->orderBy('created_at', 'desc')->take(5)->get();
        $recentActivities = Kegiatan::with('ormawa')->orderBy('created_at', 'desc')->take(5)->get();

        return view('dashboard.admin', compact('stats', 'recentUsers', 'recentActivities'));
    }

    /**
     * Pengurus Ormawa Dashboard.
     */
    public function pengurusDashboard()
    {
        $user = Auth::user();
        $ormawa = $user->ormawa;

        if (!$ormawa) {
            return view('dashboard.pengurus_ormawa', [
                'ormawa' => null,
                'totalKegiatan' => 0,
                'totalAnggota' => 0,
                'recentActivities' => collect(),
            ]);
        }

        $totalKegiatan = Kegiatan::where('ormawa_id', $ormawa->id)->count();
        $totalAnggota = Mahasiswa::where('ormawa_id', $ormawa->id)->count();
        $recentActivities = Kegiatan::where('ormawa_id', $ormawa->id)->orderBy('created_at', 'desc')->take(5)->get();

        return view('dashboard.pengurus_ormawa', compact('ormawa', 'totalKegiatan', 'totalAnggota', 'recentActivities'));
    }

    /**
     * Mahasiswa KIP Dashboard.
     */
    public function mahasiswaDashboard()
    {
        $user = Auth::user();
        $mahasiswa = $user->mahasiswa;

        if (!$mahasiswa) {
            return view('dashboard.mahasiswa_kip', [
                'mahasiswa' => null,
                'persentase' => 0,
                'statusKeaktifan' => 'TIDAK AKTIF',
                'recentPresence' => collect(),
            ]);
        }

        $ormawaId = $mahasiswa->ormawa_id;
        $totalKegiatan = 0;
        $totalHadir = 0;

        if ($ormawaId) {
            $totalKegiatan = Kegiatan::where('ormawa_id', $ormawaId)->count();
            $totalHadir = Kehadiran::where('mahasiswa_id', $mahasiswa->id)
                ->whereHas('kegiatan', fn($q) => $q->where('ormawa_id', $ormawaId))
                ->where('status_kehadiran', 'Hadir')
                ->where('status_verifikasi', 'Disetujui')
                ->count();
        }

        $persentase = $totalKegiatan > 0 ? round(($totalHadir / $totalKegiatan) * 100, 1) : 0;
        $statusKeaktifan = $persentase >= 60 ? 'AKTIF' : 'TIDAK AKTIF';

        $recentPresence = Kehadiran::with('kegiatan')
            ->where('mahasiswa_id', $mahasiswa->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('dashboard.mahasiswa_kip', compact('mahasiswa', 'persentase', 'statusKeaktifan', 'recentPresence'));
    }

    /**
     * Wadir Dashboard.
     */
    public function wadirDashboard()
    {
        $totalMahasiswa = Mahasiswa::count();
        $totalOrmawa = Ormawa::count();

        // Calculate active and inactive counts
        $students = Mahasiswa::all();
        $aktifCount = 0;
        $tidakAktifCount = 0;
        $watchlist = [];

        foreach ($students as $student) {
            $ormawaId = $student->ormawa_id;
            $totalKeg = 0;
            $totalHad = 0;

            if ($ormawaId) {
                $totalKeg = Kegiatan::where('ormawa_id', $ormawaId)->count();
                $totalHad = Kehadiran::where('mahasiswa_id', $student->id)
                    ->whereHas('kegiatan', fn($q) => $q->where('ormawa_id', $ormawaId))
                    ->where('status_kehadiran', 'Hadir')
                    ->where('status_verifikasi', 'Disetujui')
                    ->count();
            }

            $persen = $totalKeg > 0 ? round(($totalHad / $totalKeg) * 100, 1) : 0;
            $isActive = $persen >= 60;

            if ($isActive) {
                $aktifCount++;
            } else {
                $tidakAktifCount++;
                // Add to watchlist (keaktifan < 60%)
                $watchlist[] = [
                    'student' => $student,
                    'ormawa' => $student->ormawa ? $student->ormawa->nama_ormawa : 'Tidak Mengikuti',
                    'total_kegiatan' => $totalKeg,
                    'total_hadir' => $totalHad,
                    'persentase' => $persen,
                ];
            }
        }

        $persenAktifKeseluruhan = $totalMahasiswa > 0 ? round(($aktifCount / $totalMahasiswa) * 100, 1) : 0;

        // Data for Chart.js: Keaktifan per Ormawa (Average attendance rate of registered students)
        $ormawas = Ormawa::all();
        $chartLabels = [];
        $chartData = [];

        foreach ($ormawas as $o) {
            $chartLabels[] = $o->nama_ormawa;
            $studentsInOrmawa = Mahasiswa::where('ormawa_id', $o->id)->get();
            $sumPercentage = 0;
            $totalStud = $studentsInOrmawa->count();

            if ($totalStud > 0) {
                $totalKeg = Kegiatan::where('ormawa_id', $o->id)->count();
                foreach ($studentsInOrmawa as $s) {
                    $totalHad = Kehadiran::where('mahasiswa_id', $s->id)
                        ->whereHas('kegiatan', fn($q) => $q->where('ormawa_id', $o->id))
                        ->where('status_kehadiran', 'Hadir')
                        ->where('status_verifikasi', 'Disetujui')
                        ->count();
                    $sPersen = $totalKeg > 0 ? ($totalHad / $totalKeg) * 100 : 0;
                    $sumPercentage += $sPersen;
                }
                $avgPercentage = round($sumPercentage / $totalStud, 1);
            } else {
                $avgPercentage = 0;
            }

            $chartData[] = $avgPercentage;
        }

        return view('dashboard.wadir', compact(
            'totalMahasiswa',
            'totalOrmawa',
            'persenAktifKeseluruhan',
            'tidakAktifCount',
            'watchlist',
            'chartLabels',
            'chartData',
            'ormawas'
        ));
    }
}
