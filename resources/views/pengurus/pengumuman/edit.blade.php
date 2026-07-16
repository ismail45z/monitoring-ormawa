@extends('layouts.app')
@section('title', 'Edit Pengumuman')

@section('content')
<div class="d-flex align-items-center mb-4 gap-3">
    <a href="{{ route('pengurus.pengumuman.index') }}" class="btn btn-light rounded-pill">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
    <div>
        <h4 class="fw-bold mb-0">Edit Pengumuman</h4>
        <p class="text-muted mb-0">Ubah informasi pengumuman</p>
    </div>
</div>

<div class="card card-custom p-4">
    <form action="{{ route('pengurus.pengumuman.update', $pengumuman->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label fw-semibold">Judul Pengumuman</label>
            <input type="text" name="judul" class="form-control" value="{{ old('judul', $pengumuman->judul) }}" required>
            @error('judul') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Isi Pengumuman</label>
            <textarea name="isi" class="form-control" rows="5" required>{{ old('isi', $pengumuman->isi) }}</textarea>
            @error('isi') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Lampiran Foto/Pamflet (Opsional)</label>
            @if($pengumuman->lampiran)
                <div class="mb-2">
                    <img src="{{ asset('storage/' . $pengumuman->lampiran) }}" alt="Lampiran" class="img-thumbnail" style="max-height: 200px;">
                </div>
            @endif
            <input type="file" name="lampiran" class="form-control" accept="image/*">
            <small class="text-muted">Maksimal ukuran 2MB (Format: JPG, PNG, GIF). Biarkan kosong jika tidak ingin mengubah lampiran.</small>
            @error('lampiran') <br><small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-4 form-check form-switch">
            <input class="form-check-input" type="checkbox" role="switch" name="is_aktif" id="isAktifSwitch" value="1" {{ $pengumuman->is_aktif ? 'checked' : '' }}>
            <label class="form-check-label ms-2" for="isAktifSwitch">Aktifkan Pengumuman (akan langsung terlihat oleh anggota)</label>
        </div>

        <button type="submit" class="btn btn-primary rounded-pill px-4">
            <i class="bi bi-save me-1"></i> Simpan Perubahan
        </button>
    </form>
</div>
@endsection
