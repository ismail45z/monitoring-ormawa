@extends('layouts.app')
@section('title', 'Riwayat Kehadiran')

@section('content')
<div class="card card-custom p-4">
    <h4 class="fw-bold mb-4"><i class="bi bi-clock-history text-primary me-2"></i>Riwayat Catatan Kehadiran Anda</h4>

    <div class="table-responsive">
        <table class="table align-middle datatable">
            <thead>
                <tr>
                    <th>Nama Kegiatan</th>
                    <th>Periode</th>
                    <th>Bobot Poin</th>
                    <th>Tanggal Pelaksanaan</th>
                    <th>Status Kehadiran</th>
                    <th>Status Verifikasi</th>
                    <th>Keterangan Anda</th>
                    <th>Tanggal Pengisian</th>
                </tr>
            </thead>
            <tbody>
                @foreach($kehadirans as $kh)
                    <tr>
                        <td><span class="fw-semibold">{{ $kh->kegiatan->nama_kegiatan }}</span></td>
                        <td><span class="badge bg-info text-dark">{{ $kh->kegiatan->periode ?? '-' }}</span></td>
                        <td><span class="badge bg-primary">{{ $kh->kegiatan->bobot_poin }}</span></td>
                        <td>{{ \Carbon\Carbon::parse($kh->kegiatan->tanggal)->translatedFormat('d F Y') }}</td>
                        <td>
                            @if($kh->status_kehadiran == 'Hadir')
                                <span class="badge bg-success">Hadir</span>
                            @elseif($kh->status_kehadiran == 'Tidak Hadir')
                                <span class="badge bg-danger">Tidak Hadir</span>
                            @else
                                <span class="badge bg-warning text-dark">Izin</span>
                            @endif
                        </td>
                        <td>
                            @if($kh->status_verifikasi == 'Disetujui')
                                <span class="badge bg-success"><i class="bi bi-check-circle-fill me-1"></i>Disetujui</span>
                                @if($kh->keterangan_verifikasi)
                                    <div class="mt-1 small text-success"><strong>Catatan:</strong> {{ $kh->keterangan_verifikasi }}</div>
                                @endif
                            @elseif($kh->status_verifikasi == 'Ditolak')
                                <span class="badge bg-danger"><i class="bi bi-x-circle-fill me-1"></i>Ditolak</span>
                                @if($kh->keterangan_verifikasi)
                                    <div class="mt-1 small text-danger"><strong>Alasan:</strong> {{ $kh->keterangan_verifikasi }}</div>
                                @endif
                            @else
                                <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i>Pending</span>
                                @if($kh->keterangan_verifikasi)
                                    <div class="mt-1 small text-muted"><strong>Info:</strong> {{ $kh->keterangan_verifikasi }}</div>
                                @endif
                            @endif
                        </td>
                        <td>{{ $kh->keterangan ?? '-' }}</td>
                        <td>{{ $kh->created_at->translatedFormat('d M Y, H:i') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
