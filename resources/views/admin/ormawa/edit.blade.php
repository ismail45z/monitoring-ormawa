@extends('layouts.app')
@section('title', 'Edit Ormawa')

@section('content')
<div class="card card-custom p-4" style="max-width: 600px;">
    <h4 class="fw-bold mb-4"><i class="bi bi-pencil-square text-primary me-2"></i>Edit Data Ormawa</h4>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.ormawa.update', $ormawa->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="mb-3">
            <label class="form-label">Nama Ormawa</label>
            <input type="text" name="nama_ormawa" class="form-control" placeholder="Contoh: Himpunan Mahasiswa TI" value="{{ old('nama_ormawa', $ormawa->nama_ormawa) }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Jenis Ormawa</label>
            <select name="jenis" class="form-select" required>
                <option value="BEM" {{ old('jenis', $ormawa->jenis) == 'BEM' ? 'selected' : '' }}>BEM</option>
                <option value="HMJ" {{ old('jenis', $ormawa->jenis) == 'HMJ' ? 'selected' : '' }}>HMJ</option>
                <option value="UKM" {{ old('jenis', $ormawa->jenis) == 'UKM' ? 'selected' : '' }}>UKM</option>
                <option value="MPM" {{ old('jenis', $ormawa->jenis) == 'MPM' ? 'selected' : '' }}>MPM</option>
                <option value="Independen" {{ old('jenis', $ormawa->jenis) == 'Independen' ? 'selected' : '' }}>Independen</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Periode Kepengurusan</label>
            <input type="text" name="periode" class="form-control" placeholder="Contoh: 2025/2026" value="{{ old('periode', $ormawa->periode) }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Ketua Umum</label>
            <input type="text" name="ketua" class="form-control" placeholder="Nama Ketua" value="{{ old('ketua', $ormawa->ketua) }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Dosen Pembina</label>
            <input type="text" name="pembina" class="form-control" placeholder="Nama Pembina" value="{{ old('pembina', $ormawa->pembina) }}">
        </div>
        <div class="mb-4">
            <label class="form-label">Deskripsi Singkat</label>
            <textarea name="deskripsi" class="form-control" rows="3" placeholder="Deskripsi organisasi...">{{ old('deskripsi', $ormawa->deskripsi) }}</textarea>
        </div>

        <div class="d-flex justify-content-end">
            <a href="{{ route('admin.ormawa.index') }}" class="btn btn-light me-2">Batal</a>
            <button type="submit" class="btn btn-primary btn-gradient-primary">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection
