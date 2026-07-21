@extends('layouts.app')
@section('title', 'Detail Kegiatan')

@section('content')
<div class="d-flex align-items-center mb-4 gap-3">
    <a href="{{ route('pengurus.kegiatan.index') }}" class="btn btn-light rounded-pill">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
    <div>
        <h4 class="fw-bold mb-0">{{ $kegiatan->nama_kegiatan }}</h4>
        <small class="text-muted">Detail & Rekap Kehadiran Kegiatan</small>
    </div>
    <div class="ms-auto d-flex gap-2">
        <a href="{{ route('pengurus.kegiatan.edit', $kegiatan->id) }}" class="btn btn-outline-primary rounded-pill">
            <i class="bi bi-pencil-square me-1"></i> Edit
        </a>
    </div>
</div>

<div class="row g-4">
    {{-- Detail Info --}}
    <div class="col-lg-4">
        <div class="card card-custom p-4">
            <h5 class="fw-bold mb-4"><i class="bi bi-info-circle-fill text-primary me-2"></i>Informasi Kegiatan</h5>
            <div class="mb-3">
                <label class="text-muted small d-block">Tanggal</label>
                <span class="fw-semibold"><i class="bi bi-calendar2 me-1 text-primary"></i>{{ \Carbon\Carbon::parse($kegiatan->tanggal)->translatedFormat('l, d F Y') }}</span>
            </div>
            <div class="mb-3">
                <label class="text-muted small d-block">Waktu</label>
                <span class="fw-semibold"><i class="bi bi-clock me-1 text-primary"></i>{{ substr($kegiatan->waktu_mulai,0,5) }} – {{ substr($kegiatan->waktu_selesai,0,5) }} WIB</span>
            </div>
            <div class="mb-3">
                <label class="text-muted small d-block">Tempat</label>
                <span class="fw-semibold"><i class="bi bi-geo-alt me-1 text-primary"></i>{{ $kegiatan->tempat }}</span>
            </div>
            <div class="mb-3">
                <label class="text-muted small d-block">Periode</label>
                <span class="badge bg-primary">{{ $kegiatan->periode }}</span>
            </div>
            <div class="mb-3">
                <label class="text-muted small d-block">Bobot Poin</label>
                <span class="fw-semibold text-success">{{ $kegiatan->poin }} Poin</span>
            </div>
            <div class="mb-3">
                <label class="text-muted small d-block">Status Absensi</label>
                <div class="d-flex align-items-center gap-2 mt-1">
                    @if($kegiatan->isAttendanceOpen())
                        <span class="badge bg-success"><i class="bi bi-unlock-fill me-1"></i>{{ $kegiatan->getAttendanceStatusText() }}</span>
                    @elseif($kegiatan->isAttendanceNotYetOpen())
                        <span class="badge bg-warning text-dark"><i class="bi bi-clock-fill me-1"></i>{{ $kegiatan->getAttendanceStatusText() }}</span>
                    @else
                        <span class="badge bg-danger"><i class="bi bi-lock-fill me-1"></i>{{ $kegiatan->getAttendanceStatusText() }}</span>
                    @endif
                    <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2" data-bs-toggle="modal" data-bs-target="#modalToggleAbsensi">
                        <i class="bi bi-gear"></i> Ubah
                    </button>
                </div>
            </div>
            @if($kegiatan->deskripsi)
            <div class="mb-0">
                <label class="text-muted small d-block">Deskripsi</label>
                <p class="small mb-0">{{ $kegiatan->deskripsi }}</p>
            </div>
            @endif
        </div>

        <div class="card card-custom p-4 mt-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-graph-up-arrow text-success me-2"></i>Statistik Kehadiran</h5>
            <div class="row g-3 text-center">
                <div class="col-4">
                    <div class="bg-light rounded p-2">
                        <div class="fw-bold fs-4 text-primary">{{ $totalPeserta }}</div>
                        <small class="text-muted">Peserta</small>
                    </div>
                </div>
                <div class="col-4">
                    <div class="bg-light rounded p-2">
                        <div class="fw-bold fs-4 text-success">{{ $totalHadir }}</div>
                        <small class="text-muted">Hadir</small>
                    </div>
                </div>
                <div class="col-4">
                    <div class="bg-light rounded p-2">
                        <div class="fw-bold fs-4 text-warning">{{ $totalPending }}</div>
                        <small class="text-muted">Pending</small>
                    </div>
                </div>
            </div>
            @if($totalPeserta > 0)
                <div class="progress mt-3" style="height: 8px;">
                    <div class="progress-bar bg-success" style="width: {{ round($totalHadir / $totalPeserta * 100) }}%"></div>
                </div>
                <small class="text-muted">Tingkat kehadiran: {{ round($totalHadir / $totalPeserta * 100) }}%</small>
            @endif
        </div>
    </div>

    {{-- Attendance List --}}
    <div class="col-lg-8">
        <div class="card card-custom p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0"><i class="bi bi-people-fill text-success me-2"></i>Daftar Kehadiran</h5>
                @if($totalPending > 0)
                    <a href="{{ route('pengurus.kehadiran.index') }}" class="btn btn-warning btn-sm rounded-pill">
                        <i class="bi bi-shield-check me-1"></i> Verifikasi {{ $totalPending }} Pending
                    </a>
                @endif
            </div>
            <div class="table-responsive">
                <table class="table align-middle datatable">
                    <thead>
                        <tr>
                            <th>Mahasiswa</th>
                            <th>NIM</th>
                            <th>Jurusan/Prodi</th>
                            <th>Status</th>
                            <th>Verifikasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kegiatan->kehadiran as $kh)
                            <tr>
                                <td class="fw-semibold">{{ $kh->keanggotaan->mahasiswa->pengguna->nama }}</td>
                                <td class="text-muted">{{ $kh->keanggotaan->mahasiswa->nim }}</td>
                                <td>
                                    <div class="small fw-semibold text-dark">{{ $kh->keanggotaan->mahasiswa->jurusan ?? '-' }}</div>
                                    <div class="small text-muted">{{ $kh->keanggotaan->mahasiswa->prodi ?? '-' }}</div>
                                </td>
                                <td>
                                    @if($kh->status_kehadiran == 'Hadir')
                                        <span class="badge bg-success">Hadir</span>
                                    @elseif($kh->status_kehadiran == 'Izin')
                                        <span class="badge bg-warning text-dark">Izin</span>
                                    @else
                                        <span class="badge bg-danger">Tidak Hadir</span>
                                    @endif
                                </td>
                                <td>
                                    @if($kh->status_verifikasi == 'Disetujui')
                                        <span class="badge bg-success"><i class="bi bi-check-circle-fill me-1"></i>Disetujui</span>
                                    @elseif($kh->status_verifikasi == 'Ditolak')
                                        <span class="badge bg-danger"><i class="bi bi-x-circle-fill me-1"></i>Ditolak</span>
                                    @else
                                        <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i>Pending</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-5">
                                    <i class="bi bi-person-x fs-2 d-block mb-2 opacity-50"></i>
                                    Belum ada mahasiswa yang mencatat kehadiran untuk kegiatan ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Toggle Absensi -->
