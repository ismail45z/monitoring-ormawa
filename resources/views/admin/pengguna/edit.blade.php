@extends('layouts.app')
@section('title', 'Edit Pengguna')

@section('content')
<div class="card card-custom p-4" style="max-width: 600px;">
    <h4 class="fw-bold mb-4"><i class="bi bi-pencil-square text-primary me-2"></i>Edit Akun Pengguna</h4>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.pengguna.update', $pengguna->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="mb-3">
            <label class="form-label">Nama Lengkap</label>
            <input type="text" name="nama" class="form-control" value="{{ old('nama', $pengguna->nama) }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Alamat Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email', $pengguna->email) }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Kata Sandi (Kosongkan jika tidak diubah)</label>
            <input type="password" name="password" class="form-control" placeholder="••••••••">
        </div>
        <div class="mb-3">
            <label class="form-label">Peran (Role)</label>
            <select name="role" id="roleSelect" class="form-select" required>
                <option value="admin" {{ old('role', $pengguna->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="pengurus_ormawa" {{ old('role', $pengguna->role) == 'pengurus_ormawa' ? 'selected' : '' }}>Pengurus Ormawa</option>
                <option value="mahasiswa_kip" {{ old('role', $pengguna->role) == 'mahasiswa_kip' ? 'selected' : '' }}>Mahasiswa KIP</option>
                <option value="wadir" {{ old('role', $pengguna->role) == 'wadir' ? 'selected' : '' }}>Wadir</option>
            </select>
        </div>
        
        <!-- Ormawa Selection (Only for Pengurus) -->
        <div class="mb-4" id="ormawaContainer" style="display: none;">
            <label class="form-label">Organisasi Mahasiswa (Ormawa)</label>
            <select name="ormawa_id" class="form-select">
                <option value="">-- Pilih Ormawa --</option>
                @foreach($ormawas as $orm)
                    <option value="{{ $orm->id }}" {{ old('ormawa_id', $pengguna->ormawa_id) == $orm->id ? 'selected' : '' }}>{{ $orm->nama_ormawa }}</option>
                @endforeach
            </select>
            <small class="text-muted">Wajib dipilih jika peran adalah Pengurus Ormawa.</small>
        </div>

        <div class="d-flex justify-content-end">
            <a href="{{ route('admin.pengguna.index') }}" class="btn btn-light me-2">Batal</a>
            <button type="submit" class="btn btn-primary btn-gradient-primary">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const roleSelect = document.getElementById('roleSelect');
        const ormawaContainer = document.getElementById('ormawaContainer');

        function toggleOrmawa() {
            if (roleSelect.value === 'pengurus_ormawa') {
                ormawaContainer.style.display = 'block';
            } else {
                ormawaContainer.style.display = 'none';
            }
        }

        roleSelect.addEventListener('change', toggleOrmawa);
        toggleOrmawa(); // Trigger initially
    });
</script>
@endsection
