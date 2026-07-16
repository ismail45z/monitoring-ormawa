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
        <a href="{{ route('admin.pengguna.create') }}" class="btn btn-gradient-primary rounded-pill"><i class="bi bi-plus-circle me-1"></i> Tambah Pengguna</a>
    </div>

    <!-- Filter & Search Form -->
    <form action="{{ route('admin.pengguna.index') }}" method="GET" class="mb-4 row g-2">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Cari Nama atau Email..." value="{{ request('search') }}">
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
        <div class="col-md-3">
            <button class="btn btn-primary" type="submit"><i class="bi bi-filter"></i> Filter</button>
            @if(request('search') || request('role'))
                <a href="{{ route('admin.pengguna.index') }}" class="btn btn-outline-secondary">Reset</a>
            @endif
        </div>
    </form>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
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
                                @if($user->role === 'mahasiswa_kip' && $user->mahasiswa && $user->mahasiswa->bukti_kip)
                                    <a href="{{ Storage::url($user->mahasiswa->bukti_kip) }}" target="_blank" class="btn btn-sm btn-outline-info me-1" title="Lihat Bukti KIP">
                                        <i class="bi bi-image"></i> Bukti
                                    </a>
                                @endif
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
                            @else
                                <a href="{{ route('admin.pengguna.edit', $user->id) }}" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-pencil-square"></i></a>
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
                        <td colspan="6" class="text-center py-4 text-muted">Tidak ada data pengguna ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-end mt-3">
        {{ $users->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