<div class="modal fade" id="modalToggleAbsensi" tabindex="-1" aria-labelledby="modalToggleAbsensiLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 rounded-4 shadow-lg">
      <div class="modal-header border-bottom-0 pb-0">
        <h5 class="modal-title fw-bold" id="modalToggleAbsensiLabel"><i class="bi bi-gear-fill text-primary me-2"></i>Ubah Status Absensi</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body pt-3">
        <form action="{{ route('pengurus.kegiatan.toggle-absensi', $kegiatan->id) }}" method="POST">
            @csrf
            @method('PATCH')
            
            <div class="mb-4">
                <label class="form-label text-muted small">Pilih Mode Status Absensi</label>
                <div class="form-check mb-2">
                  <input class="form-check-input" type="radio" name="status_absensi" id="statusAuto" value="auto" {{ $kegiatan->status_absensi === 'auto' ? 'checked' : '' }}>
                  <label class="form-check-label fw-semibold" for="statusAuto">
                    Otomatis (Auto)
                  </label>
                  <div class="form-text mt-0">Absensi akan otomatis ditutup jika waktu kegiatan (tanggal & waktu selesai) sudah lewat.</div>
                </div>
                
                <div class="form-check mb-2">
                  <input class="form-check-input" type="radio" name="status_absensi" id="statusBuka" value="buka" {{ $kegiatan->status_absensi === 'buka' ? 'checked' : '' }}>
                  <label class="form-check-label fw-semibold text-success" for="statusBuka">
                    Buka Paksa (Force Open)
                  </label>
                  <div class="form-text mt-0">Mahasiswa tetap bisa absen kapanpun walau waktu kegiatan sudah lewat lama.</div>
                </div>
                
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="status_absensi" id="statusTutup" value="tutup" {{ $kegiatan->status_absensi === 'tutup' ? 'checked' : '' }}>
                  <label class="form-check-label fw-semibold text-danger" for="statusTutup">
                    Tutup Paksa (Force Close)
                  </label>
                  <div class="form-text mt-0">Mahasiswa tidak bisa absen walau kegiatan sedang berlangsung saat ini.</div>
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary btn-gradient-primary">Simpan Perubahan</button>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
