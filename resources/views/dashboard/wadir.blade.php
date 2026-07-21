@extends('layouts.app')
@section('title', 'Wadir Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-0">Dashboard Eksekutif</h3>
        <p class="text-muted mb-0">Halaman Monitoring KIP-K Kemahasiswaan</p>
    </div>
    <span class="badge bg-success px-3 py-2 text-uppercase">Pimpinan / Wadir</span>
</div>

<!-- Statistics Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card card-custom p-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted small text-uppercase">Mahasiswa KIP-K</h6>
                    <h2 class="fw-bold mb-0">{{ $totalMahasiswa }}</h2>
                </div>
                <div class="fs-1 text-primary"><i class="bi bi-people-fill"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted small text-uppercase">Total Ormawa</h6>
                    <h2 class="fw-bold mb-0">{{ $totalOrmawa }}</h2>
                </div>
                <div class="fs-1 text-info"><i class="bi bi-diagram-3-fill"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted small text-uppercase">% Aktif Keseluruhan</h6>
                    <h2 class="fw-bold mb-0 text-success">{{ $persenAktifKeseluruhan }}%</h2>
                </div>
                <div class="fs-1 text-success"><i class="bi bi-check-circle-fill"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted small text-uppercase">Perlu Perhatian (<60%)</h6>
                    <h2 class="fw-bold mb-0 text-danger">{{ $tidakAktifCount }}</h2>
                </div>
                <div class="fs-1 text-danger"><i class="bi bi-exclamation-triangle-fill"></i></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Filters & Export -->
    <div class="col-lg-4">
        <div class="card card-custom p-4 h-100">
            <h5 class="fw-bold mb-3"><i class="bi bi-funnel-fill text-primary me-2"></i>Filter Laporan</h5>
            
            <form action="{{ route('wadir.dashboard') }}" method="GET" id="filterForm">
                <div class="mb-3">
                    <label class="form-label small text-muted">Pilih Ormawa</label>
                    <select name="ormawa_id" class="form-select">
                        <option value="">Semua Ormawa</option>
                        @foreach($ormawas as $orm)
                            <option value="{{ $orm->id }}" {{ request('ormawa_id') == $orm->id ? 'selected' : '' }}>
                                {{ $orm->nama_ormawa }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small text-muted">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" class="form-control" value="{{ request('tanggal_mulai') }}">
                </div>
                <div class="mb-4">
                    <label class="form-label small text-muted">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" class="form-control" value="{{ request('tanggal_selesai') }}">
                </div>
                
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-search me-1"></i> Terapkan Filter</button>
                    <a href="{{ route('wadir.dashboard') }}" class="btn btn-light"><i class="bi bi-arrow-counterclockwise me-1"></i> Reset</a>
                </div>
            </form>

            <div class="mt-4 pt-3 border-top border-secondary border-opacity-10">
                <h6 class="small text-muted mb-2">Export Data Terfilter:</h6>
                <div class="row g-2">
                    <div class="col-6">
                        <a href="{{ route('wadir.export.pdf', request()->all()) }}" class="btn btn-outline-danger w-100"><i class="bi bi-file-earmark-pdf-fill me-1"></i> PDF</a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('wadir.export.excel', request()->all()) }}" class="btn btn-outline-success w-100"><i class="bi bi-file-earmark-excel-fill me-1"></i> Excel</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart JS Keaktifan per Ormawa -->
    <div class="col-lg-8">
        <div class="card card-custom p-4 h-100">
            <h5 class="fw-bold mb-3"><i class="bi bi-bar-chart-line-fill text-success me-2"></i>Rata-Rata Tingkat Keaktifan per Ormawa</h5>
            <div style="position: relative; height: 300px;">
                <canvas id="ormawaChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Watchlist Table (Keaktifan < 60%) -->
<div class="card card-custom p-4">
    <h5 class="fw-bold text-danger mb-3"><i class="bi bi-exclamation-triangle-fill me-2"></i>Mahasiswa KIP-K Perlu Perhatian (Keaktifan < 60%)</h5>
    <div class="table-responsive">
        <table class="table align-middle datatable">
            <thead>
                <tr>
                    <th>Nama Mahasiswa</th>
                    <th>NIM</th>
                    <th>No. KIP</th>
                    <th>Jurusan / Prodi</th>
                    <th>Ormawa</th>
                    <th>Poin Didapat / Poin Maks</th>
                    <th>Tingkat Kehadiran</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($watchlist as $w)
                    <tr>
                        <td><span class="fw-semibold">{{ $w['student']->pengguna->nama }}</span></td>
                        <td>{{ $w['student']->nim }}</td>
                        <td>{{ $w['student']->no_kip }}</td>
                        <td>
                            <div class="small fw-semibold text-dark">{{ $w['student']->jurusan ?? '-' }}</div>
                            <div class="small text-muted">{{ $w['student']->prodi ?? '-' }}</div>
                        </td>
                        <td>{{ $w['ormawa'] }}</td>
                        <td>{{ $w['total_poin'] }} / {{ $w['total_poin_maks'] }}</td>
                        <td style="min-width:150px;">
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress flex-grow-1" style="height: 10px;">
                                    <div class="progress-bar {{ $w['persentase'] >= 40 ? 'bg-warning' : 'bg-danger' }}" role="progressbar" style="width: {{ $w['persentase'] }}%"></div>
                                </div>
                                <span class="fw-bold {{ $w['persentase'] >= 40 ? 'text-warning' : 'text-danger' }}">{{ $w['persentase'] }}%</span>
                            </div>
                        </td>
                        <td>
                            @php
                                $status = $w['persentase'] >= 40 ? 'Cukup' : 'Tidak Aktif';
                                $badge = $w['persentase'] >= 40 ? 'bg-warning text-dark' : 'bg-danger';
                            @endphp
                            <span class="badge {{ $badge }} text-uppercase">{{ $status }}</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('ormawaChart').getContext('2d');
        const chartLabels = {!! json_encode($chartLabels) !!};
        const chartData = {!! json_encode($chartData) !!};

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Rata-rata Keaktifan (%)',
                    data: chartData,
                    backgroundColor: 'rgba(59, 130, 246, 0.7)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 1,
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(value) { return value + "%" }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
