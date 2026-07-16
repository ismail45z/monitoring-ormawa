@extends('layouts.app')
@section('title', 'Kegiatan Ormawa')

@section('content')
<div class="card card-custom p-4">
    <h4 class="fw-bold mb-4"><i class="bi bi-calendar3 text-primary me-2"></i>Daftar Kegiatan Ormawa Anda</h4>

    @if(isset($info))
        <div class="alert alert-info">
            <i class="bi bi-info-circle-fill me-2"></i> {{ $info }}
        </div>
    @endif

    <div class="table-responsive">
        <table class="table align-middle datatable">
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
                @foreach($kegiatans as $kegiatan)
                    <tr>
                        <td><span class="fw-semibold">{{ $kegiatan->nama_kegiatan }}</span></td>
                        <td><span class="badge bg-info text-dark">{{ $kegiatan->periode ?? '-' }}</span></td>
                        <td><span class="badge bg-primary">{{ $kegiatan->bobot_poin }}</span></td>
                        <td>{{ \Carbon\Carbon::parse($kegiatan->tanggal)->translatedFormat('d F Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($kegiatan->waktu_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($kegiatan->waktu_selesai)->format('H:i') }}</td>
                        <td>{{ $kegiatan->tempat }}</td>
                        <td>{{ $kegiatan->deskripsi ?? '-' }}</td>
                        <td>
                            @if(in_array($kegiatan->id, $alreadyLogged))
                                <span class="badge bg-secondary py-2 px-3 rounded-pill"><i class="bi bi-check-lg me-1"></i>Telah Dicatat</span>
                            @else
                                <a href="{{ route('mahasiswa.kehadiran.catat', $kegiatan->id) }}" class="btn btn-sm btn-primary rounded-pill px-3"><i class="bi bi-pencil me-1"></i> Catat Kehadiran</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
