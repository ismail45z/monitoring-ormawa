@extends('layouts.app')
@section('title', 'Rekap Keaktifan Mandiri')

@section('content')
<h4 class="fw-bold mb-4"><i class="bi bi-person-badge-fill text-primary me-2"></i>Rekap Keaktifan Mandiri per Ormawa</h4>

@if($rekapPerOrmawa->isEmpty())
    <div class="alert alert-warning">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        Anda belum terdaftar di Ormawa manapun. Silakan minta Admin untuk menghubungkan Anda dengan Ormawa.
    </div>
@else
    <div class="row g-4">
        @foreach($rekapPerOrmawa as $rekap)
            <div class="col-md-6">
                <div class="card card-custom p-4 h-100">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="bi bi-building text-primary fs-5"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0">{{ $rekap['ormawa']->nama_ormawa }}</h5>
                            @if($rekap['ormawa']->jenis)
                                <small class="text-muted">{{ $rekap['ormawa']->jenis }}</small>
                            @endif
                        </div>
                    </div>

                    <div class="text-center py-3">
                        <div class="display-4 fw-bold {{ $rekap['persentase'] >= 75 ? 'text-success' : 'text-danger' }} mb-2">
                            {{ $rekap['persentase'] }}<span class="fs-5">%</span>
                        </div>
                        <span class="{{ $rekap['statusKeaktifan'] == 'AKTIF' ? 'badge-custom-aktif' : 'badge-custom-tidak' }} text-uppercase fw-semibold px-3 py-2">
                            {{ $rekap['statusKeaktifan'] }}
                        </span>
                    </div>

                    <div class="progress mb-3" style="height: 10px;">
                        <div class="progress-bar {{ $rekap['persentase'] >= 75 ? 'bg-success' : 'bg-danger' }}" 
                            role="progressbar" 
                            style="width: {{ $rekap['persentase'] > 100 ? 100 : $rekap['persentase'] }}%">
                        </div>
                    </div>

                    <div class="row g-2">
                        <div class="col-6">
                            <div class="bg-light p-3 rounded text-center">
                                <small class="text-muted d-block">Poin Didapat / Maks</small>
                                <span class="fs-4 fw-bold">{{ $rekap['totalPoin'] }} <span class="fs-6 text-muted">/ {{ $rekap['totalPoinMaks'] }}</span></span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-light p-3 rounded text-center">
                                <small class="text-muted d-block">Jumlah Kegiatan</small>
                                <span class="fs-4 fw-bold">{{ $rekap['totalKegiatan'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="alert alert-secondary mt-4 small">
        <i class="bi bi-info-circle-fill me-1 text-primary"></i>
        <strong>Syarat Keaktifan:</strong> Mahasiswa diwajibkan memiliki tingkat kehadiran minimal <strong>75%</strong> pada setiap Ormawa yang diikuti. Status dipantau oleh Wakil Direktur Kemahasiswaan.
    </div>
@endif
@endsection
