@extends('layouts.app')
@section('title', 'Admin Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-0">Dashboard Admin</h3>
        <span class="badge bg-danger px-3 py-2 mt-2 text-uppercase">Admin Control Panel</span>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.export.pdf') }}" target="_blank" class="btn btn-outline-danger shadow-sm"><i class="bi bi-file-earmark-pdf-fill me-1"></i> Laporan PDF</a>
        <a href="{{ route('admin.export.excel') }}" class="btn btn-outline-success shadow-sm"><i class="bi bi-file-earmark-excel-fill me-1"></i> Laporan Excel</a>
    </div>
</div>

@section('styles')
<style>
    .stat-card-link {
        text-decoration: none;
        color: inherit;
        display: block;
    }
    .stat-card-link .card-custom {
        border-left: 4px solid transparent;
        transition: all 0.25s ease;
    }
    .stat-card-link:hover .card-custom {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px -8px rgba(0,0,0,0.3);
    }
    .stat-card-link.blue:hover  .card-custom { border-left-color: #3b82f6; }
    .stat-card-link.green:hover .card-custom { border-left-color: #10b981; }
    .stat-card-link.yellow:hover .card-custom { border-left-color: #f59e0b; }
    .stat-card-link.cyan:hover  .card-custom { border-left-color: #06b6d4; }
    .stat-arrow { opacity: 0; transition: opacity 0.2s; }
    .stat-card-link:hover .stat-arrow { opacity: 0.6; }
</style>
@endsection

<!-- Statistics Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <a href="{{ route('admin.pengguna.index') }}" class="stat-card-link blue">
            <div class="card card-custom p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted small text-uppercase">Total Pengguna</h6>
                        <h2 class="fw-bold mb-0">{{ $stats['total_pengguna'] }}</h2>
                        <small class="text-primary stat-arrow"><i class="bi bi-arrow-right-short"></i> Kelola Pengguna</small>
                    </div>
                    <div class="fs-1 text-primary"><i class="bi bi-people-fill"></i></div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="{{ route('admin.mahasiswa.index') }}" class="stat-card-link green">
            <div class="card card-custom p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted small text-uppercase">Mahasiswa KIP</h6>
                        <h2 class="fw-bold mb-0">{{ $stats['total_mahasiswa'] }}</h2>
                        <small class="text-success stat-arrow"><i class="bi bi-arrow-right-short"></i> Kelola Mahasiswa</small>
                    </div>
                    <div class="fs-1 text-success"><i class="bi bi-mortarboard-fill"></i></div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="{{ route('admin.ormawa.index') }}" class="stat-card-link yellow">
            <div class="card card-custom p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted small text-uppercase">Total Ormawa</h6>
                        <h2 class="fw-bold mb-0">{{ $stats['total_ormawa'] }}</h2>
                        <small class="text-warning stat-arrow"><i class="bi bi-arrow-right-short"></i> Kelola Ormawa</small>
                    </div>
                    <div class="fs-1 text-warning"><i class="bi bi-diagram-3-fill"></i></div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="{{ route('admin.audit.index') }}" class="stat-card-link cyan">
            <div class="card card-custom p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted small text-uppercase">Total Kegiatan</h6>
                        <h2 class="fw-bold mb-0">{{ $stats['total_kegiatan'] }}</h2>
                        <small class="text-info stat-arrow"><i class="bi bi-arrow-right-short"></i> Lihat Audit Log</small>
                    </div>
                    <div class="fs-1 text-info"><i class="bi bi-calendar-event-fill"></i></div>
                </div>
            </div>
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Users -->
    <div class="col-lg-6">
        <div class="card card-custom p-4 h-100">
            <h5 class="fw-bold mb-3"><i class="bi bi-person-plus me-2 text-primary"></i>Pengguna Baru</h5>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Role</th>
                            <th>Ormawa</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentUsers as $u)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $u->nama }}</div>
                                    <small class="text-muted">{{ $u->email }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-secondary text-uppercase">{{ str_replace('_', ' ', $u->role) }}</span>
                                </td>
                                <td>
                                    @if($u->role === 'pengurus_ormawa' && $u->ormawa)
                                        <span class="badge rounded-pill" style="background:rgba(99,102,241,0.15);color:#818cf8;font-weight:500;">{{ $u->ormawa->nama_ormawa }}</span>
                                    @elseif($u->role === 'mahasiswa_kip' && $u->mahasiswa && $u->mahasiswa->ormawas->count() > 0)
                                        @foreach($u->mahasiswa->ormawas as $orm)
                                            <span class="badge rounded-pill mb-1" style="background:rgba(16,185,129,0.15);color:#10b981;font-weight:500;">{{ $orm->nama_ormawa }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">Belum ada pengguna.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="col-lg-6">
        <div class="card card-custom p-4 h-100">
            <h5 class="fw-bold mb-3"><i class="bi bi-calendar-check me-2 text-success"></i>Kegiatan Terbaru</h5>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Kegiatan</th>
                            <th>Penyelenggara</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentActivities as $act)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $act->nama_kegiatan }}</div>
                                    <small class="text-muted"><i class="bi bi-geo-alt-fill me-1"></i>{{ $act->tempat }}</small>
                                </td>
                                <td>{{ $act->ormawa->nama_ormawa }}</td>
                                <td>{{ \Carbon\Carbon::parse($act->tanggal)->translatedFormat('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">Belum ada kegiatan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
