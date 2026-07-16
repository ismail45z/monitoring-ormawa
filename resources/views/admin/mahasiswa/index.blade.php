@extends('layouts.app')
@section('title', 'Kelola Mahasiswa')

@section('content')
<div class="card card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="bi bi-mortarboard-fill text-primary me-2"></i>Kelola Data Mahasiswa KIP-K</h4>
        <a href="{{ route('admin.mahasiswa.create') }}" class="btn btn-gradient-primary rounded-pill"><i class="bi bi-plus-circle me-1"></i> Tambah Mahasiswa</a>
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
                            <a href="{{ route('admin.mahasiswa.edit', $student->id) }}" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-pencil-square"></i></a>
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
@endsection
