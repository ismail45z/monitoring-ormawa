@extends('layouts.app')
@section('title', 'Catat Kehadiran')

@section('content')
<div class="card card-custom p-4" style="max-width: 600px;">
    <h4 class="fw-bold mb-3"><i class="bi bi-pencil-square text-primary me-2"></i>Catat Kehadiran Mandiri</h4>
    <p class="text-muted small">Mencatat kehadiran Anda untuk kegiatan <strong>{{ $kegiatan->nama_kegiatan }}</strong>.</p>

    <div class="alert alert-info py-2 small">
        <i class="bi bi-info-circle-fill me-1"></i> Kehadiran yang dicatat akan berstatus <strong>PENDING</strong> sampai disetujui oleh Pengurus Ormawa.
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('mahasiswa.kehadiran.catat.store', $kegiatan->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label class="form-label small text-muted">Nama Kegiatan</label>
            <input type="text" class="form-control" value="{{ $kegiatan->nama_kegiatan }}" disabled>
        </div>
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
            <small class="text-muted">Wajib dilampirkan (Foto kehadiran jika Hadir, atau foto surat keterangan/bukti jika Izin).</small>
        </div>
        
        <div class="mb-4">
            <label class="form-label">Keterangan Tambahan (Opsional)</label>
            <textarea name="keterangan" class="form-control" rows="3" placeholder="Tulis alasan jika Izin / Tidak Hadir, atau catatan lain...">{{ old('keterangan') }}</textarea>
        </div>

        <div class="d-flex justify-content-end">
            <a href="{{ route('mahasiswa.kegiatan.index') }}" class="btn btn-light me-2">Batal</a>
            <button type="submit" class="btn btn-primary btn-gradient-primary">Kirim Kehadiran</button>
        </div>
    </form>
</div>
@endsection
