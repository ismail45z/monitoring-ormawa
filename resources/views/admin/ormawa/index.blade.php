@extends('layouts.app')
@section('title', 'Kelola Ormawa')

@section('content')
<div class="card card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="bi bi-diagram-3-fill text-primary me-2"></i>Kelola Data Ormawa</h4>
        <button type="button" class="btn btn-gradient-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#modalTambahOrmawa"><i class="bi bi-plus-circle me-1"></i> Tambah Ormawa</button>
    </div>

    <div class="table-responsive">
        <table class="table align-middle datatable">
            <thead>
                <tr>
                    <th>Nama Ormawa</th>
                    <th>Jenis</th>
                    <th>Periode</th>
                    <th>Ketua</th>
                    <th>Pembina</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ormawas as $ormawa)
                    <tr>
                        <td><span class="fw-semibold">{{ $ormawa->nama_ormawa }}</span></td>
                        <td><span class="badge bg-info text-dark">{{ $ormawa->jenis }}</span></td>
                        <td>{{ $ormawa->periode }}</td>
                        <td>{{ $ormawa->ketua }}</td>
                        <td>{{ $ormawa->pembina ?? '-' }}</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#modalEditOrmawa-{{ $ormawa->id }}"><i class="bi bi-pencil-square"></i></button>
                            
                            <!-- Modal Edit Ormawa -->
                            <div class="modal fade text-start" id="modalEditOrmawa-{{ $ormawa->id }}" tabindex="-1" aria-labelledby="modalEditOrmawaLabel-{{ $ormawa->id }}" aria-hidden="true">
                              <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 rounded-4 shadow-lg">
                                  <div class="modal-header border-bottom-0 pb-0">
                                    <h5 class="modal-title fw-bold" id="modalEditOrmawaLabel-{{ $ormawa->id }}"><i class="bi bi-pencil-square text-primary me-2"></i>Edit Data Ormawa</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                  </div>
                                  <div class="modal-body pt-3">
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

                                        <hr>
                                        <h6 class="fw-bold mb-3"><i class="bi bi-shield-lock text-warning me-2"></i>Batasi Pendaftaran (Opsional)</h6>
                                        <div class="alert alert-info small py-2 px-3 border-0 bg-info bg-opacity-10 text-info">
                                            Kosongkan jika Ormawa ini (seperti UKM/BEM) terbuka untuk semua mahasiswa.
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Batasi untuk Jurusan (HMJ)</label>
                                            <select name="kategori_jurusan" id="edit_jurusan_{{ $ormawa->id }}" class="form-select">
                                                <option value="">Semua Jurusan</option>
                                                @foreach($jurusans as $jurusan)
                                                    <option value="{{ $jurusan->nama }}" {{ old('kategori_jurusan', $ormawa->kategori_jurusan) == $jurusan->nama ? 'selected' : '' }}>{{ $jurusan->nama }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-4">
                                            <label class="form-label">Batasi untuk Program Studi (HMPS)</label>
                                            <select name="kategori_prodi" id="edit_prodi_{{ $ormawa->id }}" class="form-select">
                                                <option value="">Semua Prodi</option>
                                                @if($ormawa->kategori_prodi)
                                                    <option value="{{ $ormawa->kategori_prodi }}" selected>{{ $ormawa->kategori_prodi }}</option>
                                                @endif
                                            </select>
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
                            <form action="{{ route('admin.ormawa.destroy', $ormawa->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus ormawa ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah Ormawa -->
<div class="modal fade" id="modalTambahOrmawa" tabindex="-1" aria-labelledby="modalTambahOrmawaLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 rounded-4 shadow-lg">
      <div class="modal-header border-bottom-0 pb-0">
        <h5 class="modal-title fw-bold" id="modalTambahOrmawaLabel"><i class="bi bi-plus-circle text-primary me-2"></i>Tambah Data Ormawa</h5>
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

        <form action="{{ route('admin.ormawa.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Nama Ormawa</label>
                <input type="text" name="nama_ormawa" class="form-control" placeholder="Contoh: Himpunan Mahasiswa TI" value="{{ old('nama_ormawa') }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Jenis Ormawa</label>
                <select name="jenis" class="form-select" required>
                    <option value="BEM" {{ old('jenis') == 'BEM' ? 'selected' : '' }}>BEM</option>
                    <option value="HMJ" {{ old('jenis') == 'HMJ' ? 'selected' : '' }}>HMJ</option>
                    <option value="UKM" {{ old('jenis') == 'UKM' ? 'selected' : '' }}>UKM</option>
                    <option value="MPM" {{ old('jenis') == 'MPM' ? 'selected' : '' }}>MPM</option>
                    <option value="Independen" {{ old('jenis') == 'Independen' ? 'selected' : '' }}>Independen</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Periode Kepengurusan</label>
                <input type="text" name="periode" class="form-control" placeholder="Contoh: 2025/2026" value="{{ old('periode') }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Ketua Umum</label>
                <input type="text" name="ketua" class="form-control" placeholder="Nama Ketua" value="{{ old('ketua') }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Dosen Pembina</label>
                <input type="text" name="pembina" class="form-control" placeholder="Nama Pembina" value="{{ old('pembina') }}">
            </div>
            <div class="mb-4">
                <label class="form-label">Deskripsi Singkat</label>
                <textarea name="deskripsi" class="form-control" rows="3" placeholder="Deskripsi organisasi...">{{ old('deskripsi') }}</textarea>
            </div>

            <hr>
            <h6 class="fw-bold mb-3"><i class="bi bi-shield-lock text-warning me-2"></i>Batasi Pendaftaran (Opsional)</h6>
            <div class="alert alert-info small py-2 px-3 border-0 bg-info bg-opacity-10 text-info">
                Kosongkan jika Ormawa ini (seperti UKM/BEM) terbuka untuk semua mahasiswa.
            </div>
            <div class="mb-3">
                <label class="form-label">Batasi untuk Jurusan (HMJ)</label>
                <select name="kategori_jurusan" id="add_jurusan" class="form-select">
                    <option value="">Semua Jurusan</option>
                    @foreach($jurusans as $jurusan)
                        <option value="{{ $jurusan->nama }}" {{ old('kategori_jurusan') == $jurusan->nama ? 'selected' : '' }}>{{ $jurusan->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="form-label">Batasi untuk Program Studi (HMPS)</label>
                <select name="kategori_prodi" id="add_prodi" class="form-select">
                    <option value="">Semua Prodi</option>
                </select>
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
        // Auto-open modal if validation errors exist
        @if($errors->any())
            var myModal = new bootstrap.Modal(document.getElementById('modalTambahOrmawa'));
            myModal.show();
        @endif

        function setupCascadingDropdown(jurusanId, prodiId, currentProdiVal = '') {
            const jurusanSelect = document.getElementById(jurusanId);
            const prodiSelect = document.getElementById(prodiId);
            
            if (jurusanSelect && prodiSelect) {
                jurusanSelect.addEventListener('change', function() {
                    const jurusan = this.value;
                    prodiSelect.innerHTML = '<option value="">Semua Prodi (Memuat...)</option>';
                    
                    if(jurusan) {
                        fetch(`/api/prodi?jurusan=${encodeURIComponent(jurusan)}`)
                            .then(response => response.json())
                            .then(data => {
                                prodiSelect.innerHTML = '<option value="">Semua Prodi</option>';
                                data.forEach(prodi => {
                                    const option = document.createElement('option');
                                    option.value = prodi;
                                    option.textContent = prodi;
                                    if(prodi === currentProdiVal) {
                                        option.selected = true;
                                    }
                                    prodiSelect.appendChild(option);
                                });
                            });
                    } else {
                        prodiSelect.innerHTML = '<option value="">Semua Prodi</option>';
                    }
                });

                // Trigger change to load if there's an existing value
                if(jurusanSelect.value && !currentProdiVal) {
                    jurusanSelect.dispatchEvent(new Event('change'));
                } else if(jurusanSelect.value && currentProdiVal) {
                    // Re-trigger fetch but pass currentProdiVal to keep it selected
                    jurusanSelect.dispatchEvent(new Event('change'));
                }
            }
        }

        // Setup Add Modal
        setupCascadingDropdown('add_jurusan', 'add_prodi');

        // Setup Edit Modals
        @foreach($ormawas as $ormawa)
            setupCascadingDropdown('edit_jurusan_{{ $ormawa->id }}', 'edit_prodi_{{ $ormawa->id }}', '{{ $ormawa->kategori_prodi }}');
        @endforeach
    });
</script>
@endsection
