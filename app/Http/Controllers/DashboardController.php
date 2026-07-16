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
            'admin'           => redirect()->route('admin.dashboard'),
            'pengurus_ormawa' => redirect()->route('pengurus.dashboard'),
            'mahasiswa_kip'   => redirect()->route('mahasiswa.dashboard'),
            'wadir'           => redirect()->route('wadir.dashboard'),
            default           => redirect()->route('login'),
        };
    }

    /**
     * Admin Dashboard.
     */
    public function adminDashboard()
    {
        $stats = [
            'total_pengguna'  => Pengguna::count(),
            'total_mahasiswa' => Mahasiswa::count(),
            'total_ormawa'    => Ormawa::count(),
            'total_kegiatan'  => Kegiatan::count(),
        ];

        $recentUsers       = Pengguna::with(['ormawa', 'mahasiswa.ormawas'])->orderBy('created_at', 'desc')->take(5)->get();
        $recentActivities  = Kegiatan::with('ormawa')->orderBy('created_at', 'desc')->take(5)->get();

        return view('dashboard.admin', compact('stats', 'recentUsers', 'recentActivities'));
    }

    /**
     * Pengurus Ormawa Dashboard.
     */
    public function pengurusDashboard()
    {
        $user   = Auth::user();
        $ormawa = $user->ormawa;

        if (!$ormawa) {
            return view('dashboard.pengurus_ormawa', [
                'ormawa'           => null,
                'totalKegiatan'    => 0,
                'totalAnggota'     => 0,
                'recentActivities' => collect(),
                'pendingCount'     => 0,
                'upcomingEvents'   => collect(),
                'trendLabels'      => [],
                'trendHadir'       => [],
                'trendIzin'        => [],
                'calendarEvents'   => [],
            ]);
        }

        $totalKegiatan    = Kegiatan::where('ormawa_id', $ormawa->id)->count();
        $totalAnggota     = $ormawa->mahasiswas()->count();
        $recentActivities = Kegiatan::where('ormawa_id', $ormawa->id)->orderBy('created_at', 'desc')->take(5)->get();

        // Pending verification count
        $pendingCount = \App\Models\Kehadiran::whereHas('kegiatan', fn($q) => $q->where('ormawa_id', $ormawa->id))
            ->where('status_verifikasi', 'Pending')
            ->count();

        // Upcoming events (next 7 days)
        $upcomingEvents = Kegiatan::where('ormawa_id', $ormawa->id)
            ->whereBetween('tanggal', [now()->toDateString(), now()->addDays(7)->toDateString()])
            ->orderBy('tanggal', 'asc')
            ->get();

        // Monthly attendance trend (last 6 months) for Chart.js
        $trendLabels = [];
        $trendHadir  = [];
        $trendIzin   = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $trendLabels[] = $month->translatedFormat('M Y');
            $trendHadir[]  = \App\Models\Kehadiran::whereHas('kegiatan', fn($q) => $q->where('ormawa_id', $ormawa->id))
                ->where('status_kehadiran', 'Hadir')
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
            $trendIzin[]   = \App\Models\Kehadiran::whereHas('kegiatan', fn($q) => $q->where('ormawa_id', $ormawa->id))
                ->where('status_kehadiran', 'Izin')
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
        }

        // All events for calendar (FullCalendar format)
        $calendarEvents = Kegiatan::where('ormawa_id', $ormawa->id)->get()->map(fn($k) => [
            'id'    => $k->id,
            'title' => $k->nama_kegiatan,
            'start' => $k->tanggal,
            'end'   => $k->tanggal,
            'url'   => route('pengurus.kegiatan.show', $k->id),
            'color' => \Carbon\Carbon::parse($k->tanggal)->isPast() ? '#6c757d' : '#4f46e5',
        ])->values()->toArray();

        return view('dashboard.pengurus_ormawa', compact(
            'ormawa', 'totalKegiatan', 'totalAnggota', 'recentActivities',
            'pendingCount', 'upcomingEvents', 'trendLabels', 'trendHadir', 'trendIzin', 'calendarEvents'
        ));
    }

    /**
     * Toggle open recruitment status for the Ormawa.
     */
    public function toggleRecruitment(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'pengurus_ormawa' || !$user->ormawa) {
            abort(403, 'Unauthorized action.');
        }

        $ormawa = $user->ormawa;
        $ormawa->update([
            'is_open_recruitment' => !$ormawa->is_open_recruitment
        ]);

        $status = $ormawa->is_open_recruitment ? 'dibuka' : 'ditutup';
        return back()->with('success', "Pendaftaran anggota baru berhasil {$status}.");
    }

    /**
     * Mahasiswa KIP Dashboard.
     */
    public function mahasiswaDashboard()
    {
        $user      = Auth::user();
        $mahasiswa = $user->mahasiswa;

        if (!$mahasiswa) {
            return view('dashboard.mahasiswa_kip', [
                'mahasiswa'       => null,
                'rekapPerOrmawa'  => collect(),
                'recentPresence'  => collect(),
            ]);
        }

        $ormawas = $mahasiswa->ormawas;

        // Calculate keaktifan per ormawa for dashboard widgets
        $rekapPerOrmawa = $ormawas->map(function ($ormawa) use ($mahasiswa) {
            $totalKegiatan = Kegiatan::where('ormawa_id', $ormawa->id)->count();
            $totalHadir    = Kehadiran::where('mahasiswa_id', $mahasiswa->id)
                ->whereHas('kegiatan', fn($q) => $q->where('ormawa_id', $ormawa->id))
                ->where('status_kehadiran', 'Hadir')
                ->where('status_verifikasi', 'Disetujui')
                ->count();

            $persentase      = $totalKegiatan > 0 ? round(($totalHadir / $totalKegiatan) * 100, 1) : 0;
            $statusKeaktifan = $persentase >= 60 ? 'AKTIF' : 'TIDAK AKTIF';

            return [
                'ormawa'          => $ormawa,
                'totalKegiatan'   => $totalKegiatan,
                'totalHadir'      => $totalHadir,
                'persentase'      => $persentase,
                'statusKeaktifan' => $statusKeaktifan,
            ];
        });

        $recentPresence = Kehadiran::with('kegiatan.ormawa')
            ->where('mahasiswa_id', $mahasiswa->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $pengumuman = \App\Models\Pengumuman::with('ormawa')
            ->whereIn('ormawa_id', $ormawas->pluck('id'))
            ->where('is_aktif', true)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('dashboard.mahasiswa_kip', compact('mahasiswa', 'rekapPerOrmawa', 'recentPresence', 'pengumuman'));
    }

    /**
     * Wadir Dashboard.
     */
    public function wadirDashboard()
    {
        $totalMahasiswa = Mahasiswa::count();
        $totalOrmawa    = Ormawa::count();

        // Calculate active and inactive — a student can appear in multiple ormawa entries
        $students       = Mahasiswa::with('ormawas')->get();
        $aktifCount     = 0;
        $tidakAktifCount= 0;
        $watchlist      = [];
        $studentStatusMap = []; // track if student has been counted

        foreach ($students as $student) {
            $ormawas       = $student->ormawas;
            $studentActive = true; // assume active unless proven inactive in any ormawa

            foreach ($ormawas as $ormawa) {
                $totalKeg = Kegiatan::where('ormawa_id', $ormawa->id)->count();
                $totalPoinMaksimal = Kegiatan::where('ormawa_id', $ormawa->id)->sum('bobot_poin');
                $totalPoin = Kehadiran::where('mahasiswa_id', $student->id)
                    ->whereHas('kegiatan', fn($q) => $q->where('ormawa_id', $ormawa->id))
                    ->whereIn('status_kehadiran', ['Hadir', 'Izin'])
                    ->where('status_verifikasi', 'Disetujui')
                    ->with('kegiatan')
                    ->get()
                    ->sum(function ($kh) {
                        return $kh->status_kehadiran === 'Hadir' ? ($kh->kegiatan->bobot_poin ?? 0) : (($kh->kegiatan->bobot_poin ?? 0) / 2);
                    });

                $persen = $totalPoinMaksimal > 0 ? round(($totalPoin / $totalPoinMaksimal) * 100) : 0;

                if ($persen < 75) {
                    $studentActive = false;
                    // Add entry for this ormawa to watchlist
                    $watchlist[] = [
                        'student'        => $student,
                        'ormawa'         => $ormawa->nama_ormawa,
                        'total_poin_maks'=> $totalPoinMaksimal,
                        'total_poin'     => $totalPoin,
                        'total_kegiatan' => $totalKeg,
                        'persentase'     => $persen,
                    ];
                }
            }

            // Count active/inactive per student (not per ormawa)
            if (!isset($studentStatusMap[$student->id])) {
                $studentStatusMap[$student->id] = true;
                if ($studentActive) {
                    $aktifCount++;
                } else {
                    $tidakAktifCount++;
                }
            }
        }

        $persenAktifKeseluruhan = $totalMahasiswa > 0 ? round(($aktifCount / $totalMahasiswa) * 100, 1) : 0;

        // Data for Chart.js: average attendance rate per Ormawa
        $ormawas     = Ormawa::all();
        $chartLabels = [];
        $chartData   = [];

        foreach ($ormawas as $o) {
            $chartLabels[]       = $o->nama_ormawa;
            $studentsInOrmawa    = $o->mahasiswas;
            $sumPercentage       = 0;
            $totalStud           = $studentsInOrmawa->count();

            if ($totalStud > 0) {
                $totalPoinMaksimal = Kegiatan::where('ormawa_id', $o->id)->sum('bobot_poin');
                foreach ($studentsInOrmawa as $s) {
                    $totalPoin = Kehadiran::where('mahasiswa_id', $s->id)
                        ->whereHas('kegiatan', fn($q) => $q->where('ormawa_id', $o->id))
                        ->whereIn('status_kehadiran', ['Hadir', 'Izin'])
                        ->where('status_verifikasi', 'Disetujui')
                        ->with('kegiatan')
                        ->get()
                        ->sum(function ($kh) {
                            return $kh->status_kehadiran === 'Hadir' ? ($kh->kegiatan->bobot_poin ?? 0) : (($kh->kegiatan->bobot_poin ?? 0) / 2);
                        });
                    $sPersen        = $totalPoinMaksimal > 0 ? round(($totalPoin / $totalPoinMaksimal) * 100) : 0;
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
