@extends('layouts.app')
@section('title', 'Tambah Mahasiswa')

@section('content')
<div class="card card-custom p-4" style="max-width: 700px;">
    <h4 class="fw-bold mb-4"><i class="bi bi-person-plus text-primary me-2"></i>Tambah Mahasiswa KIP-K</h4>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.mahasiswa.store') }}" method="POST">
        @csrf
        
        <h6 class="fw-bold text-uppercase text-muted mb-3 border-bottom pb-2">Informasi Akun Login</h6>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="nama" class="form-control" placeholder="Nama Mahasiswa" value="{{ old('nama') }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" placeholder="mahasiswa@mail.com" value="{{ old('email') }}" required>
            </div>
            <div class="col-md-12 mb-3">
                <label class="form-label">Kata Sandi</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
        </div>

        <h6 class="fw-bold text-uppercase text-muted my-3 border-bottom pb-2">Informasi Akademik & KIP</h6>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">NIM (Nomor Induk Mahasiswa)</label>
                <input type="text" name="nim" class="form-control" placeholder="NIM" value="{{ old('nim') }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Nomor KIP-Kuliah</label>
                <input type="text" name="no_kip" class="form-control" placeholder="KIP-XXXXXXXX" value="{{ old('no_kip') }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Jurusan</label>
                <input type="text" name="jurusan" class="form-control" placeholder="Contoh: Teknologi Informasi" value="{{ old('jurusan') }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Program Studi</label>
                <input type="text" name="prodi" class="form-control" placeholder="Contoh: D3 Teknik Informatika" value="{{ old('prodi') }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Angkatan (Tahun)</label>
                <input type="number" name="angkatan" class="form-control" placeholder="Contoh: 2024" value="{{ old('angkatan', 2024) }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Status KIP-K</label>
                <select name="status_kip" class="form-select" required>
                    <option value="Aktif" {{ old('status_kip') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="Cuti" {{ old('status_kip') == 'Cuti' ? 'selected' : '' }}>Cuti</option>
                    <option value="Diberhentikan" {{ old('status_kip') == 'Diberhentikan' ? 'selected' : '' }}>Diberhentikan</option>
                </select>
            </div>
            <div class="col-md-12 mb-4">
                <label class="form-label">Organisasi Mahasiswa (Ormawa)</label>
                <select name="ormawa_id" class="form-select">
                    <option value="">-- Tidak Mengikuti / Pilih Ormawa --</option>
                    @foreach($ormawas as $orm)
                        <option value="{{ $orm->id }}" {{ old('ormawa_id') == $orm->id ? 'selected' : '' }}>{{ $orm->nama_ormawa }}</option>
                    @endforeach
                </select>
                <small class="text-muted">Menghubungkan mahasiswa dengan Ormawa yang diikuti agar dapat mencatat kehadiran di kegiatannya.</small>
            </div>
        </div>

        <div class="d-flex justify-content-end">
            <a href="{{ route('admin.mahasiswa.index') }}" class="btn btn-light me-2">Batal</a>
            <button type="submit" class="btn btn-primary btn-gradient-primary">Simpan</button>
        </div>
    </form>
</div>
@endsection
