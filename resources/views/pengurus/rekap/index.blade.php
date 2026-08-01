@extends('layouts.app')
@section('title', 'Rekap Keaktifan Anggota')

@section('content')
<div class="card card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-0"><i class="bi bi-bar-chart-line-fill text-primary me-2"></i>Rekap Keaktifan Anggota KIP-K</h4>
            <p class="text-muted small mb-0">Data keaktifan seluruh anggota KIP-K di ormawa Anda</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('pengurus.rekap.export-pdf') }}"
               target="_blank"
               class="btn btn-outline-danger shadow-sm rounded-pill">
                <i class="bi bi-file-earmark-pdf-fill me-1"></i> Ekspor PDF
            </a>
            <a href="{{ route('pengurus.rekap.export-excel') }}"
               class="btn btn-outline-success shadow-sm rounded-pill">
                <i class="bi bi-file-earmark-excel-fill me-1"></i> Ekspor Excel
            </a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table align-middle datatable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Mahasiswa</th>
                    <th>NIM</th>
                    <th>Program Studi</th>
                    <th>Poin Didapat / Maks</th>
                    <th>Tingkat Kehadiran</th>
                    <th>Status Keaktifan</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @foreach($rekapData as $data)
                    <tr>
                        <td>{{ $no++ }}</td>
                        <td><span class="fw-semibold">{{ $data['nama'] }}</span></td>
                        <td>{{ $data['nim'] }}</td>
                        <td>{{ $data['prodi'] }}</td>
                        <td>{{ $data['total_poin'] }} / {{ $data['total_poin_maks'] ?? 0 }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="fw-bold me-2">{{ $data['persentase'] }}%</span>
                                <div class="progress flex-grow-1" style="height: 6px; min-width: 80px;">
                                    @php
                                        $barColor = match($data['status']) {
                                            'Sangat Aktif' => 'bg-success',
                                            'Aktif' => 'bg-primary',
                                            'Cukup' => 'bg-warning',
                                            default => 'bg-danger',
                                        };
                                    @endphp
                                    <div class="progress-bar {{ $barColor }}" role="progressbar" style="width: {{ $data['persentase'] }}%"></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @php
                                $badgeClass = match($data['status']) {
                                    'Sangat Aktif' => 'bg-success',
                                    'Aktif' => 'bg-primary',
                                    'Cukup' => 'bg-warning text-dark',
                                    default => 'bg-danger',
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }} px-3 py-2 rounded-pill text-uppercase">{{ $data['status'] }}</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
