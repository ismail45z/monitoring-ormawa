@extends('layouts.app')
@section('title', 'Mahasiswa Dashboard')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0">Dashboard Mahasiswa KIP-K</h3>
            <p class="text-muted mb-0">Selamat datang kembali,
                {{ $mahasiswa ? $mahasiswa->pengguna->nama : Auth::user()->nama }}</p>
        </div>
    </div>

    @if(!$mahasiswa)
        <div class="alert alert-warning card-custom p-4 text-center">
            <i class="bi bi-exclamation-triangle-fill fs-1 text-warning mb-2 d-block"></i>
            <h5>Profil Mahasiswa Belum Dibuat</h5>
            <p class="mb-0">Mohon hubungi Administrator untuk membuat profil KIP-Kuliah untuk akun Anda.</p>
        </div>
    @else
        <div class="row g-4 mb-4">
            <!-- Keaktifan Per Ormawa Cards -->
            @forelse($rekapPerOrmawa as $rekap)
                <div class="col-md-4">
                    <div class="card card-custom p-4 text-center h-100 d-flex flex-column justify-content-center">
                        <small class="text-muted text-uppercase fw-bold mb-1">{{ $rekap['ormawa']->nama_ormawa }}</small>
                        <div class="display-4 fw-bold {{ $rekap['persentase'] >= 60 ? 'text-success' : 'text-danger' }} my-2">
                            {{ $rekap['persentase'] }}</div>
                        <small class="text-muted mb-2">Poin Keaktifan</small>
                        @php
                            $badgeClass = match ($rekap['statusKeaktifan']) {
                                'Sangat Aktif' => 'badge-custom-sangat-aktif',
                                'Aktif' => 'badge-custom-aktif',
                                'Cukup' => 'badge-custom-cukup',
                                default => 'badge-custom-tidak',
                            };
                        @endphp
                        <span class="{{ $badgeClass }} text-uppercase fw-semibold">
                            {{ $rekap['statusKeaktifan'] }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info">Anda belum terdaftar di Ormawa manapun.</div>
                </div>
            @endforelse

            <!-- Student Quick Profile Card -->
            <div class="col-md-{{ $rekapPerOrmawa->count() >= 3 ? '12' : (12 - ($rekapPerOrmawa->count() * 4)) }}">
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
                            @forelse($mahasiswa->ormawas as $orm)
                                <span class="badge bg-primary me-1">{{ $orm->nama_ormawa }}</span>
                            @empty
                                <span class="text-muted">Tidak Mengikuti</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pengumuman Ormawa -->
        @if(isset($pengumuman) && $pengumuman->count() > 0)
            <div class="row g-4 mb-4">
                <div class="col-12">
                    <div class="card card-custom p-4">
                        <h5 class="fw-bold mb-3"><i class="bi bi-megaphone-fill text-primary me-2"></i>Pengumuman Ormawa</h5>
                        <div class="list-group list-group-flush">
                            @foreach($pengumuman as $p)
                                <div class="list-group-item px-0 py-3 border-bottom">
                                    <div class="d-flex w-100 justify-content-between mb-1">
                                        <h6 class="mb-0 fw-bold">{{ $p->judul }}</h6>
                                        <small class="text-muted">{{ $p->created_at->diffForHumans() }}</small>
                                    </div>
                                    <span class="badge bg-primary mb-2">{{ $p->ormawa->nama_ormawa }}</span>
                                    <p class="mb-2 text-muted" style="white-space: pre-line;">{{ $p->isi }}</p>
                                    @if($p->lampiran)
                                        <div class="mt-2 text-center text-md-start">
                                            <img src="{{ asset('storage/' . $p->lampiran) }}" alt="Lampiran Pengumuman"
                                                class="img-fluid rounded shadow-sm img-popup" style="max-height: 400px; object-fit: contain;">
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Recent Presence Timeline -->
        <div class="card card-custom p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0"><i class="bi bi-clock-history text-success me-2"></i>Kehadiran Terakhir</h5>
                <a href="{{ route('mahasiswa.kegiatan.index') }}" class="btn btn-sm btn-gradient-primary rounded-pill"><i
                        class="bi bi-calendar3 me-1"></i> Catat Kehadiran</a>
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
                                        <span class="badge bg-warning text-dark"><i
                                                class="bi bi-hourglass-split me-1"></i>Pending</span>
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