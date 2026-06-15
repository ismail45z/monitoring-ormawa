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
                    <div class="py-2 px-3 bg-secondary bg-opacity-25 rounded-3">
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
                    @elseif(Auth::user()->role == 'mahasiswa_kip')
                        <li class="nav-item">
                            <a class="nav-link {{ Route::is('mahasiswa.dashboard') ? 'active' : '' }}" href="{{ route('mahasiswa.dashboard') }}">
                                <i class="bi bi-speedometer2 me-2"></i> Dashboard
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
                                <span class="me-3 text-muted d-none d-md-inline">Hari ini: <strong class="text-dark">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</strong></span>
                                <div class="dropdown">
                                    <button class="btn btn-light dropdown-toggle rounded-pill" type="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-person-circle me-1"></i> {{ Auth::user()->nama }}
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
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
        });
    </script>
    @yield('scripts')
</body>
</html>
