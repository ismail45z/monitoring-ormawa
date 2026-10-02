@extends('layouts.app')
@section('title', 'Kelola Pengguna')

@section('content')
<div class="card card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div class="d-flex align-items-center gap-3">
            <h4 class="fw-bold mb-0"><i class="bi bi-people-fill text-primary me-2"></i>Kelola Akun Pengguna</h4>
            @if($pendingCount > 0)
                <span class="badge bg-warning text-dark fs-6 rounded-pill px-3 py-2">
                    <i class="bi bi-clock-history me-1"></i>{{ $pendingCount }} Menunggu Persetujuan
                </span>
            @endif
        </div>
        <button type="button" class="btn btn-gradient-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#modalTambahPengguna"><i class="bi bi-plus-circle me-1"></i> Tambah Pengguna</button>
    </div>

    <!-- Filter & Search Form -->
    <form action="{{ route('admin.pengguna.index') }}" method="GET" class="mb-4 row g-2">
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">Semua Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>⏳ Menunggu Verifikasi</option>
                <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>✅ Aktif</option>
            </select>
        </div>
        <div class="col-md-3">
            <select name="role" class="form-select">
                <option value="">Semua Role</option>
                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="pengurus_ormawa" {{ request('role') == 'pengurus_ormawa' ? 'selected' : '' }}>Pengurus Ormawa</option>
                <option value="mahasiswa_kip" {{ request('role') == 'mahasiswa_kip' ? 'selected' : '' }}>Mahasiswa KIP</option>
                <option value="wadir" {{ request('role') == 'wadir' ? 'selected' : '' }}>Wadir</option>
            </select>
        </div>
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Cari Nama atau Email..." value="{{ request('search') }}">
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary" type="submit"><i class="bi bi-filter"></i> Filter</button>
            @if(request('search') || request('role') || request('status'))
                <a href="{{ route('admin.pengguna.index') }}" class="btn btn-outline-secondary">Reset</a>
            @endif
        </div>
    </form>

    @if($pendingCount > 0 && !request('status'))
    <div class="alert alert-warning alert-dismissible fade show rounded-3 d-flex align-items-center gap-3 mb-4" role="alert">
        <div class="flex-shrink-0">
            <i class="bi bi-person-fill-exclamation fs-3"></i>
        </div>
        <div class="flex-grow-1">
            <strong>Ada {{ $pendingCount }} pengguna baru menunggu verifikasi!</strong>
            <div class="small mt-1">Mahasiswa atau pengurus ormawa yang baru mendaftar perlu disetujui sebelum dapat menggunakan sistem.</div>
        </div>
        <a href="{{ route('admin.pengguna.index', ['status' => 'pending']) }}" class="btn btn-warning btn-sm rounded-pill px-3 flex-shrink-0">
            <i class="bi bi-shield-check me-1"></i> Verifikasi Sekarang
        </a>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Jurusan / Prodi</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Ormawa</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr class="{{ $user->status_akun === 'pending' ? 'table-warning' : '' }}">
                        <td><span class="fw-semibold">{{ $user->nama }}</span></td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if($user->role === 'mahasiswa_kip' && $user->mahasiswa)
                                <div class="small fw-semibold text-dark">{{ $user->mahasiswa->jurusan ?? '-' }}</div>
                                <div class="small text-muted">{{ $user->mahasiswa->prodi ?? '-' }}</div>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-secondary text-uppercase">{{ str_replace('_', ' ', $user->role) }}</span>
                        </td>
                        <td>
                            @if($user->status_akun === 'pending')
                                <span class="badge bg-warning text-dark"><i class="bi bi-clock-history me-1"></i>Pending</span>
                            @else
                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Aktif</span>
                            @endif
                        </td>
                        <td>
                            @if($user->role === 'pengurus_ormawa' && $user->ormawa)
                                <span class="badge rounded-pill" style="background:rgba(99,102,241,0.15);color:#818cf8;font-weight:500;">{{ $user->ormawa->nama_ormawa }}</span>
                            @elseif($user->role === 'mahasiswa_kip' && $user->mahasiswa && $user->mahasiswa->ormawas->count() > 0)
                                @foreach($user->mahasiswa->ormawas as $orm)
                                    <span class="badge rounded-pill mb-1" style="background:rgba(16,185,129,0.15);color:#10b981;font-weight:500;">{{ $orm->nama_ormawa }}</span>
                                @endforeach
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td>
                            @if($user->status_akun === 'pending')
                                @if($user->role === 'mahasiswa_kip' && $user->mahasiswa)
                                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalVerifikasi-{{ $user->id }}">
                                        <i class="bi bi-shield-check"></i> Verifikasi
                                    </button>
                                    
                                    <!-- Modal Verifikasi -->
                                    <div class="modal fade" id="modalVerifikasi-{{ $user->id }}" tabindex="-1" aria-hidden="true">
                                      <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content text-start border-0 rounded-4 shadow-lg">
                                          <div class="modal-header border-bottom-0 pb-0">
                                            <h5 class="modal-title fw-bold"><i class="bi bi-person-badge text-primary me-2"></i>Verifikasi Mahasiswa KIP-K</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                          </div>
                                          <div class="modal-body pt-3">
                                            <div class="row g-4">
                                                <div class="col-md-6">
                                                    <h6 class="fw-bold border-bottom pb-2 mb-3">Data Pendaftar</h6>
                                                    <table class="table table-sm table-borderless">
                                                        <tr><td width="100" class="text-muted">Nama</td><td class="fw-semibold">: {{ $user->nama }}</td></tr>
                                                        <tr><td class="text-muted">NIM</td><td class="fw-semibold">: {{ $user->mahasiswa->nim }}</td></tr>
                                                        <tr><td class="text-muted">No. KIP</td><td class="fw-semibold">: <span class="badge bg-info text-dark">{{ $user->mahasiswa->no_kip }}</span></td></tr>
                                                        <tr><td class="text-muted">Angkatan</td><td class="fw-semibold">: {{ $user->mahasiswa->angkatan }}</td></tr>
                                                        <tr><td class="text-muted">Jurusan</td><td class="fw-semibold">: {{ $user->mahasiswa->jurusan ?? '-' }}</td></tr>
                                                        <tr><td class="text-muted">Prodi</td><td class="fw-semibold">: {{ $user->mahasiswa->prodi ?? '-' }}</td></tr>
                                                    </table>
                                                    
                                                    <div class="d-flex gap-2 mt-4">
                                                        <form action="{{ route('admin.pengguna.approve', $user->id) }}" method="POST" class="flex-grow-1" onsubmit="return confirm('Aktifkan akun {{ $user->nama }}?')">
                                                            @csrf
                                                            <button type="submit" class="btn btn-success w-100 rounded-pill"><i class="bi bi-check-lg"></i> Setujui Akun</button>
                                                        </form>
                                                        <form action="{{ route('admin.pengguna.reject', $user->id) }}" method="POST" class="flex-grow-1" onsubmit="return confirm('Tolak dan hapus pendaftaran akun ini?')">
                                                            @csrf
                                                            <button type="submit" class="btn btn-outline-danger w-100 rounded-pill"><i class="bi bi-x-lg"></i> Tolak</button>
                                                        </form>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 text-center">
                                                    <h6 class="fw-bold border-bottom pb-2 mb-3">Foto/Dokumen Bukti KIP</h6>
                                                    @if($user->mahasiswa->bukti_kip)
                                                        @if(Str::endsWith(strtolower($user->mahasiswa->bukti_kip), '.pdf'))
                                                            <div class="p-4 bg-light rounded border border-2 border-light mb-2">
                                                                <i class="bi bi-file-earmark-pdf-fill text-danger d-block mb-2" style="font-size: 3rem;"></i>
                                                                <span class="fw-semibold text-muted">Dokumen PDF</span>
                                                            </div>
                                                            <a href="{{ route('secure.file', ['path' => $user->mahasiswa->bukti_kip]) }}" target="_blank" class="btn btn-outline-danger btn-sm rounded-pill px-4">
                                                                <i class="bi bi-download me-1"></i> Buka / Unduh PDF
                                                            </a>
                                                        @else
                                                            <img src="{{ route('secure.file', ['path' => $user->mahasiswa->bukti_kip]) }}" class="img-fluid rounded shadow-sm border border-2 border-light mb-2" alt="Bukti KIP">
                                                            <a href="{{ route('secure.file', ['path' => $user->mahasiswa->bukti_kip]) }}" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill px-4">
                                                                <i class="bi bi-arrows-fullscreen me-1"></i> Perbesar
                                                            </a>
                                                        @endif
                                                    @else
                                                        <div class="p-5 bg-light text-muted rounded"><i class="bi bi-image-fill fs-1 d-block mb-2"></i>Tidak ada dokumen bukti.</div>
                                                    @endif
                                                </div>
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                @else
                                    {{-- Approve Button --}}
                                    <form action="{{ route('admin.pengguna.approve', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Aktifkan akun {{ $user->nama }}?')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success me-1" title="Setujui">
                                            <i class="bi bi-check-lg"></i> Setujui
                                        </button>
                                    </form>
                                    {{-- Reject Button --}}
                                    <form action="{{ route('admin.pengguna.reject', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Tolak dan hapus pendaftaran akun ini?')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Tolak">
                                            <i class="bi bi-x-lg"></i> Tolak
                                        </button>
                                    </form>
                                @endif
                            @else
                                <button type="button" class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#modalEditPengguna-{{ $user->id }}">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                
                                <!-- Modal Edit Pengguna -->
                                <div class="modal fade" id="modalEditPengguna-{{ $user->id }}" tabindex="-1" aria-labelledby="modalEditPenggunaLabel-{{ $user->id }}" aria-hidden="true">
                                  <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content border-0 rounded-4 shadow-lg text-start">
                                      <div class="modal-header border-bottom-0 pb-0">
                                        <h5 class="modal-title fw-bold" id="modalEditPenggunaLabel-{{ $user->id }}"><i class="bi bi-pencil-square text-primary me-2"></i>Edit Akun Pengguna</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                      </div>
                                      <div class="modal-body pt-3">
                                        <form action="{{ route('admin.pengguna.update', $user->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="mb-3">
                                                <label class="form-label">Nama Lengkap</label>
                                                <input type="text" name="nama" class="form-control" value="{{ old('nama', $user->nama) }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Alamat Email</label>
                                                <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Kata Sandi <small class="text-muted">(Kosongkan jika tidak ingin mengubah)</small></label>
                                                <div class="input-group">
                                                    <input type="password" name="password" id="password-{{ $user->id }}" class="form-control">
                                                    <button class="btn btn-outline-secondary toggle-password" type="button" data-target="password-{{ $user->id }}">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Peran (Role)</label>
                                                <select name="role" id="roleSelect-{{ $user->id }}" class="form-select role-select" data-target="ormawaContainer-{{ $user->id }}" required>
                                                    <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                                                    <option value="pengurus_ormawa" {{ old('role', $user->role) == 'pengurus_ormawa' ? 'selected' : '' }}>Pengurus Ormawa</option>
                                                    <option value="mahasiswa_kip" {{ old('role', $user->role) == 'mahasiswa_kip' ? 'selected' : '' }}>Mahasiswa KIP</option>
                                                    <option value="wadir" {{ old('role', $user->role) == 'wadir' ? 'selected' : '' }}>Wadir</option>
                                                </select>
                                            </div>
                                            
                                            <!-- Ormawa Selection (Only for Pengurus) -->
                                            <div class="mb-4" id="ormawaContainer-{{ $user->id }}" style="display: {{ old('role', $user->role) == 'pengurus_ormawa' ? 'block' : 'none' }};">
                                                <label class="form-label">Organisasi Mahasiswa (Ormawa)</label>
                                                <select name="ormawa_id" class="form-select">
                                                    <option value="">-- Pilih Ormawa --</option>
                                                    @foreach($ormawas as $orm)
                                                        <option value="{{ $orm->id }}" {{ old('ormawa_id', $user->ormawa_id) == $orm->id ? 'selected' : '' }}>{{ $orm->nama_ormawa }}</option>
                                                    @endforeach
                                                </select>
                                                <small class="text-muted">Wajib dipilih jika peran adalah Pengurus Ormawa.</small>
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
                                @if(Auth::id() !== $user->id)
                                    <form action="{{ route('admin.pengguna.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                @endif
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">Tidak ada data pengguna ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-end mt-3">
        {{ $users->links('pagination::bootstrap-5') }}
    </div>
