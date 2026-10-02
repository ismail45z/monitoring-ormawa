<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Models\Kehadiran;
use App\Models\Mahasiswa;
use App\Models\Ormawa;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

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
            ->whereBetween('tanggal', [now()->toDateString(), now()->copy()->addDays(7)->toDateString()])
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

        // Calculate keaktifan per ormawa for dashboard widgets using KeaktifanService
        $keaktifanService = new \App\Services\KeaktifanService();
        $rekapPerOrmawa = $ormawas->map(function ($ormawa) use ($mahasiswa, $keaktifanService) {
            return $keaktifanService->hitungRekap($mahasiswa, $ormawa);
        });

        $recentPresence = Kehadiran::with('kegiatan.ormawa')
            ->whereHas('keanggotaan', fn($q) => $q->where('mahasiswa_id', $mahasiswa->id))
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
    public function wadirDashboard(\Illuminate\Http\Request $request)
    {
        $ormawaId = $request->ormawa_id;
        $startDate = $request->tanggal_mulai;
        $endDate = $request->tanggal_selesai;
        $statusKeaktifanFilter = $request->status_keaktifan; // null = semua

        $totalMahasiswa = Mahasiswa::count();
        $totalOrmawa    = Ormawa::count();

        // Calculate active and inactive
        $studentsQuery = Mahasiswa::with('ormawas');
        if ($ormawaId) {
            $studentsQuery->whereHas('ormawas', fn($q) => $q->where('ormawa.id', $ormawaId));
        }
        $students = $studentsQuery->get();

        $aktifCount       = 0;
        $tidakAktifCount  = 0;
        $watchlist        = [];
        $studentStatusMap = [];
        $allStudentList   = [];
        $countSangatAktif = 0;
        $countAktif       = 0;
        $countCukup       = 0;
        $countTidakAktif  = 0;

        $keaktifanService = new \App\Services\KeaktifanService();

        foreach ($students as $student) {
            $ormawasStudent = $student->ormawas;
            if ($ormawaId) {
                $ormawasStudent = $ormawasStudent->where('id', $ormawaId);
            }
            $studentActive = true;

            foreach ($ormawasStudent as $ormawa) {
                $cacheKey = "rekap_{$student->id}_{$ormawa->id}_" . ($startDate ?? 'all') . '_' . ($endDate ?? 'now');
                $rekap = Cache::remember($cacheKey, now()->addMinutes(30), function () use ($student, $ormawa, $startDate, $endDate, $keaktifanService) {
                    return $keaktifanService->hitungRekap($student, $ormawa, $startDate, $endDate);
                });

                $persen = $rekap['persentase'];

                // Determine status label
                if ($persen >= 80) {
                    $statusLabel = 'Sangat Aktif';
                    $countSangatAktif++;
                } elseif ($persen >= 60) {
                    $statusLabel = 'Aktif';
                    $countAktif++;
                } elseif ($persen >= 40) {
                    $statusLabel = 'Cukup';
                    $countCukup++;
                } else {
                    $statusLabel = 'Tidak Aktif';
                    $countTidakAktif++;
                }

                if ($persen < 60) {
                    $studentActive = false;
                    $watchlist[] = [
                        'student'        => $student,
                        'ormawa'         => $ormawa->nama_ormawa,
                        'total_poin_maks'=> $rekap['totalPoinMaks'],
                        'total_poin'     => $rekap['totalPoin'],
                        'total_kegiatan' => $rekap['totalKegiatan'],
                        'persentase'     => $persen,
                        'status'         => $statusLabel,
                    ];
                }

                $allStudentList[] = [
                    'student'        => $student,
                    'ormawa'         => $ormawa->nama_ormawa,
                    'total_poin_maks'=> $rekap['totalPoinMaks'],
                    'total_poin'     => $rekap['totalPoin'],
                    'total_kegiatan' => $rekap['totalKegiatan'],
                    'persentase'     => $persen,
                    'status'         => $statusLabel,
                ];
            }

            if (!isset($studentStatusMap[$student->id])) {
                $studentStatusMap[$student->id] = true;
                if ($studentActive) {
                    $aktifCount++;
                } else {
                    $tidakAktifCount++;
                }
            }
        }

        $baseTotalForPercentage = $ormawaId ? $students->count() : $totalMahasiswa;
        $persenAktifKeseluruhan = $baseTotalForPercentage > 0 ? round(($aktifCount / $baseTotalForPercentage) * 100, 1) : 0;

        // Data for Chart.js: average attendance rate per Ormawa
        $ormawasDataQuery = Ormawa::query();
        if ($ormawaId) {
            $ormawasDataQuery->where('id', $ormawaId);
        }
        $ormawasData = $ormawasDataQuery->get();
        
        $chartLabels = [];
        $chartData   = [];

        foreach ($ormawasData as $o) {
            $chartLabels[]       = $o->nama_ormawa;
            $studentsInOrmawa    = $o->mahasiswas;
            $sumPercentage       = 0;
            $totalStud           = $studentsInOrmawa->count();

            if ($totalStud > 0) {
                foreach ($studentsInOrmawa as $s) {
                    $cacheKey = "rekap_{$s->id}_{$o->id}_" . ($startDate ?? 'all') . '_' . ($endDate ?? 'now');
                    $rekap = Cache::remember($cacheKey, now()->addMinutes(30), function () use ($s, $o, $startDate, $endDate, $keaktifanService) {
                        return $keaktifanService->hitungRekap($s, $o, $startDate, $endDate);
                    });
                    $sumPercentage += $rekap['persentase'];
                }
                $avgPercentage = round($sumPercentage / $totalStud, 1);
            } else {
                $avgPercentage = 0;
            }

            $chartData[] = $avgPercentage;
        }

        // Filter allStudentList by status keaktifan if requested, then sort
        if ($statusKeaktifanFilter) {
            $allStudentList = array_values(array_filter($allStudentList, fn($item) => $item['status'] === $statusKeaktifanFilter));
        }
        usort($allStudentList, fn($a, $b) => $b['persentase'] <=> $a['persentase']);

        $ormawas = Ormawa::all(); // For the filter dropdown options

        return view('dashboard.wadir', compact(
            'totalMahasiswa',
            'totalOrmawa',
            'persenAktifKeseluruhan',
            'tidakAktifCount',
            'watchlist',
            'chartLabels',
            'chartData',
            'ormawas',
            'allStudentList',
            'statusKeaktifanFilter',
            'countSangatAktif',
            'countAktif',
            'countCukup',
            'countTidakAktif'
        ));
    }
}
