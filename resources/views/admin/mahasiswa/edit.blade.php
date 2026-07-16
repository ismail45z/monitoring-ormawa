@extends('layouts.app')
@section('title', 'Edit Mahasiswa')

@section('content')
<div class="card card-custom p-4" style="max-width: 700px;">
    <h4 class="fw-bold mb-4"><i class="bi bi-pencil-square text-primary me-2"></i>Edit Mahasiswa KIP-K</h4>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.mahasiswa.update', $mahasiswa->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <h6 class="fw-bold text-uppercase text-muted mb-3 border-bottom pb-2">Informasi Akun Login</h6>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="nama" class="form-control" value="{{ old('nama', $mahasiswa->pengguna->nama) }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $mahasiswa->pengguna->email) }}" required>
            </div>
            <div class="col-md-12 mb-3">
                <label class="form-label">Kata Sandi (Kosongkan jika tidak diubah)</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••">
            </div>
        </div>

        <h6 class="fw-bold text-uppercase text-muted my-3 border-bottom pb-2">Informasi Akademik & KIP</h6>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">NIM (Nomor Induk Mahasiswa)</label>
                <input type="text" name="nim" class="form-control" value="{{ old('nim', $mahasiswa->nim) }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Nomor KIP-Kuliah</label>
                <input type="text" name="no_kip" class="form-control" value="{{ old('no_kip', $mahasiswa->no_kip) }}" required maxlength="6" minlength="6" pattern="\d{6}" title="Nomor KIP-Kuliah harus berupa 6 digit angka">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Jurusan</label>
                <input type="text" name="jurusan" class="form-control" value="{{ old('jurusan', $mahasiswa->jurusan) }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Program Studi</label>
                <input type="text" name="prodi" class="form-control" value="{{ old('prodi', $mahasiswa->prodi) }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Angkatan (Tahun)</label>
                <input type="number" name="angkatan" class="form-control" value="{{ old('angkatan', $mahasiswa->angkatan) }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Status KIP-K</label>
                <select name="status_kip" class="form-select" required>
                    <option value="Aktif" {{ old('status_kip', $mahasiswa->status_kip) == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="Cuti" {{ old('status_kip', $mahasiswa->status_kip) == 'Cuti' ? 'selected' : '' }}>Cuti</option>
                    <option value="Diberhentikan" {{ old('status_kip', $mahasiswa->status_kip) == 'Diberhentikan' ? 'selected' : '' }}>Diberhentikan</option>
                </select>
            </div>
            <div class="col-md-12 mb-4">
                <label class="form-label fw-semibold">Organisasi Mahasiswa (Ormawa) <span class="text-muted fw-normal">(Dapat memilih lebih dari satu)</span></label>
                <div class="border rounded p-3 bg-light" style="max-height: 200px; overflow-y: auto;">
                    @php $currentOrmawa = old('ormawa_ids', $mahasiswa->ormawas->pluck('id')->toArray()); @endphp
                    @foreach($ormawas as $orm)
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="checkbox" name="ormawa_ids[]" id="ormawa_{{ $orm->id }}" value="{{ $orm->id }}"
                                {{ in_array($orm->id, $currentOrmawa) ? 'checked' : '' }}>
                            <label class="form-check-label" for="ormawa_{{ $orm->id }}">
                                {{ $orm->nama_ormawa }}
                                @if($orm->jenis)
                                    <small class="text-muted">({{ $orm->jenis }})</small>
                                @endif
                            </label>
                        </div>
                    @endforeach
                </div>
                <small class="text-muted">Centang semua Ormawa yang diikuti mahasiswa ini. Keaktifan dihitung terpisah per Ormawa.</small>
            </div>
        </div>

        <div class="d-flex justify-content-end">
            <a href="{{ route('admin.mahasiswa.index') }}" class="btn btn-light me-2">Batal</a>
            <button type="submit" class="btn btn-primary btn-gradient-primary">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection
