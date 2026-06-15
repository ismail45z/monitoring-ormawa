@extends('layouts.app')
@section('title', 'Pengurus Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-0">Dashboard Pengurus Ormawa</h3>
        <p class="text-muted mb-0">Selamat datang, Pengurus {{ $ormawa ? $ormawa->nama_ormawa : 'Ormawa' }}</p>
    </div>
    @if($ormawa)
        <span class="badge bg-primary px-3 py-2 text-uppercase">{{ $ormawa->jenis }} - Periode {{ $ormawa->periode }}</span>
    @endif
</div>

@if(!$ormawa)
    <div class="alert alert-warning card-custom p-4 text-center">
        <i class="bi bi-exclamation-triangle-fill fs-1 text-warning mb-2 d-block"></i>
        <h5>Akun Anda Belum Terhubung</h5>
        <p class="mb-0">Mohon hubungi Administrator untuk menghubungkan akun pengguna Anda dengan data Organisasi Mahasiswa (Ormawa).</p>
    </div>
@else
    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card card-custom p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted small text-uppercase">Total Kegiatan Kita</h6>
                        <h2 class="fw-bold mb-0">{{ $totalKegiatan }}</h2>
                    </div>
                    <div class="fs-1 text-primary"><i class="bi bi-calendar-event"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-custom p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted small text-uppercase">Total Anggota KIP-K</h6>
                        <h2 class="fw-bold mb-0">{{ $totalAnggota }}</h2>
                    </div>
                    <div class="fs-1 text-success"><i class="bi bi-mortarboard"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ormawa Profile & Activities -->
    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card card-custom p-4 h-100">
                <h5 class="fw-bold mb-4"><i class="bi bi-info-circle-fill me-2 text-primary"></i>Profil Ormawa</h5>
                <div class="mb-3">
                    <label class="text-muted small d-block">Nama Organisasi</label>
                    <div class="fw-bold">{{ $ormawa->nama_ormawa }}</div>
                </div>
                <div class="mb-3">
                    <label class="text-muted small d-block">Ketua Umum</label>
                    <div class="fw-semibold">{{ $ormawa->ketua }}</div>
                </div>
                <div class="mb-3">
                    <label class="text-muted small d-block">Pembina</label>
                    <div class="fw-semibold">{{ $ormawa->pembina ?? '-' }}</div>
                </div>
                <div class="mb-3">
                    <label class="text-muted small d-block">Deskripsi</label>
                    <p class="text-muted mb-0 small">{{ $ormawa->deskripsi ?? 'Tidak ada deskripsi.' }}</p>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card card-custom p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0"><i class="bi bi-calendar3 me-2 text-success"></i>Kegiatan Terbaru</h5>
                    <a href="{{ route('pengurus.kegiatan.create') }}" class="btn btn-sm btn-gradient-primary rounded-pill"><i class="bi bi-plus-circle me-1"></i> Tambah</a>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Nama Kegiatan</th>
                                <th>Tanggal</th>
                                <th>Tempat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentActivities as $act)
                                <tr>
                                    <td><span class="fw-semibold">{{ $act->nama_kegiatan }}</span></td>
                                    <td>{{ \Carbon\Carbon::parse($act->tanggal)->translatedFormat('d M Y') }}</td>
                                    <td>{{ $act->tempat }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">Belum ada kegiatan yang dibuat.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection
