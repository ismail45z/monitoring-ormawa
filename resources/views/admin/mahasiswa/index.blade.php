@extends('layouts.app')
@section('title', 'Kelola Mahasiswa')

@section('content')
<div class="card card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="bi bi-mortarboard-fill text-primary me-2"></i>Kelola Data Mahasiswa KIP-K</h4>
        <button type="button" class="btn btn-gradient-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#modalTambahMahasiswa"><i class="bi bi-plus-circle me-1"></i> Tambah Mahasiswa</button>
    </div>

    <!-- Search Form -->
    <form action="{{ route('admin.mahasiswa.index') }}" method="GET" class="mb-4">
        <div class="input-group" style="max-width: 400px;">
            <input type="text" name="search" class="form-control" placeholder="Cari NIM, Nama, atau Prodi..." value="{{ request('search') }}">
            <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
            @if(request('search'))
                <a href="{{ route('admin.mahasiswa.index') }}" class="btn btn-outline-secondary">Reset</a>
            @endif
        </div>
    </form>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>NIM</th>
                    <th>No. KIP</th>
                    <th>Prodi (Jurusan)</th>
                    <th>Angkatan</th>
                    <th>Ormawa</th>
                    <th>KIP Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $student)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $student->pengguna->nama }}</div>
                            <small class="text-muted">{{ $student->pengguna->email }}</small>
                        </td>
                        <td>{{ $student->nim }}</td>
                        <td>{{ $student->no_kip }}</td>
                        <td>{{ $student->prodi }} ({{ $student->jurusan }})</td>
                        <td>{{ $student->angkatan }}</td>
                        <td>
                            @forelse($student->ormawas as $orm)
                                <span class="badge bg-primary me-1 mb-1">{{ $orm->nama_ormawa }}</span>
                            @empty
                                <span class="text-muted small">-</span>
                            @endforelse
                        </td>
                        <td>
                            <span class="badge bg-success">{{ $student->status_kip }}</span>
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#modalEditMahasiswa-{{ $student->id }}"><i class="bi bi-pencil-square"></i></button>
                            
                            <!-- Modal Edit Mahasiswa -->
                            <div class="modal fade text-start" id="modalEditMahasiswa-{{ $student->id }}" tabindex="-1" aria-labelledby="modalEditMahasiswaLabel-{{ $student->id }}" aria-hidden="true">
                              <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content border-0 rounded-4 shadow-lg">
                                  <div class="modal-header border-bottom-0 pb-0">
                                    <h5 class="modal-title fw-bold" id="modalEditMahasiswaLabel-{{ $student->id }}"><i class="bi bi-pencil-square text-primary me-2"></i>Edit Mahasiswa KIP-K</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                  </div>
                                  <div class="modal-body pt-3">
                                    <form action="{{ route('admin.mahasiswa.update', $student->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        
                                        <h6 class="fw-bold text-uppercase text-muted mb-3 border-bottom pb-2">Informasi Akun Login</h6>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Nama Lengkap</label>
                                                <input type="text" name="nama" class="form-control" value="{{ old('nama', $student->pengguna->nama) }}" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Email</label>
                                                <input type="email" name="email" class="form-control" value="{{ old('email', $student->pengguna->email) }}" required>
                                            </div>
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label">Kata Sandi <small class="text-muted">(Kosongkan jika tidak diubah)</small></label>
                                                <div class="input-group">
                                                    <input type="password" name="password" id="password-edit-{{ $student->id }}" class="form-control" placeholder="••••••••">
                                                    <button class="btn btn-outline-secondary toggle-password" type="button" data-target="password-edit-{{ $student->id }}">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <h6 class="fw-bold text-uppercase text-muted my-3 border-bottom pb-2">Informasi Akademik & KIP</h6>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">NIM (Nomor Induk Mahasiswa)</label>
                                                <input type="text" name="nim" class="form-control" value="{{ old('nim', $student->nim) }}" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Nomor KIP-Kuliah</label>
                                                <input type="text" name="no_kip" class="form-control" value="{{ old('no_kip', $student->no_kip) }}" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Jurusan</label>
                                                <select name="jurusan" class="form-select jurusan-select" data-target="prodi-edit-{{ $student->id }}" required>
                                                    <option value="">Pilih Jurusan</option>
                                                    @foreach($jurusans as $jurusan)
                                                        <option value="{{ $jurusan->nama }}" {{ old('jurusan', $student->jurusan) == $jurusan->nama ? 'selected' : '' }}>{{ $jurusan->nama }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Program Studi</label>
                                                <select name="prodi" id="prodi-edit-{{ $student->id }}" class="form-select" data-old="{{ old('prodi', $student->prodi) }}" required>
                                                    <option value="">Pilih Prodi</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Angkatan (Tahun)</label>
                                                <input type="number" name="angkatan" class="form-control" value="{{ old('angkatan', $student->angkatan) }}" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Status KIP-K</label>
                                                <select name="status_kip" class="form-select" required>
                                                    <option value="Aktif" {{ old('status_kip', $student->status_kip) == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                                    <option value="Cuti" {{ old('status_kip', $student->status_kip) == 'Cuti' ? 'selected' : '' }}>Cuti</option>
                                                    <option value="Diberhentikan" {{ old('status_kip', $student->status_kip) == 'Diberhentikan' ? 'selected' : '' }}>Diberhentikan</option>
                                                </select>
                                            </div>
                                            <div class="col-md-12 mb-4">
                                                <label class="form-label fw-semibold">Organisasi Mahasiswa (Ormawa)</label>
                                                <div class="border rounded p-3 bg-light" style="max-height: 200px; overflow-y: auto;">
                                                    @php $currentOrmawa = old('ormawa_ids', $student->ormawas->pluck('id')->toArray()); @endphp
                                                    @foreach($ormawas as $orm)
                                                        <div class="form-check mb-1">
                                                            <input class="form-check-input" type="checkbox" name="ormawa_ids[]" id="ormawa_{{ $student->id }}_{{ $orm->id }}" value="{{ $orm->id }}"
                                                                {{ in_array($orm->id, $currentOrmawa) ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="ormawa_{{ $student->id }}_{{ $orm->id }}">
                                                                {{ $orm->nama_ormawa }}
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
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
                            <form action="{{ route('admin.mahasiswa.destroy', $student->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data mahasiswa ini? Akun pengguna yang terkait juga akan terhapus.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">Tidak ada data mahasiswa ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-end mt-3">
        {{ $students->links('pagination::bootstrap-5') }}
    </div>
</div>

<!-- Modal Tambah Mahasiswa -->
<div class="modal fade" id="modalTambahMahasiswa" tabindex="-1" aria-labelledby="modalTambahMahasiswaLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 rounded-4 shadow-lg">
      <div class="modal-header border-bottom-0 pb-0">
        <h5 class="modal-title fw-bold" id="modalTambahMahasiswaLabel"><i class="bi bi-person-plus text-primary me-2"></i>Tambah Mahasiswa KIP-K</h5>
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
                    <div class="input-group">
                        <input type="password" name="password" id="password-create" class="form-control" placeholder="••••••••" required>
                        <button class="btn btn-outline-secondary toggle-password" type="button" data-target="password-create">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
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
                    <input type="text" name="no_kip" class="form-control" placeholder="Contoh: KIP123456" value="{{ old('no_kip') }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Jurusan</label>
                    <select name="jurusan" class="form-select jurusan-select" data-target="prodi-create" required>
                        <option value="">Pilih Jurusan</option>
                        @foreach($jurusans as $jurusan)
                            <option value="{{ $jurusan->nama }}" {{ old('jurusan') == $jurusan->nama ? 'selected' : '' }}>{{ $jurusan->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Program Studi</label>
                    <select name="prodi" id="prodi-create" class="form-select" data-old="{{ old('prodi') }}" required>
                        <option value="">Pilih Prodi</option>
                    </select>
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
                    <label class="form-label fw-semibold">Organisasi Mahasiswa (Ormawa)</label>
                    <div class="border rounded p-3 bg-light" style="max-height: 200px; overflow-y: auto;">
                        @foreach($ormawas as $orm)
                            <div class="form-check mb-1">
                                <input class="form-check-input" type="checkbox" name="ormawa_ids[]" id="ormawa_create_{{ $orm->id }}" value="{{ $orm->id }}"
                                    {{ in_array($orm->id, old('ormawa_ids', [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="ormawa_create_{{ $orm->id }}">
                                    {{ $orm->nama_ormawa }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary btn-gradient-primary">Simpan</button>
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
        // Toggle password visibility
        document.addEventListener('click', function (e) {
            if (e.target && e.target.closest('.toggle-password')) {
                const btn = e.target.closest('.toggle-password');
                const targetId = btn.getAttribute('data-target');
                const password = document.getElementById(targetId);
                if (password) {
                    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                    password.setAttribute('type', type);
                    const icon = btn.querySelector('i');
                    if (icon) {
                        icon.classList.toggle('bi-eye');
                        icon.classList.toggle('bi-eye-slash');
                    }
                }
            }
        });

        // Auto-open create modal if validation errors exist
        @if($errors->any())
            var myModal = new bootstrap.Modal(document.getElementById('modalTambahMahasiswa'));
            myModal.show();
        @endif

        // Handle cascading dropdowns for Jurusan -> Prodi
        document.querySelectorAll('.jurusan-select').forEach(function(select) {
            select.addEventListener('change', function() {
                const jurusan = this.value;
                const targetId = this.getAttribute('data-target');
                const prodiSelect = document.getElementById(targetId);
                
                if (!prodiSelect) return;
                
                prodiSelect.innerHTML = '<option value="">Memuat...</option>';
                
                if(jurusan) {
                    fetch(`/api/prodi?jurusan=${encodeURIComponent(jurusan)}`)
                        .then(response => response.json())
                        .then(data => {
                            prodiSelect.innerHTML = '<option value="">Pilih Prodi</option>';
                            data.forEach(prodi => {
                                const option = document.createElement('option');
                                option.value = prodi;
                                option.textContent = prodi;
                                prodiSelect.appendChild(option);
                            });
                            
                            const oldProdi = prodiSelect.getAttribute('data-old');
                            if(oldProdi && data.includes(oldProdi)) {
                                prodiSelect.value = oldProdi;
                            }
                        });
                } else {
                    prodiSelect.innerHTML = '<option value="">Pilih Prodi</option>';
                }
            });

            // Trigger change on load if already selected
            if(select.value) {
                select.dispatchEvent(new Event('change'));
            }
        });
    });
</script>
@endsection
