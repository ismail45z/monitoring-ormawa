@extends('layouts.app')
@section('title', 'Kelola Pengumuman')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0"><i class="bi bi-megaphone-fill text-primary me-2"></i>Kelola Pengumuman</h4>
        <p class="text-muted mb-0">Informasi dan berita untuk anggota Ormawa</p>
    </div>
    <a href="{{ route('pengurus.pengumuman.create') }}" class="btn btn-primary rounded-pill">
        <i class="bi bi-plus-circle me-1"></i> Buat Pengumuman
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="card card-custom p-4">
    @if($pengumuman->isEmpty())
        <div class="text-center py-5 text-muted">
            <i class="bi bi-inbox fs-1 d-block mb-2 opacity-50"></i>
            <p class="mb-0">Belum ada pengumuman yang dibuat.</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Isi Singkat</th>
                        <th>Status</th>
                        <th>Tanggal Dibuat</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pengumuman as $p)
                        <tr>
                            <td class="fw-semibold">{{ $p->judul }}</td>
                            <td>
                                {{ \Illuminate\Support\Str::limit($p->isi, 50) }}
                                @if($p->lampiran)
                                    <br><span class="badge bg-info mt-1"><i class="bi bi-image"></i> Ada Lampiran</span>
                                @endif
                            </td>
                            <td>
                                @if($p->is_aktif)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Nonaktif</span>
                                @endif
                            </td>
                            <td><small class="text-muted">{{ $p->created_at->translatedFormat('d M Y, H:i') }}</small></td>
                            <td class="text-center">
                                <a href="{{ route('pengurus.pengumuman.edit', $p->id) }}" class="btn btn-sm btn-outline-primary rounded-pill">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('pengurus.pengumuman.destroy', $p->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengumuman ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
