@extends('layouts.app')
@section('title', 'Kegiatan Ormawa')

@section('content')
<div class="card card-custom p-4">
    <h4 class="fw-bold mb-4"><i class="bi bi-calendar3 text-primary me-2"></i>Daftar Kegiatan Ormawa Anda</h4>

    @if(isset($info))
        <div class="alert alert-info">
            <i class="bi bi-info-circle-fill me-2"></i> {{ $info }}
        </div>
    @endif

    <div class="table-responsive">
        <table class="table align-middle datatable">
            <thead>
                <tr>
                    <th>Nama Kegiatan</th>
                    <th>Periode</th>
                    <th>Bobot (Poin)</th>
                    <th>Tanggal</th>
                    <th>Waktu</th>
                    <th>Tempat</th>
                    <th>Deskripsi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($kegiatans as $kegiatan)
                    <tr>
                        <td><span class="fw-semibold">{{ $kegiatan->nama_kegiatan }}</span></td>
                        <td><span class="badge bg-info text-dark">{{ $kegiatan->periode ? $kegiatan->periode->tahun . ' - ' . $kegiatan->periode->semester : '-' }}</span></td>
                        <td><span class="badge bg-primary">{{ $kegiatan->poin }}</span></td>
                        <td>{{ \Carbon\Carbon::parse($kegiatan->tanggal)->translatedFormat('d F Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($kegiatan->waktu_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($kegiatan->waktu_selesai)->format('H:i') }}</td>
                        <td>{{ $kegiatan->tempat }}</td>
                        <td>{{ $kegiatan->deskripsi ?? '-' }}</td>
                        <td>
                            @if(in_array($kegiatan->id, $alreadyLogged))
                                <span class="badge bg-secondary py-2 px-3 rounded-pill"><i class="bi bi-check-lg me-1"></i>Telah Dicatat</span>
                            @elseif($kegiatan->isAttendanceNotYetOpen())
                                <span class="badge bg-warning py-2 px-3 rounded-pill text-dark"><i class="bi bi-clock me-1"></i>Belum Mulai</span>
                            @elseif(!$kegiatan->isAttendanceOpen())
                                <span class="badge bg-danger py-2 px-3 rounded-pill"><i class="bi bi-x-circle me-1"></i>Absensi Ditutup</span>
                            @else
                                <button type="button" class="btn btn-sm btn-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalCatat{{ $kegiatan->id }}">
                                    <i class="bi bi-pencil me-1"></i> Catat Kehadiran
                                </button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @foreach($kegiatans as $kegiatan)
        @if(!in_array($kegiatan->id, $alreadyLogged) && !$kegiatan->isAttendanceNotYetOpen() && $kegiatan->isAttendanceOpen())
            <!-- Modal for {{ $kegiatan->id }} -->
            <div class="modal fade" id="modalCatat{{ $kegiatan->id }}" tabindex="-1" aria-labelledby="modalCatatLabel{{ $kegiatan->id }}" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title fw-bold" id="modalCatatLabel{{ $kegiatan->id }}"><i class="bi bi-pencil-square text-primary me-2"></i>Catat Kehadiran Mandiri</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('mahasiswa.kehadiran.catat.store', $kegiatan->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="kegiatan_id" value="{{ $kegiatan->id }}">
                            <div class="modal-body">
                                <p class="text-muted small mb-3">Mencatat kehadiran Anda untuk kegiatan <strong>{{ $kegiatan->nama_kegiatan }}</strong>.</p>
                                
                                <div class="alert alert-info py-2 small mb-3">
                                    <i class="bi bi-info-circle-fill me-1"></i> Kehadiran berstatus <strong>PENDING</strong> sampai disetujui Pengurus.
                                </div>

                                @if($errors->any() && old('kegiatan_id') == $kegiatan->id)
                                    <div class="alert alert-danger py-2 small mb-3">
                                        <ul class="mb-0 ps-3">
                                            @foreach($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <div class="mb-3">
                                    <label class="form-label small text-muted">Tanggal & Lokasi</label>
                                    <input type="text" class="form-control" value="{{ \Carbon\Carbon::parse($kegiatan->tanggal)->translatedFormat('d F Y') }} di {{ $kegiatan->tempat }}" disabled>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Pilih Status Kehadiran</label>
                                    <select name="status_kehadiran" class="form-select" required>
                                        <option value="Hadir" {{ old('status_kehadiran') == 'Hadir' ? 'selected' : '' }}>Hadir</option>
                                        <option value="Tidak Hadir" {{ old('status_kehadiran') == 'Tidak Hadir' ? 'selected' : '' }}>Tidak Hadir</option>
                                        <option value="Izin" {{ old('status_kehadiran') == 'Izin' ? 'selected' : '' }}>Izin</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Bukti Foto <span class="text-danger">*</span></label>
                                    <input type="file" name="bukti_foto" class="form-control" accept="image/*" required>
                                    <small class="text-muted">Wajib dilampirkan (Foto kehadiran jika Hadir, atau foto surat keterangan jika Izin).</small>
                                </div>
                                
                                <div class="mb-0">
                                    <label class="form-label">Keterangan Tambahan (Opsional)</label>
                                    <textarea name="keterangan" class="form-control" rows="2" placeholder="Tulis alasan jika Izin / Tidak Hadir, atau catatan lain...">{{ old('keterangan') }}</textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary btn-gradient-primary">Kirim Kehadiran</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

</div>

@if($errors->any() && old('kegiatan_id'))
    @section('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var myModal = new bootstrap.Modal(document.getElementById('modalCatat{{ old('kegiatan_id') }}'));
            myModal.show();
        });
    </script>
    @endsection
@endif
@endsection
