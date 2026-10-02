@extends('layouts.app')
@section('title', 'Kelola Kegiatan')

@section('content')
<div class="card card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="bi bi-calendar-event-fill text-primary me-2"></i>Kelola Kegiatan Ormawa</h4>
        <button type="button" class="btn btn-gradient-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#modalTambahKegiatan"><i class="bi bi-plus-circle me-1"></i> Buat Kegiatan</button>
    </div>

    <!-- Filter & Search Form -->
    <form action="{{ route('pengurus.kegiatan.index') }}" method="GET" class="mb-4 row g-2">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Cari Nama Kegiatan..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <input type="date" name="tanggal_mulai" class="form-control" placeholder="Tgl Mulai" value="{{ request('tanggal_mulai') }}">
        </div>
        <div class="col-md-3">
            <input type="date" name="tanggal_selesai" class="form-control" placeholder="Tgl Selesai" value="{{ request('tanggal_selesai') }}">
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary w-100" type="submit"><i class="bi bi-filter"></i> Filter</button>
            @if(request('search') || request('tanggal_mulai') || request('tanggal_selesai'))
                <a href="{{ route('pengurus.kegiatan.index') }}" class="btn btn-sm btn-outline-secondary w-100 mt-1">Reset</a>
            @endif
        </div>
    </form>

    <div class="table-responsive">
        <table class="table align-middle">
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
                @forelse($kegiatans as $kegiatan)
                    <tr>
                        <td><span class="fw-semibold">{{ $kegiatan->nama_kegiatan }}</span></td>
                        <td><span class="badge bg-info text-dark">{{ $kegiatan->periode ? $kegiatan->periode->tahun . ' - ' . $kegiatan->periode->semester : '-' }}</span></td>
                        <td><span class="badge bg-primary">{{ $kegiatan->poin }}</span></td>
                        <td>{{ \Carbon\Carbon::parse($kegiatan->tanggal)->translatedFormat('d F Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($kegiatan->waktu_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($kegiatan->waktu_selesai)->format('H:i') }}</td>
                        <td>{{ $kegiatan->tempat }}</td>
                        <td>{{ Str::limit($kegiatan->deskripsi, 50) }}</td>
                        <td>
                            <a href="{{ route('pengurus.kegiatan.show', $kegiatan->id) }}" class="btn btn-sm btn-outline-info me-1" title="Detail Kegiatan"><i class="bi bi-eye"></i></a>
                            <button type="button" class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#modalEditKegiatan-{{ $kegiatan->id }}" title="Edit Kegiatan"><i class="bi bi-pencil-square"></i></button>

                            <!-- Modal Edit Kegiatan -->
                            <div class="modal fade text-start" id="modalEditKegiatan-{{ $kegiatan->id }}" tabindex="-1" aria-labelledby="modalEditKegiatanLabel-{{ $kegiatan->id }}" aria-hidden="true">
                              <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 rounded-4 shadow-lg">
                                  <div class="modal-header border-bottom-0 pb-0">
                                    <h5 class="modal-title fw-bold" id="modalEditKegiatanLabel-{{ $kegiatan->id }}"><i class="bi bi-pencil-square text-primary me-2"></i>Edit Kegiatan</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                  </div>
                                  <div class="modal-body pt-3">
                                    <form action="{{ route('pengurus.kegiatan.update', $kegiatan->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Nama Kegiatan</label>
                                            <input type="text" name="nama_kegiatan" class="form-control" value="{{ old('nama_kegiatan', $kegiatan->nama_kegiatan) }}" required>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Periode</label>
                                                <select name="periode_id" class="form-select" required>
                                                    <option value="">Pilih Periode...</option>
                                                    @foreach($periodes->sortByDesc('tanggal_mulai') as $p)
                                                        <option value="{{ $p->id }}" {{ old('periode_id', $kegiatan->periode_id) == $p->id ? 'selected' : '' }}>
                                                            {{ $p->tahun }} - {{ $p->semester }}
                                                            (Mulai: {{ \Carbon\Carbon::parse($p->tanggal_mulai)->format('d M Y') }})
                                                            {{ $p->status === 'Aktif' ? '✓ Aktif' : '' }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Bobot (Poin)</label>
                                                <select name="poin" class="form-select" required>
                                                    <option value="">Pilih Poin...</option>
                                                    <option value="1" {{ old('poin', $kegiatan->poin) == '1' ? 'selected' : '' }}>1 Poin (Kegiatan Rutin)</option>
                                                    <option value="2" {{ old('poin', $kegiatan->poin) == '2' ? 'selected' : '' }}>2 Poin (Kepanitiaan Menengah)</option>
                                                    <option value="3" {{ old('poin', $kegiatan->poin) == '3' ? 'selected' : '' }}>3 Poin (Kepanitiaan Besar)</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Tanggal Pelaksanaan</label>
                                            <input type="date" name="tanggal" class="form-control" value="{{ old('tanggal', $kegiatan->tanggal) }}" required>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Waktu Mulai</label>
                                                <input type="time" name="waktu_mulai" class="form-control" value="{{ old('waktu_mulai', \Carbon\Carbon::parse($kegiatan->waktu_mulai)->format('H:i')) }}" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Waktu Selesai</label>
                                                <input type="time" name="waktu_selesai" class="form-control" value="{{ old('waktu_selesai', \Carbon\Carbon::parse($kegiatan->waktu_selesai)->format('H:i')) }}" required>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Lokasi / Tempat</label>
                                            <input type="text" name="tempat" class="form-control" value="{{ old('tempat', $kegiatan->tempat) }}" required>
                                        </div>
                                        <div class="mb-4">
                                            <label class="form-label">Deskripsi Kegiatan</label>
                                            <textarea name="deskripsi" class="form-control" rows="4">{{ old('deskripsi', $kegiatan->deskripsi) }}</textarea>
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
                            <form action="{{ route('pengurus.kegiatan.destroy', $kegiatan->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kegiatan ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">Tidak ada data kegiatan ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-end mt-3">
        {{ $kegiatans->links('pagination::bootstrap-5') }}
    </div>
</div>

<!-- Modal Tambah Kegiatan -->
<div class="modal fade" id="modalTambahKegiatan" tabindex="-1" aria-labelledby="modalTambahKegiatanLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 rounded-4 shadow-lg">
      <div class="modal-header border-bottom-0 pb-0">
        <h5 class="modal-title fw-bold" id="modalTambahKegiatanLabel"><i class="bi bi-calendar-plus text-primary me-2"></i>Buat Kegiatan Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body pt-3">
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('pengurus.kegiatan.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Nama Kegiatan</label>
                <input type="text" name="nama_kegiatan" class="form-control" placeholder="Contoh: Rapat Kerja Ormawa" value="{{ old('nama_kegiatan') }}" required>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Periode</label>
                    @php
                        $latestPeriode = $periodes->sortByDesc('tanggal_mulai')->first();
                    @endphp
                    <select name="periode_id" class="form-select" required>
                        <option value="">Pilih Periode...</option>
                        @foreach($periodes->sortByDesc('tanggal_mulai') as $p)
                            <option value="{{ $p->id }}"
                                {{ old('periode_id', $latestPeriode?->id) == $p->id ? 'selected' : '' }}>
                                {{ $p->tahun }} - {{ $p->semester }}
                                (Mulai: {{ \Carbon\Carbon::parse($p->tanggal_mulai)->format('d M Y') }})
                                {{ $p->status === 'Aktif' ? '✓ Aktif' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Bobot (Poin)</label>
                    <select name="poin" class="form-select" required>
                        <option value="">Pilih Poin...</option>
                        <option value="1" {{ old('poin') == '1' ? 'selected' : '' }}>1 Poin (Kegiatan Rutin)</option>
                        <option value="2" {{ old('poin') == '2' ? 'selected' : '' }}>2 Poin (Kepanitiaan Menengah)</option>
                        <option value="3" {{ old('poin') == '3' ? 'selected' : '' }}>3 Poin (Kepanitiaan Besar)</option>
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Tanggal Pelaksanaan</label>
                <input type="date" name="tanggal" class="form-control" value="{{ old('tanggal') }}" required>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Waktu Mulai</label>
                    <input type="time" name="waktu_mulai" class="form-control" value="{{ old('waktu_mulai') }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Waktu Selesai</label>
                    <input type="time" name="waktu_selesai" class="form-control" value="{{ old('waktu_selesai') }}" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Lokasi / Tempat</label>
                <input type="text" name="tempat" class="form-control" placeholder="Contoh: Aula Kampus / Zoom" value="{{ old('tempat') }}" required>
            </div>
            <div class="mb-4">
                <label class="form-label">Deskripsi Kegiatan</label>
                <textarea name="deskripsi" class="form-control" rows="4" placeholder="Tulis rincian atau agenda kegiatan...">{{ old('deskripsi') }}</textarea>
            </div>

            <div class="d-flex justify-content-end">
                <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary btn-gradient-primary">Buat Kegiatan</button>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Auto-open modal if validation errors exist
        @if($errors->any())
            var myModal = new bootstrap.Modal(document.getElementById('modalTambahKegiatan'));
            myModal.show();
        @endif
    });
</script>
@endsection
