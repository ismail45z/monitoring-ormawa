@extends('layouts.app')
@section('title', 'Kelola Pengumuman')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0"><i class="bi bi-megaphone-fill text-primary me-2"></i>Kelola Pengumuman</h4>
        <p class="text-muted mb-0">Informasi dan berita untuk anggota Ormawa</p>
    </div>
    <button type="button" class="btn btn-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#modalTambahPengumuman">
        <i class="bi bi-plus-circle me-1"></i> Buat Pengumuman
    </button>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="card card-custom p-4">
    @if($pengumuman->isEmpty())
        <div class="text-center py-5 text-muted">
            <i class="bi bi-inbox fs-1 d-block mb-2 opacity-50"></i>
            <p class="mb-0">Belum ada pengumuman yang dibuat.</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Isi Singkat</th>
                        <th>Status</th>
                        <th>Tanggal Dibuat</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pengumuman as $p)
                        <tr>
                            <td class="fw-semibold">{{ $p->judul }}</td>
                            <td>
                                {{ \Illuminate\Support\Str::limit($p->isi, 50) }}
                                @if($p->lampiran)
                                    <br><span class="badge bg-info mt-1"><i class="bi bi-image"></i> Ada Lampiran</span>
                                @endif
                            </td>
                            <td>
                                @if($p->is_aktif)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Nonaktif</span>
                                @endif
                            </td>
                            <td><small class="text-muted">{{ $p->created_at->translatedFormat('d M Y, H:i') }}</small></td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#modalEditPengumuman-{{ $p->id }}">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                
                                <!-- Modal Edit Pengumuman -->
                                <div class="modal fade text-start" id="modalEditPengumuman-{{ $p->id }}" tabindex="-1" aria-labelledby="modalEditPengumumanLabel-{{ $p->id }}" aria-hidden="true">
                                  <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content border-0 rounded-4 shadow-lg">
                                      <div class="modal-header border-bottom-0 pb-0">
                                        <h5 class="modal-title fw-bold" id="modalEditPengumumanLabel-{{ $p->id }}"><i class="bi bi-pencil-square text-primary me-2"></i>Edit Pengumuman</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                      </div>
                                      <div class="modal-body pt-3">
                                        <form action="{{ route('pengurus.pengumuman.update', $p->id) }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Judul Pengumuman</label>
                                                <input type="text" name="judul" class="form-control" value="{{ old('judul', $p->judul) }}" required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Isi Pengumuman</label>
                                                <textarea name="isi" class="form-control" rows="5" required>{{ old('isi', $p->isi) }}</textarea>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Lampiran Foto/Pamflet (Opsional)</label>
                                                @if($p->lampiran)
                                                    <div class="mb-2">
                                                        <img src="{{ asset('storage/' . $p->lampiran) }}" alt="Lampiran" class="img-thumbnail img-popup" style="max-height: 200px;">
                                                    </div>
                                                @endif
                                                <input type="file" name="lampiran" class="form-control" accept="image/*">
                                                <small class="text-muted">Maksimal ukuran 2MB (Format: JPG, PNG, GIF). Biarkan kosong jika tidak ingin mengubah lampiran.</small>
                                            </div>

                                            <div class="mb-4 form-check form-switch">
                                                <input class="form-check-input" type="checkbox" role="switch" name="is_aktif" id="isAktifSwitch-{{ $p->id }}" value="1" {{ $p->is_aktif ? 'checked' : '' }}>
                                                <label class="form-check-label ms-2" for="isAktifSwitch-{{ $p->id }}">Aktifkan Pengumuman</label>
                                            </div>

                                            <div class="d-flex justify-content-end">
                                                <button type="button" class="btn btn-light me-2 rounded-pill" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary rounded-pill px-4"><i class="bi bi-save me-1"></i> Simpan Perubahan</button>
                                            </div>
                                        </form>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                <form action="{{ route('pengurus.pengumuman.destroy', $p->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengumuman ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<!-- Modal Tambah Pengumuman -->
<div class="modal fade" id="modalTambahPengumuman" tabindex="-1" aria-labelledby="modalTambahPengumumanLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 rounded-4 shadow-lg">
      <div class="modal-header border-bottom-0 pb-0">
        <h5 class="modal-title fw-bold" id="modalTambahPengumumanLabel"><i class="bi bi-megaphone-fill text-primary me-2"></i>Buat Pengumuman Baru</h5>
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

        <form action="{{ route('pengurus.pengumuman.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold">Judul Pengumuman</label>
                <input type="text" name="judul" class="form-control" placeholder="Masukkan judul..." required value="{{ old('judul') }}">
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Isi Pengumuman</label>
                <textarea name="isi" class="form-control" rows="5" placeholder="Tuliskan isi pengumuman secara detail..." required>{{ old('isi') }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Lampiran Foto/Pamflet (Opsional)</label>
                <input type="file" name="lampiran" class="form-control" accept="image/*">
                <small class="text-muted">Maksimal ukuran 2MB (Format: JPG, PNG, GIF)</small>
            </div>

            <div class="mb-4 form-check form-switch">
                <input class="form-check-input" type="checkbox" role="switch" name="is_aktif" id="isAktifSwitch" value="1" checked>
                <label class="form-check-label ms-2" for="isAktifSwitch">Aktifkan Pengumuman (akan langsung terlihat oleh anggota)</label>
            </div>

            <div class="d-flex justify-content-end">
                <button type="button" class="btn btn-light me-2 rounded-pill" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4"><i class="bi bi-send me-1"></i> Terbitkan</button>
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
            var myModal = new bootstrap.Modal(document.getElementById('modalTambahPengumuman'));
            myModal.show();
        @endif
    });
</script>
@endsection
