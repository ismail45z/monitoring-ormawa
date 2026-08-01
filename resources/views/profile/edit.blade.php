@extends('layouts.app')
@section('title', 'Profil Saya')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-12">
        <div class="card card-custom p-4">
            <h4 class="fw-bold mb-4"><i class="bi bi-person-lines-fill text-primary me-2"></i>Pengaturan Profil</h4>

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="text-center mb-4">
                    <div class="position-relative d-inline-block">
                        @if($user->foto)
                            <img src="{{ asset('storage/' . $user->foto) }}" id="fotoPreview" alt="Profile Photo" class="rounded-circle border border-3 border-primary shadow-sm img-popup" style="width: 120px; height: 120px; object-fit: cover;">
                        @else
                            <div id="fotoPlaceholder" class="rounded-circle bg-secondary bg-opacity-25 d-flex align-items-center justify-content-center text-secondary border border-3 border-secondary shadow-sm" style="width: 120px; height: 120px; font-size: 3rem;">
                                <i class="bi bi-person"></i>
                            </div>
                            <img src="" id="fotoPreview" alt="Profile Photo" class="rounded-circle border border-3 border-primary shadow-sm d-none img-popup" style="width: 120px; height: 120px; object-fit: cover;">
                        @endif
                        
                        <label for="foto" class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle p-2 shadow" style="cursor: pointer; transform: translate(10%, 10%); transition: all 0.2s;" onmouseover="this.style.transform='translate(10%, 10%) scale(1.1)'" onmouseout="this.style.transform='translate(10%, 10%) scale(1)'">
                            <i class="bi bi-camera"></i>
                        </label>
                        <input type="file" name="foto" id="foto" class="d-none" accept="image/jpeg,image/png,image/jpg,image/gif" onchange="previewImage(this)">
                    </div>
                    <div class="mt-2 small text-muted">Format: JPG, PNG, GIF. Max: 2MB.</div>
                </div>

                <h6 class="text-uppercase text-muted fw-bold mb-3">Informasi Akun Dasar</h6>
                <div class="mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="nama" class="form-control" value="{{ old('nama', $user->nama) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                </div>

                <div class="mb-4">
                    <label class="form-label">Role Akses</label>
                    <input type="text" class="form-control" value="{{ strtoupper(str_replace('_', ' ', $user->role)) }}" disabled>
                    <small class="text-muted">Peran tidak dapat diubah secara mandiri.</small>
                </div>

                <hr class="my-4">
                
                <h6 class="text-uppercase text-muted fw-bold mb-3">Ubah Password <small class="text-lowercase fw-normal">(Opsional)</small></h6>
                <div class="mb-3">
                    <label class="form-label">Password Baru</label>
                    <div class="position-relative">
                        <input type="password" name="password" id="password" class="form-control pe-5" placeholder="Biarkan kosong jika tidak ingin mengubah password">
                        <i class="bi bi-eye position-absolute top-50 end-0 translate-middle-y me-3" style="cursor: pointer; color: #64748b;" onclick="togglePasswordVisibility('password', this)"></i>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label">Konfirmasi Password Baru</label>
                    <div class="position-relative">
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control pe-5" placeholder="Ketik ulang password baru">
                        <i class="bi bi-eye position-absolute top-50 end-0 translate-middle-y me-3" style="cursor: pointer; color: #64748b;" onclick="togglePasswordVisibility('password_confirmation', this)"></i>
                    </div>
                </div>

                @if($user->role === 'mahasiswa_kip' && $user->mahasiswa)
                    <hr class="my-4">
                    <h6 class="text-uppercase text-muted fw-bold mb-3">Informasi Mahasiswa KIP-K</h6>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">NIM (Nomor Induk Mahasiswa)</label>
                            <input type="text" name="nim" class="form-control" value="{{ old('nim', $user->mahasiswa->nim) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nomor KIP-Kuliah</label>
                            <input type="text" name="no_kip" class="form-control" value="{{ old('no_kip', $user->mahasiswa->no_kip) }}" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jurusan</label>
                            <input type="text" class="form-control bg-light" value="{{ $user->mahasiswa->jurusan }}" disabled readonly>
                            <input type="hidden" name="jurusan" value="{{ $user->mahasiswa->jurusan }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Program Studi</label>
                            <input type="text" class="form-control bg-light" value="{{ $user->mahasiswa->prodi }}" disabled readonly>
                            <input type="hidden" name="prodi" value="{{ $user->mahasiswa->prodi }}">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Tahun Angkatan</label>
                        <input type="number" name="angkatan" class="form-control" value="{{ old('angkatan', $user->mahasiswa->angkatan) }}" required min="2000" max="{{ date('Y') + 1 }}">
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Bukti Kartu KIP-Kuliah</label>
                        @if($user->mahasiswa->bukti_kip)
                            <div class="mb-2">
                                <img src="{{ route('secure.file', ['path' => $user->mahasiswa->bukti_kip]) }}" alt="Bukti KIP" class="img-thumbnail shadow-sm img-popup" style="max-height: 200px; object-fit: contain;">
                            </div>
                        @endif
                        <input type="file" name="bukti_kip" class="form-control" accept="image/jpeg,image/png,image/jpg,image/gif">
                        <small class="text-muted">Format: JPG, PNG, GIF. Maksimal: 2MB.</small>
                    </div>
                @endif

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary btn-gradient-primary px-4"><i class="bi bi-save me-2"></i>Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        
        reader.onload = function(e) {
            const preview = document.getElementById('fotoPreview');
            const placeholder = document.getElementById('fotoPlaceholder');
            
            if(placeholder) {
                placeholder.classList.add('d-none');
                preview.classList.remove('d-none');
            }
            
            preview.src = e.target.result;
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}

function togglePasswordVisibility(inputId, iconElement) {
    const input = document.getElementById(inputId);
    if (input.type === 'password') {
        input.type = 'text';
        iconElement.classList.remove('bi-eye');
        iconElement.classList.add('bi-eye-slash');
    } else {
        input.type = 'password';
        iconElement.classList.remove('bi-eye-slash');
        iconElement.classList.add('bi-eye');
    }
}
</script>
@endsection
