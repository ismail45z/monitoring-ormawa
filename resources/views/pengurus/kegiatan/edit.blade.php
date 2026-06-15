@extends('layouts.app')
@section('title', 'Edit Kegiatan')

@section('content')
<div class="card card-custom p-4" style="max-width: 600px;">
    <h4 class="fw-bold mb-4"><i class="bi bi-pencil-square text-primary me-2"></i>Edit Kegiatan</h4>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('pengurus.kegiatan.update', $kegiatan->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="mb-3">
            <label class="form-label">Nama Kegiatan</label>
            <input type="text" name="nama_kegiatan" class="form-control" value="{{ old('nama_kegiatan', $kegiatan->nama_kegiatan) }}" required>
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
            <a href="{{ route('pengurus.kegiatan.index') }}" class="btn btn-light me-2">Batal</a>
            <button type="submit" class="btn btn-primary btn-gradient-primary">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection
