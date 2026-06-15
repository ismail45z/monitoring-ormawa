@extends('layouts.app')
@section('title', 'Rekap Keaktifan Mandiri')

@section('content')
<div class="card card-custom p-4" style="max-width: 600px;">
    <h4 class="fw-bold mb-4"><i class="bi bi-person-badge-fill text-primary me-2"></i>Rekap Keaktifan Mandiri Mahasiswa KIP-K</h4>

    <div class="text-center py-4">
        <h5 class="text-muted text-uppercase mb-2">Persentase Kehadiran Anda</h5>
        <div class="display-1 fw-bold text-primary mb-3">{{ $persentase }}%</div>
        
        <div class="mb-4">
            <span class="{{ $statusKeaktifan == 'AKTIF' ? 'badge-custom-aktif' : 'badge-custom-tidak' }} text-uppercase fw-bold fs-5 px-4 py-2">
                Status Keaktifan: {{ $statusKeaktifan }}
            </span>
        </div>

        <div class="progress mb-4" style="height: 12px;">
            <div class="progress-bar {{ $persentase >= 60 ? 'bg-success' : 'bg-danger' }}" role="progressbar" style="width: {{ $persentase }}%"></div>
        </div>

        <div class="row g-3 text-start">
            <div class="col-6">
                <div class="bg-light p-3 rounded shadow-sm">
                    <small class="text-muted d-block">Hadir Terverifikasi</small>
                    <span class="fs-4 fw-bold">{{ $totalHadir }}</span> Kegiatan
                </div>
            </div>
            <div class="col-6">
                <div class="bg-light p-3 rounded shadow-sm">
                    <small class="text-muted d-block">Total Kegiatan Ormawa</small>
                    <span class="fs-4 fw-bold">{{ $totalKegiatan }}</span> Kegiatan
                </div>
            </div>
        </div>

        <div class="alert alert-secondary mt-4 mb-0 small text-start">
            <i class="bi bi-info-circle-fill me-1 text-primary"></i> 
            <strong>Syarat Keaktifan Penerima KIP-K:</strong> Mahasiswa diwajibkan memiliki tingkat keaktifan menghadiri kegiatan organisasi minimal <strong>60%</strong>. Status keaktifan ini dipantau secara langsung oleh Wakil Direktur Kemahasiswaan.
        </div>
    </div>
</div>
@endsection
