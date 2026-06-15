@extends('layouts.app')
@section('title', 'Admin Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold mb-0">Dashboard Admin</h3>
    <span class="badge bg-danger px-3 py-2 text-uppercase">Admin Control Panel</span>
</div>

<!-- Statistics Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card card-custom p-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted small text-uppercase">Total Pengguna</h6>
                    <h2 class="fw-bold mb-0">{{ $stats['total_pengguna'] }}</h2>
                </div>
                <div class="fs-1 text-primary"><i class="bi bi-people-fill"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted small text-uppercase">Mahasiswa KIP</h6>
                    <h2 class="fw-bold mb-0">{{ $stats['total_mahasiswa'] }}</h2>
                </div>
                <div class="fs-1 text-success"><i class="bi bi-mortarboard-fill"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted small text-uppercase">Total Ormawa</h6>
                    <h2 class="fw-bold mb-0">{{ $stats['total_ormawa'] }}</h2>
                </div>
                <div class="fs-1 text-warning"><i class="bi bi-diagram-3-fill"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted small text-uppercase">Total Kegiatan</h6>
                    <h2 class="fw-bold mb-0">{{ $stats['total_kegiatan'] }}</h2>
                </div>
                <div class="fs-1 text-info"><i class="bi bi-calendar-event-fill"></i></div>
            </div>
        </div>
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
                                <td>{{ $u->ormawa ? $u->ormawa->nama_ormawa : '-' }}</td>
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
