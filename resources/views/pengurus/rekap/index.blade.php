@extends('layouts.app')
@section('title', 'Rekap Keaktifan Anggota')

@section('content')
<div class="card card-custom p-4">
    <h4 class="fw-bold mb-4"><i class="bi bi-bar-chart-line-fill text-primary me-2"></i>Rekap Keaktifan Anggota KIP-K</h4>

    <div class="table-responsive">
        <table class="table align-middle datatable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Mahasiswa</th>
                    <th>NIM</th>
                    <th>Program Studi</th>
                    <th>Hadir / Total Kegiatan</th>
                    <th>Persentase</th>
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
                        <td>{{ $data['total_hadir'] }} / {{ $data['total_kegiatan'] }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="fw-bold me-2">{{ $data['persentase'] }}%</span>
                                <div class="progress flex-grow-1" style="height: 6px; min-width: 80px;">
                                    <div class="progress-bar {{ $data['persentase'] >= 60 ? 'bg-success' : 'bg-danger' }}" role="progressbar" style="width: {{ $data['persentase'] }}%"></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($data['status'] == 'AKTIF')
                                <span class="badge bg-success px-3 py-2 rounded-pill">AKTIF</span>
                            @else
                                <span class="badge bg-danger px-3 py-2 rounded-pill">TIDAK AKTIF</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
