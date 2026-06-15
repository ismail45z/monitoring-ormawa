@extends('layouts.app')
@section('title', 'Mahasiswa Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-0">Dashboard Mahasiswa KIP-K</h3>
        <p class="text-muted mb-0">Selamat datang kembali, {{ $mahasiswa ? $mahasiswa->pengguna->nama : Auth::user()->nama }}</p>
    </div>
    @if($mahasiswa)
        <span class="{{ $statusKeaktifan == 'AKTIF' ? 'badge-custom-aktif' : 'badge-custom-tidak' }} text-uppercase fw-bold">
            Status Keaktifan: {{ $statusKeaktifan }}
        </span>
    @endif
</div>

@if(!$mahasiswa)
    <div class="alert alert-warning card-custom p-4 text-center">
        <i class="bi bi-exclamation-triangle-fill fs-1 text-warning mb-2 d-block"></i>
        <h5>Profil Mahasiswa Belum Dibuat</h5>
        <p class="mb-0">Mohon hubungi Administrator untuk membuat profil KIP-Kuliah untuk akun Anda.</p>
    </div>
@else
    <div class="row g-4 mb-4">
        <!-- Attendance Stats Card -->
        <div class="col-md-4">
            <div class="card card-custom p-4 text-center h-100 d-flex flex-column justify-content-center">
                <h6 class="text-muted small text-uppercase">Persentase Kehadiran</h6>
                <div class="display-3 fw-bold text-primary my-2">{{ $persentase }}%</div>
                <p class="text-muted small mb-0">Dari seluruh kegiatan yang diselenggarakan oleh Ormawa Anda</p>
            </div>
        </div>

        <!-- Student Quick Profile Card -->
        <div class="col-md-8">
            <div class="card card-custom p-4 h-100">
                <h5 class="fw-bold mb-3"><i class="bi bi-person-vcard-fill text-primary me-2"></i>Informasi Mahasiswa</h5>
                <div class="row">
                    <div class="col-sm-6 mb-3">
                        <small class="text-muted d-block">Nama Lengkap</small>
                        <span class="fw-semibold">{{ $mahasiswa->pengguna->nama }}</span>
                    </div>
                    <div class="col-sm-6 mb-3">
                        <small class="text-muted d-block">Nomor Induk Mahasiswa (NIM)</small>
                        <span class="fw-semibold">{{ $mahasiswa->nim }}</span>
                    </div>
                    <div class="col-sm-6 mb-3">
                        <small class="text-muted d-block">No. KIP-Kuliah</small>
                        <span class="fw-semibold">{{ $mahasiswa->no_kip }}</span>
                    </div>
                    <div class="col-sm-6 mb-3">
                        <small class="text-muted d-block">Program Studi / Jurusan</small>
                        <span class="fw-semibold">{{ $mahasiswa->prodi }} ({{ $mahasiswa->jurusan }})</span>
                    </div>
                    <div class="col-sm-6 mb-0">
                        <small class="text-muted d-block">Angkatan</small>
                        <span class="fw-semibold">{{ $mahasiswa->angkatan }}</span>
                    </div>
                    <div class="col-sm-6 mb-0">
                        <small class="text-muted d-block">Organisasi Diikuti</small>
                        <span class="fw-semibold text-primary">{{ $mahasiswa->ormawa ? $mahasiswa->ormawa->nama_ormawa : 'Tidak Mengikuti' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Presence Timeline -->
    <div class="card card-custom p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0"><i class="bi bi-clock-history text-success me-2"></i>Kehadiran Terakhir</h5>
            <a href="{{ route('mahasiswa.kegiatan.index') }}" class="btn btn-sm btn-gradient-primary rounded-pill"><i class="bi bi-calendar3 me-1"></i> Catat Kehadiran</a>
        </div>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Kegiatan</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Verifikasi</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentPresence as $pres)
                        <tr>
                            <td><span class="fw-semibold">{{ $pres->kegiatan->nama_kegiatan }}</span></td>
                            <td>{{ \Carbon\Carbon::parse($pres->kegiatan->tanggal)->translatedFormat('d M Y') }}</td>
                            <td>
                                @if($pres->status_kehadiran == 'Hadir')
                                    <span class="badge bg-success">Hadir</span>
                                @elseif($pres->status_kehadiran == 'Tidak Hadir')
                                    <span class="badge bg-danger">Tidak Hadir</span>
                                @else
                                    <span class="badge bg-warning text-dark">Izin</span>
                                @endif
                            </td>
                            <td>
                                @if($pres->status_verifikasi == 'Disetujui')
                                    <span class="badge bg-success"><i class="bi bi-check-circle-fill me-1"></i>Disetujui</span>
                                @elseif($pres->status_verifikasi == 'Ditolak')
                                    <span class="badge bg-danger"><i class="bi bi-x-circle-fill me-1"></i>Ditolak</span>
                                @else
                                    <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i>Pending</span>
                                @endif
                            </td>
                            <td>{{ $pres->keterangan ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Belum ada riwayat kehadiran dicatat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endif
@endsection