</div>

<!-- Modal Tambah Pengguna -->
<div class="modal fade" id="modalTambahPengguna" tabindex="-1" aria-labelledby="modalTambahPenggunaLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 rounded-4 shadow-lg">
      <div class="modal-header border-bottom-0 pb-0">
        <h5 class="modal-title fw-bold" id="modalTambahPenggunaLabel"><i class="bi bi-person-plus text-primary me-2"></i>Tambah Akun Pengguna</h5>
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

        <form action="{{ route('admin.pengguna.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="nama" class="form-control" value="{{ old('nama') }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Alamat Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Kata Sandi</label>
                <div class="input-group">
                    <input type="password" name="password" id="password" class="form-control" required>
                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Peran (Role)</label>
                <select name="role" id="roleSelect" class="form-select" required>
                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="pengurus_ormawa" {{ old('role') == 'pengurus_ormawa' ? 'selected' : '' }}>Pengurus Ormawa</option>
                    <option value="mahasiswa_kip" {{ old('role') == 'mahasiswa_kip' ? 'selected' : '' }}>Mahasiswa KIP</option>
                    <option value="wadir" {{ old('role') == 'wadir' ? 'selected' : '' }}>Wadir</option>
                </select>
            </div>
            
            <!-- Ormawa Selection (Only for Pengurus) -->
            <div class="mb-4" id="ormawaContainer" style="display: none;">
                <label class="form-label">Organisasi Mahasiswa (Ormawa)</label>
                <select name="ormawa_id" class="form-select">
                    <option value="">-- Pilih Ormawa --</option>
                    @foreach($ormawas as $orm)
                        <option value="{{ $orm->id }}" {{ old('ormawa_id') == $orm->id ? 'selected' : '' }}>{{ $orm->nama_ormawa }}</option>
                    @endforeach
                </select>
                <small class="text-muted">Wajib dipilih jika peran adalah Pengurus Ormawa.</small>
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
        // Toggle Ormawa for Create Modal
        const roleSelect = document.getElementById('roleSelect');
        const ormawaContainer = document.getElementById('ormawaContainer');
        if(roleSelect && ormawaContainer) {
            function toggleOrmawa() {
                if (roleSelect.value === 'pengurus_ormawa') {
                    ormawaContainer.style.display = 'block';
                } else {
                    ormawaContainer.style.display = 'none';
                }
            }
            roleSelect.addEventListener('change', toggleOrmawa);
            toggleOrmawa(); // Trigger initially
        }

        // Toggle Ormawa for Edit Modals
        document.querySelectorAll('.role-select').forEach(function(selectElement) {
            selectElement.addEventListener('change', function() {
                const targetId = this.getAttribute('data-target');
                const container = document.getElementById(targetId);
                if (container) {
                    container.style.display = (this.value === 'pengurus_ormawa') ? 'block' : 'none';
                }
            });
        });

        // Toggle password visibility (both create and edit)
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
        
        // Single toggle for create modal
        const togglePasswordCreate = document.getElementById('togglePassword');
        if (togglePasswordCreate) {
            togglePasswordCreate.addEventListener('click', function () {
                const password = document.getElementById('password');
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                const icon = this.querySelector('i');
                icon.classList.toggle('bi-eye');
                icon.classList.toggle('bi-eye-slash');
            });
        }

        // Auto-open modal if there are validation errors
        @if($errors->any())
            // If the error has a user_id, it's an edit error, but Laravel validator doesn't easily expose this to the view unless custom flashed.
            // For now, we only auto-open Create modal for simplicity.
            var myModal = new bootstrap.Modal(document.getElementById('modalTambahPengguna'));
            myModal.show();
        @endif
    });
</script>
@endsection
