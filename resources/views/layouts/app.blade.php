<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring KIP-Kuliah - @yield('title', 'Dashboard')</title>
    <!-- Google Fonts: Outfit -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <!-- jQuery DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- Dark Mode Init Script -->
    <script>
        const storedTheme = localStorage.getItem('theme');
        const getPreferredTheme = () => {
            if (storedTheme) {
                return storedTheme;
            }
            return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        }
        document.documentElement.setAttribute('data-bs-theme', getPreferredTheme());
    </script>
    <!-- Custom Style -->
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f4f6fa;
            color: #333;
        }
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
            color: #fff;
            box-shadow: 4px 0 10px rgba(0,0,0,0.1);
        }
        .sidebar .nav-link {
            color: #cbd5e1;
            padding: 12px 20px;
            border-radius: 8px;
            margin: 4px 15px;
            font-weight: 500;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: #fff;
            background-color: rgba(255,255,255,0.1);
            transform: translateX(5px);
        }
        .navbar-custom {
            background-color: #fff;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        }
        .card-custom {
            background-color: #fff;
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }

        /* Dark Mode Overrides */
        [data-bs-theme="dark"] body {
            background-color: #0f172a;
            color: #f8fafc;
        }
        [data-bs-theme="dark"] .navbar-custom {
            background-color: #1e293b !important;
            border-bottom: 1px solid #334155;
        }
        [data-bs-theme="dark"] .card-custom {
            background-color: #1e293b !important;
            box-shadow: none;
            border: 1px solid #334155;
        }
        [data-bs-theme="dark"] .text-dark {
            color: #f8fafc !important;
        }
        [data-bs-theme="dark"] .text-dark-toggle {
            color: #f8fafc !important;
        }
        [data-bs-theme="dark"] .bg-light {
            background-color: #334155 !important;
            color: #f8fafc !important;
        }
        [data-bs-theme="dark"] .btn-light {
            background-color: #334155;
            color: #f8fafc;
            border-color: #475569;
        }
        [data-bs-theme="dark"] .btn-light:hover {
            background-color: #475569;
            color: #fff;
        }
        [data-bs-theme="dark"] .dropdown-menu {
            background-color: #1e293b;
            border: 1px solid #334155 !important;
        }
        [data-bs-theme="dark"] .dropdown-item {
            color: #cbd5e1;
        }
        [data-bs-theme="dark"] .dropdown-item:hover {
            background-color: #334155;
            color: #fff;
        }
        [data-bs-theme="dark"] .table {
            color: #cbd5e1;
            border-color: #334155;
        }
        [data-bs-theme="dark"] .list-group-item {
            background-color: #1e293b;
            border-color: #334155;
            color: #cbd5e1;
        }
            transition: transform 0.3s;
        }
        .card-custom:hover {
            transform: translateY(-5px);
        }
        .badge-custom-aktif {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #fff;
            padding: 6px 12px;
            border-radius: 30px;
        }
        .badge-custom-tidak {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: #fff;
            padding: 6px 12px;
            border-radius: 30px;
        }
        .btn-gradient-primary {
            background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
            border: none;
            color: white;
            transition: all 0.3s;
        }
        .btn-gradient-primary:hover {
            opacity: 0.9;
            transform: scale(1.02);
            color: white;
        }
        /* Image Popup Style */
        .img-popup {
            cursor: pointer;
            transition: transform 0.2s ease-in-out;
        }
        .img-popup:hover {
            transform: scale(1.02);
            opacity: 0.9;
        }
    </style>
    @yield('styles')
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0 sidebar d-none d-md-block">
                <div class="p-4 text-center border-bottom border-secondary mb-4">
                    <h5 class="fw-bold mb-0 text-white"><i class="bi bi-mortarboard-fill me-2"></i>KIP-K Monitor</h5>
                    <small class="text-muted text-uppercase tracking-wider">Aktivitas Mahasiswa</small>
                </div>
                
                <div class="px-3 mb-3 text-center">
                    <div class="py-2 px-3 bg-secondary bg-opacity-25 rounded-3 text-center">
                        @if(Auth::user()->foto)
                            <img src="{{ asset('storage/' . Auth::user()->foto) }}" alt="Profile" class="rounded-circle mb-2 border border-2 border-primary" style="width: 60px; height: 60px; object-fit: cover;">
                        @else
                            <div class="rounded-circle bg-secondary d-inline-flex align-items-center justify-content-center text-white mb-2" style="width: 60px; height: 60px; font-size: 1.5rem;">
                                <i class="bi bi-person"></i>
                            </div>
                        @endif
                        <small class="text-white d-block text-truncate fw-semibold">{{ Auth::user()->nama }}</small>
                        <span class="badge bg-primary text-uppercase mt-1" style="font-size: 0.7rem;">
                            {{ str_replace('_', ' ', Auth::user()->role) }}
                        </span>
                    </div>
                </div>

                <ul class="nav flex-column mb-auto">
                    <!-- Dynamic Navigation by Role -->
                    @if(Auth::user()->role == 'admin')
                        <li class="nav-item">
                            <a class="nav-link {{ Route::is('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                                <i class="bi bi-speedometer2 me-2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Route::is('admin.pengguna.*') ? 'active' : '' }}" href="{{ route('admin.pengguna.index') }}">
                                <i class="bi bi-people-fill me-2"></i> Kelola Pengguna
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Route::is('admin.ormawa.*') ? 'active' : '' }}" href="{{ route('admin.ormawa.index') }}">
                                <i class="bi bi-diagram-3-fill me-2"></i> Kelola Ormawa
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Route::is('admin.mahasiswa.*') ? 'active' : '' }}" href="{{ route('admin.mahasiswa.index') }}">
                                <i class="bi bi-mortarboard me-2"></i> Kelola Mahasiswa
                            </a>
                        </li>
                        <li class="nav-item mt-3 mb-1">
                            <span class="text-muted small fw-bold px-3 text-uppercase">Sistem</span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Route::is('admin.audit.*') ? 'active' : '' }}" href="{{ route('admin.audit.index') }}">
                                <i class="bi bi-shield-lock-fill me-2"></i> Audit Log
                            </a>
                        </li>
                    @elseif(Auth::user()->role == 'pengurus_ormawa')
                        <li class="nav-item">
                            <a class="nav-link {{ Route::is('pengurus.dashboard') ? 'active' : '' }}" href="{{ route('pengurus.dashboard') }}">
                                <i class="bi bi-speedometer2 me-2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Route::is('pengurus.kegiatan.*') ? 'active' : '' }}" href="{{ route('pengurus.kegiatan.index') }}">
                                <i class="bi bi-calendar-event-fill me-2"></i> Kegiatan Ormawa
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Route::is('pengurus.kehadiran.*') ? 'active' : '' }}" href="{{ route('pengurus.kehadiran.index') }}">
                                <i class="bi bi-check-circle-fill me-2"></i> Verifikasi Kehadiran
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Route::is('pengurus.rekap.*') ? 'active' : '' }}" href="{{ route('pengurus.rekap.index') }}">
                                <i class="bi bi-bar-chart-line-fill me-2"></i> Rekap Keaktifan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Route::is('pengurus.anggota.*') ? 'active' : '' }}" href="{{ route('pengurus.anggota.index') }}">
                                <i class="bi bi-people-fill me-2"></i> Anggota Ormawa
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Route::is('pengurus.pengumuman.*') ? 'active' : '' }}" href="{{ route('pengurus.pengumuman.index') }}">
                                <i class="bi bi-megaphone-fill me-2"></i> Pengumuman
                            </a>
                        </li>
                    @elseif(Auth::user()->role == 'mahasiswa_kip')
                        <li class="nav-item">
                            <a class="nav-link {{ Route::is('mahasiswa.dashboard') ? 'active' : '' }}" href="{{ route('mahasiswa.dashboard') }}">
                                <i class="bi bi-speedometer2 me-2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Route::is('mahasiswa.pendaftaran.*') ? 'active' : '' }}" href="{{ route('mahasiswa.pendaftaran.index') }}">
                                <i class="bi bi-person-plus-fill me-2"></i> Gabung Ormawa
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Route::is('mahasiswa.kegiatan.*') ? 'active' : '' }}" href="{{ route('mahasiswa.kegiatan.index') }}">
                                <i class="bi bi-calendar3 me-2"></i> Kegiatan Tersedia
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Route::is('mahasiswa.riwayat') ? 'active' : '' }}" href="{{ route('mahasiswa.riwayat') }}">
                                <i class="bi bi-clock-history me-2"></i> Riwayat Kehadiran
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Route::is('mahasiswa.rekap') ? 'active' : '' }}" href="{{ route('mahasiswa.rekap') }}">
                                <i class="bi bi-person-badge-fill me-2"></i> Rekap Keaktifan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Route::is('mahasiswa.timeline') ? 'active' : '' }}" href="{{ route('mahasiswa.timeline') }}">
                                <i class="bi bi-calendar-range-fill me-2"></i> Timeline Progja
                            </a>
                        </li>
                    @elseif(Auth::user()->role == 'wadir')
                        <li class="nav-item">
                            <a class="nav-link {{ Route::is('wadir.dashboard') ? 'active' : '' }}" href="{{ route('wadir.dashboard') }}">
                                <i class="bi bi-speedometer2 me-2"></i> Dashboard Eksekutif
                            </a>
                        </li>
                    @endif
                </ul>
                <div class="p-3 border-top border-secondary mt-auto">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger w-100 btn-sm"><i class="bi bi-box-arrow-right me-2"></i> Logout</button>
                    </form>
                </div>
            </div>

            <!-- Content Area -->
            <div class="col-md-9 col-lg-10 px-0">
                <!-- Top Navbar -->
                <nav class="navbar navbar-expand-lg navbar-custom py-3 px-4">
                    <div class="container-fluid">
                        <span class="navbar-brand d-md-none fw-bold"><i class="bi bi-mortarboard-fill me-2"></i>KIP-K</span>
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarSupportedContent">
                            <div class="ms-auto d-flex align-items-center">
                                <span class="me-3 text-muted d-none d-md-inline">Hari ini: <strong class="text-dark-toggle">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</strong></span>
                                
                                <!-- Dark Mode Toggle -->
                                <button id="darkModeToggle" class="btn btn-light rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="bi bi-moon-fill" id="themeIcon"></i>
                                </button>

                                <!-- Notification Bell -->
                                @php
                                    $notifCount = 0;
                                    $notifItems = collect();

                                    if (Auth::user()->role === 'mahasiswa_kip' && Auth::user()->mahasiswa) {
                                        $mahasiswa   = Auth::user()->mahasiswa;
                                        $ormawaIds   = $mahasiswa->ormawas->pluck('id');
                                        $notifItems  = \App\Models\Pengumuman::with('ormawa')
                                            ->whereIn('ormawa_id', $ormawaIds)
                                            ->where('is_aktif', true)
                                            ->latest()
                                            ->take(5)
                                            ->get();
                                        $notifCount = $notifItems->count();
                                    } elseif (Auth::user()->role === 'pengurus_ormawa' && Auth::user()->ormawa) {
                                        $ormawaId   = Auth::user()->ormawa->id;
                                        $notifCount = \App\Models\Kehadiran::whereHas('kegiatan', fn($q) => $q->where('ormawa_id', $ormawaId))
                                            ->where('status_verifikasi', 'Pending')
                                            ->count();
                                    }
                                @endphp

                                <div class="dropdown me-3">
                                    <button class="btn btn-light rounded-circle position-relative d-flex align-items-center justify-content-center"
                                            type="button" data-bs-toggle="dropdown" aria-expanded="false"
                                            style="width: 40px; height: 40px;">
                                        <i class="bi bi-bell-fill"></i>
                                        @if($notifCount > 0)
                                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                                  style="font-size: 0.65rem; min-width: 18px; padding: 3px 5px;">
                                                {{ $notifCount > 9 ? '9+' : $notifCount }}
                                            </span>
                                        @endif
                                    </button>

                                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" style="min-width: 300px; max-width: 340px;">
                                        <li class="dropdown-header fw-bold text-uppercase small px-3 py-2" style="letter-spacing: 0.05em;">
                                            <i class="bi bi-bell me-1"></i>
                                            @if(Auth::user()->role === 'mahasiswa_kip')
                                                Pengumuman Ormawa
                                            @elseif(Auth::user()->role === 'pengurus_ormawa')
                                                Kehadiran Perlu Diverifikasi
                                            @else
                                                Notifikasi
                                            @endif
                                        </li>
                                        <li><hr class="dropdown-divider my-1"></li>

                                        @if($notifCount === 0)
                                            <li class="px-3 py-3 text-center text-muted small">
                                                <i class="bi bi-check-circle me-1"></i> Tidak ada notifikasi baru
                                            </li>
                                        @else
                                            @if(Auth::user()->role === 'mahasiswa_kip')
                                                @foreach($notifItems as $notif)
                                                    <li>
                                                        <div class="dropdown-item py-2" style="white-space: normal;">
                                                            <div class="d-flex align-items-start">
                                                                <i class="bi bi-megaphone-fill text-primary me-2 mt-1 flex-shrink-0"></i>
                                                                <div>
                                                                    <div class="fw-semibold small">{{ Str::limit($notif->judul, 40) }}</div>
                                                                    <div class="text-muted" style="font-size: 0.75rem;">
                                                                        {{ $notif->ormawa->nama_ormawa ?? '-' }} &bull;
                                                                        {{ $notif->created_at->translatedFormat('d M Y') }}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li>
                                                @endforeach
                                            @elseif(Auth::user()->role === 'pengurus_ormawa')
                                                <li>
                                                    <a href="{{ route('pengurus.kehadiran.index') }}" class="dropdown-item py-2">
                                                        <div class="d-flex align-items-center">
                                                            <i class="bi bi-clock-history text-warning me-2 fs-5"></i>
                                                            <div>
                                                                <div class="fw-semibold small">{{ $notifCount }} kehadiran menunggu verifikasi</div>
                                                                <div class="text-muted" style="font-size: 0.75rem;">Klik untuk melihat daftar</div>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </li>
                                            @endif
                                        @endif

                                        <li><hr class="dropdown-divider my-1"></li>
                                        <li class="text-center py-1">
                                            @if(Auth::user()->role === 'mahasiswa_kip')
                                                <a href="{{ route('mahasiswa.dashboard') }}" class="small text-primary text-decoration-none">
                                                    Lihat semua pengumuman <i class="bi bi-arrow-right"></i>
                                                </a>
                                            @elseif(Auth::user()->role === 'pengurus_ormawa')
                                                <a href="{{ route('pengurus.kehadiran.index') }}" class="small text-primary text-decoration-none">
                                                    Buka halaman verifikasi <i class="bi bi-arrow-right"></i>
                                                </a>
                                            @endif
                                        </li>
                                    </ul>
                                </div>

                                <div class="dropdown">
                                    <button class="btn btn-light dropdown-toggle rounded-pill d-flex align-items-center" type="button" data-bs-toggle="dropdown">
                                        @if(Auth::user()->foto)
                                            <img src="{{ asset('storage/' . Auth::user()->foto) }}" alt="Profile" class="rounded-circle me-2" style="width: 24px; height: 24px; object-fit: cover;">
                                        @else
                                            <i class="bi bi-person-circle me-2"></i>
                                        @endif
                                        {{ Auth::user()->nama }}
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                                        <li>
                                            <a href="{{ route('profile.edit') }}" class="dropdown-item"><i class="bi bi-person me-2"></i> Profil Saya</a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i> Logout</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </nav>

                <!-- Page Content -->
                <div class="p-4">
                    <!-- Session Alert -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <!-- Global Image Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content bg-transparent border-0">
                <div class="modal-header border-0 pb-0">
                    <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1) grayscale(100%) brightness(200%);"></button>
                </div>
                <div class="modal-body text-center p-0">
                    <img src="" id="imageModalSrc" class="img-fluid rounded shadow-lg" alt="Preview Image" style="max-height: 85vh; object-fit: contain;">
                </div>
            </div>
        </div>
    </div>

    <!-- Script imports -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function() {
            // Apply standard DataTable styling
            if ($('.datatable').length > 0) {
                $('.datatable').DataTable({
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
                    }
                });
            }

            // Dark Mode Toggle Logic
            const themeIcon = $('#themeIcon');
            const currentTheme = document.documentElement.getAttribute('data-bs-theme');
            
            if (currentTheme === 'dark') {
                themeIcon.removeClass('bi-moon-fill').addClass('bi-sun-fill');
            } else {
                themeIcon.removeClass('bi-sun-fill').addClass('bi-moon-fill');
            }

            $('#darkModeToggle').on('click', function() {
                const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
                const newTheme = isDark ? 'light' : 'dark';
                
                document.documentElement.setAttribute('data-bs-theme', newTheme);
                localStorage.setItem('theme', newTheme);
                
                if (newTheme === 'dark') {
                    themeIcon.removeClass('bi-moon-fill').addClass('bi-sun-fill');
                } else {
                    themeIcon.removeClass('bi-sun-fill').addClass('bi-moon-fill');
                }
            });

            // Global Image Popup Logic
            $(document).on('click', '.img-popup', function(e) {
                const src = $(this).attr('src') || $(this).data('src');
                if (src) {
                    e.preventDefault();
                    $('#imageModalSrc').attr('src', src);
                    const imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
                    imageModal.show();
                }
            });
        });
    </script>
    @yield('scripts')
</body>
</html>
