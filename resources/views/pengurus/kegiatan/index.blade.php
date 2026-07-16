@extends('layouts.app')
@section('title', 'Kelola Kegiatan')

@section('content')
<div class="card card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="bi bi-calendar-event-fill text-primary me-2"></i>Kelola Kegiatan Ormawa</h4>
        <a href="{{ route('pengurus.kegiatan.create') }}" class="btn btn-gradient-primary rounded-pill"><i class="bi bi-plus-circle me-1"></i> Buat Kegiatan</a>
    </div>

    <!-- Filter & Search Form -->
    <form action="{{ route('pengurus.kegiatan.index') }}" method="GET" class="mb-4 row g-2">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Cari Nama Kegiatan..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <input type="date" name="tanggal_mulai" class="form-control" placeholder="Tgl Mulai" value="{{ request('tanggal_mulai') }}">
        </div>
        <div class="col-md-3">
            <input type="date" name="tanggal_selesai" class="form-control" placeholder="Tgl Selesai" value="{{ request('tanggal_selesai') }}">
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary w-100" type="submit"><i class="bi bi-filter"></i> Filter</button>
            @if(request('search') || request('tanggal_mulai') || request('tanggal_selesai'))
                <a href="{{ route('pengurus.kegiatan.index') }}" class="btn btn-sm btn-outline-secondary w-100 mt-1">Reset</a>
            @endif
        </div>
    </form>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Nama Kegiatan</th>
                    <th>Periode</th>
                    <th>Bobot (Poin)</th>
                    <th>Tanggal</th>
                    <th>Waktu</th>
                    <th>Tempat</th>
                    <th>Deskripsi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kegiatans as $kegiatan)
                    <tr>
                        <td><span class="fw-semibold">{{ $kegiatan->nama_kegiatan }}</span></td>
                        <td><span class="badge bg-info text-dark">{{ $kegiatan->periode ?? '-' }}</span></td>
                        <td><span class="badge bg-primary">{{ $kegiatan->bobot_poin }}</span></td>
                        <td>{{ \Carbon\Carbon::parse($kegiatan->tanggal)->translatedFormat('d F Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($kegiatan->waktu_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($kegiatan->waktu_selesai)->format('H:i') }}</td>
                        <td>{{ $kegiatan->tempat }}</td>
                        <td>{{ Str::limit($kegiatan->deskripsi, 50) }}</td>
                        <td>
                            <a href="{{ route('pengurus.kegiatan.edit', $kegiatan->id) }}" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-pencil-square"></i></a>
                            <form action="{{ route('pengurus.kegiatan.destroy', $kegiatan->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kegiatan ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">Tidak ada data kegiatan ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-end mt-3">
        {{ $kegiatans->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
