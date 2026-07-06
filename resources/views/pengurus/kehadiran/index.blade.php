@extends('layouts.app')
@section('title', 'Verifikasi Kehadiran')

@section('content')
<div class="card card-custom p-4">
    <h4 class="fw-bold mb-4"><i class="bi bi-check-circle-fill text-primary me-2"></i>Verifikasi Kehadiran Mahasiswa KIP-K</h4>

    <div class="table-responsive">
        <table class="table align-middle datatable">
            <thead>
                <tr>
                    <th>Nama Mahasiswa</th>
                    <th>NIM</th>
                    <th>Kegiatan</th>
                    <th>Tanggal Kegiatan</th>
                    <th>Waktu Pencatatan</th>
                    <th>Pilihan Kehadiran</th>
                    <th>Keterangan Mandiri</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($kehadirans as $kh)
                    <tr>
                        <td><span class="fw-semibold">{{ $kh->mahasiswa->pengguna->nama }}</span></td>
                        <td>{{ $kh->mahasiswa->nim }}</td>
                        <td>{{ $kh->kegiatan->nama_kegiatan }}</td>
                        <td>{{ \Carbon\Carbon::parse($kh->kegiatan->tanggal)->translatedFormat('d M Y') }}</td>
                        <td>
                            <small class="text-muted d-block">Mencatat pada:</small>
                            {{ $kh->created_at->translatedFormat('d M Y, H:i') }}
                        </td>
                        <td>
                            @if($kh->status_kehadiran == 'Hadir')
                                <span class="badge bg-success">Hadir</span>
                            @elseif($kh->status_kehadiran == 'Tidak Hadir')
                                <span class="badge bg-danger">Tidak Hadir</span>
                            @else
                                <span class="badge bg-warning text-dark">Izin</span>
                            @endif
                        </td>
                        <td>{{ $kh->keterangan ?? '-' }}</td>
                        <td>
                            <!-- Setuju Form -->
                            <form action="{{ route('pengurus.kehadiran.approve', $kh->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 me-1"><i class="bi bi-check-lg"></i> Setuju</button>
                            </form>
                            
                            <!-- Tolak Form -->
                            <form action="{{ route('pengurus.kehadiran.reject', $kh->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin MENOLAK kehadiran ini?')">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3"><i class="bi bi-x-lg"></i> Tolak</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
