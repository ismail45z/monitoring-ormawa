@extends('layouts.app')
@section('title', 'Wadir Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bolder mb-1 text-dark">Dashboard Eksekutif</h2>
        <p class="text-muted mb-0 fw-medium">Monitoring Aktivitas &amp; Kehadiran Mahasiswa KIP-K</p>
    </div>
    <div class="d-flex align-items-center bg-success bg-opacity-10 text-success px-4 py-2 rounded-pill border border-success border-opacity-25 shadow-sm">
        <i class="bi bi-shield-check me-2 fs-5"></i>
        <span class="fw-bold text-uppercase" style="letter-spacing: 1px;">Pimpinan / Wadir</span>
    </div>
</div>

{{-- Statistics Cards --}}
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card card-custom p-4 border-0 shadow-sm rounded-4 h-100" style="background:linear-gradient(135deg,#fff 0%,#f8f9fa 100%);position:relative;overflow:hidden;">
            <div style="position:absolute;top:-20px;right:-20px;opacity:.03;font-size:100px;"><i class="bi bi-people-fill"></i></div>
            <div class="d-flex align-items-center justify-content-between position-relative z-1">
                <div>
                    <p class="text-muted small text-uppercase fw-semibold mb-1" style="letter-spacing:.5px;">Mahasiswa KIP-K</p>
                    <h2 class="fw-bolder mb-0 text-dark">{{ $totalMahasiswa }}</h2>
                </div>
                <div class="d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-circle shadow-sm" style="width:56px;height:56px;">
                    <i class="bi bi-people-fill fs-3"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-4 border-0 shadow-sm rounded-4 h-100" style="background:linear-gradient(135deg,#fff 0%,#f8f9fa 100%);position:relative;overflow:hidden;">
            <div style="position:absolute;top:-20px;right:-20px;opacity:.03;font-size:100px;"><i class="bi bi-diagram-3-fill"></i></div>
            <div class="d-flex align-items-center justify-content-between position-relative z-1">
                <div>
                    <p class="text-muted small text-uppercase fw-semibold mb-1" style="letter-spacing:.5px;">Total Ormawa</p>
                    <h2 class="fw-bolder mb-0 text-dark">{{ $totalOrmawa }}</h2>
                </div>
                <div class="d-flex align-items-center justify-content-center bg-info bg-opacity-10 text-info rounded-circle shadow-sm" style="width:56px;height:56px;">
                    <i class="bi bi-diagram-3-fill fs-3"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-4 border-0 shadow-sm rounded-4 h-100" style="background:linear-gradient(135deg,#fff 0%,#f8f9fa 100%);position:relative;overflow:hidden;">
            <div style="position:absolute;top:-20px;right:-20px;opacity:.03;font-size:100px;"><i class="bi bi-check-circle-fill"></i></div>
            <div class="d-flex align-items-center justify-content-between position-relative z-1">
                <div>
                    <p class="text-muted small text-uppercase fw-semibold mb-1" style="letter-spacing:.5px;">% Aktif (Total)</p>
                    <h2 class="fw-bolder mb-0 text-success">{{ $persenAktifKeseluruhan }}%</h2>
                </div>
                <div class="d-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success rounded-circle shadow-sm" style="width:56px;height:56px;">
                    <i class="bi bi-check-circle-fill fs-3"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-4 border-0 shadow-sm rounded-4 h-100" style="background:linear-gradient(135deg,#fff 0%,#f8f9fa 100%);position:relative;overflow:hidden;">
            <div style="position:absolute;top:-20px;right:-20px;opacity:.03;font-size:100px;"><i class="bi bi-exclamation-triangle-fill"></i></div>
            <div class="d-flex align-items-center justify-content-between position-relative z-1">
                <div>
                    <p class="text-muted small text-uppercase fw-semibold mb-1" style="letter-spacing:.5px;">Perlu Perhatian (&lt;60%)</p>
                    <h2 class="fw-bolder mb-0 text-danger">{{ $tidakAktifCount }}</h2>
                </div>
                <div class="d-flex align-items-center justify-content-center bg-danger bg-opacity-10 text-danger rounded-circle shadow-sm" style="width:56px;height:56px;">
                    <i class="bi bi-exclamation-triangle-fill fs-3"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Keaktifan Category Quick-Filter Cards --}}
<div class="row g-3 mb-4">
    @php
        $cats = [
            ['label'=>'Sangat Aktif','count'=>$countSangatAktif,'range'=>'&ge; 80%',       'color'=>'success','icon'=>'bi-lightning-charge-fill','value'=>'Sangat Aktif'],
            ['label'=>'Aktif',       'count'=>$countAktif,      'range'=>'60 &ndash; 79%','color'=>'primary','icon'=>'bi-person-check-fill',    'value'=>'Aktif'],
            ['label'=>'Cukup',       'count'=>$countCukup,      'range'=>'40 &ndash; 59%','color'=>'warning','icon'=>'bi-person-dash-fill',     'value'=>'Cukup'],
            ['label'=>'Tidak Aktif', 'count'=>$countTidakAktif, 'range'=>'&lt; 40%',       'color'=>'danger', 'icon'=>'bi-person-x-fill',        'value'=>'Tidak Aktif'],
        ];
    @endphp
    @foreach($cats as $cat)
    @php
        $isActive  = $statusKeaktifanFilter === $cat['value'];
        $goHref    = route('wadir.dashboard', array_merge(request()->except('status_keaktifan'), ['status_keaktifan' => $cat['value']]));
        $clearHref = route('wadir.dashboard', array_filter(request()->except('status_keaktifan')));
    @endphp
    <div class="col-6 col-md-3">
        <a href="{{ $isActive ? $clearHref : $goHref }}" class="text-decoration-none">
            <div class="card rounded-4 p-3 h-100 d-flex flex-row align-items-center gap-3 position-relative overflow-hidden border-0 shadow-sm"
                 style="{{ $isActive ? 'outline:2.5px solid;outline-color:var(--bs-'.$cat['color'].');' : '' }}">
                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0 bg-{{ $cat['color'] }} {{ $isActive ? '' : 'bg-opacity-10' }}"
                     style="width:48px;height:48px;">
                    <i class="bi {{ $cat['icon'] }} fs-4 {{ $isActive ? 'text-white' : 'text-'.$cat['color'] }}"></i>
                </div>
                <div>
                    <div class="fw-bolder fs-4 text-{{ $cat['color'] }} lh-1 mb-1">{{ $cat['count'] }}</div>
                    <div class="small fw-semibold text-dark lh-1">{{ $cat['label'] }}</div>
                    <div class="text-muted" style="font-size:.72rem;">{!! $cat['range'] !!}</div>
                </div>
                @if($isActive)
                <span class="position-absolute top-0 end-0 mt-1 me-2" style="font-size:.7rem;">
                    <i class="bi bi-x-circle-fill text-{{ $cat['color'] }}"></i>
                </span>
                @endif
            </div>
        </a>
    </div>
    @endforeach
</div>

<div class="row g-4 mb-4">
    {{-- Filters & Export --}}
    <div class="col-lg-4">
        <div class="card card-custom p-4 border-0 shadow-sm rounded-4 h-100">
            <h5 class="fw-bolder mb-4 d-flex align-items-center text-dark">
                <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-3 text-primary">
                    <i class="bi bi-funnel-fill fs-5"></i>
                </div>
                Filter Laporan
            </h5>
            <form action="{{ route('wadir.dashboard') }}" method="GET" id="filterForm">
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted text-uppercase" style="letter-spacing:.5px;">Status Keaktifan</label>
                    <select name="status_keaktifan" class="form-select form-select-lg fs-6 rounded-3 bg-light border-0 shadow-none">
                        <option value="">Semua Status</option>
                        <option value="Sangat Aktif" {{ $statusKeaktifanFilter==='Sangat Aktif' ? 'selected' : '' }}>Sangat Aktif (&ge;80%)</option>
                        <option value="Aktif"        {{ $statusKeaktifanFilter==='Aktif'        ? 'selected' : '' }}>Aktif (60-79%)</option>
                        <option value="Cukup"        {{ $statusKeaktifanFilter==='Cukup'        ? 'selected' : '' }}>Cukup (40-59%)</option>
                        <option value="Tidak Aktif"  {{ $statusKeaktifanFilter==='Tidak Aktif'  ? 'selected' : '' }}>Tidak Aktif (&lt;40%)</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted text-uppercase" style="letter-spacing:.5px;">Pilih Ormawa</label>
                    <select name="ormawa_id" class="form-select form-select-lg fs-6 rounded-3 bg-light border-0 shadow-none">
                        <option value="">Semua Ormawa</option>
                        @foreach($ormawas as $orm)
                            <option value="{{ $orm->id }}" {{ request('ormawa_id')==$orm->id ? 'selected' : '' }}>{{ $orm->nama_ormawa }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="row mb-4">
                    <div class="col-6">
                        <label class="form-label small fw-semibold text-muted text-uppercase" style="letter-spacing:.5px;">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" class="form-control rounded-3 bg-light border-0 shadow-none" value="{{ request('tanggal_mulai') }}">
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold text-muted text-uppercase" style="letter-spacing:.5px;">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" class="form-control rounded-3 bg-light border-0 shadow-none" value="{{ request('tanggal_selesai') }}">
                    </div>
                </div>
                <div class="d-flex gap-2 mb-4">
                    <button type="submit" class="btn btn-primary flex-grow-1 rounded-3 py-2 fw-semibold shadow-sm">
                        <i class="bi bi-search me-1"></i> Terapkan
                    </button>
                    <a href="{{ route('wadir.dashboard') }}" class="btn btn-light border rounded-3 py-2 px-3 text-secondary">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>
            </form>
            <div class="mt-auto pt-4 border-top border-secondary border-opacity-10">
                <h6 class="small fw-bold text-muted mb-3 text-uppercase" style="letter-spacing:.5px;">Export Data Terfilter</h6>
                <div class="row g-2">
                    <div class="col-6">
                        <a href="{{ route('wadir.export.pdf', request()->all()) }}" target="_blank"
                           class="btn btn-outline-danger w-100 rounded-3 py-2 fw-semibold">
                            <i class="bi bi-file-earmark-pdf-fill me-1"></i> PDF
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('wadir.export.excel', request()->all()) }}"
                           class="btn btn-outline-success w-100 rounded-3 py-2 fw-semibold">
                            <i class="bi bi-file-earmark-excel-fill me-1"></i> Excel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Chart --}}
    <div class="col-lg-8">
        <div class="card card-custom p-4 border-0 shadow-sm rounded-4 h-100">
            <div class="d-flex align-items-center mb-4">
                <div class="bg-success bg-opacity-10 p-2 rounded-3 me-3 text-success">
                    <i class="bi bi-bar-chart-line-fill fs-4"></i>
                </div>
                <h5 class="fw-bolder mb-0 text-dark">Rata-Rata Tingkat Keaktifan per Ormawa</h5>
            </div>
            <div style="position:relative;height:320px;width:100%;">
                <canvas id="ormawaChart"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- All Students Table --}}
@php
    $tTitle = $statusKeaktifanFilter ? 'Mahasiswa — '.$statusKeaktifanFilter : 'Semua Mahasiswa KIP-K';
    $tSub   = match($statusKeaktifanFilter) {
        'Sangat Aktif' => 'Tingkat Keaktifan &ge; 80%',
        'Aktif'        => 'Tingkat Keaktifan 60&ndash;79%',
        'Cukup'        => 'Tingkat Keaktifan 40&ndash;59%',
        'Tidak Aktif'  => 'Tingkat Keaktifan &lt; 40%',
        default        => 'Semua tingkat keaktifan &mdash; klik kartu di atas untuk memfilter',
    };
    $hColor = match($statusKeaktifanFilter) {
        'Sangat Aktif' => 'success',
        'Aktif'        => 'primary',
        'Cukup'        => 'warning',
        'Tidak Aktif'  => 'danger',
        default        => 'secondary',
    };
    $hIcon = match($statusKeaktifanFilter) {
        'Sangat Aktif' => 'bi-lightning-charge-fill',
        'Aktif'        => 'bi-person-check-fill',
        'Cukup'        => 'bi-person-dash-fill',
        'Tidak Aktif'  => 'bi-person-x-fill',
        default        => 'bi-table',
    };
@endphp
<div class="card card-custom p-0 border-0 shadow-sm rounded-4 overflow-hidden mb-4">
    <div class="p-4 border-bottom border-light d-flex align-items-center justify-content-between bg-{{ $hColor }} bg-opacity-10">
        <div class="d-flex align-items-center">
            <div class="bg-{{ $hColor }} text-white p-2 rounded-circle me-3 d-flex align-items-center justify-content-center shadow-sm"
                 style="width:45px;height:45px;">
                <i class="bi {{ $hIcon }} fs-5"></i>
            </div>
            <div>
                <h5 class="fw-bolder mb-0 text-{{ $hColor }}">{{ $tTitle }}</h5>
                <p class="small mb-0 fw-semibold opacity-75 text-{{ $hColor }}">{!! $tSub !!}</p>
            </div>
        </div>
        <span class="badge bg-white text-dark rounded-pill px-3 py-2 fw-bold shadow-sm">
            {{ count($allStudentList) }} mahasiswa
        </span>
    </div>
    <div class="table-responsive p-3">
        <table class="table table-hover align-middle border-0 mb-0 datatable">
            <thead class="table-light text-muted">
                <tr>
                    <th class="border-0 fw-semibold text-uppercase small" style="letter-spacing:.5px;">Mahasiswa</th>
                    <th class="border-0 fw-semibold text-uppercase small" style="letter-spacing:.5px;">Jurusan / Prodi</th>
                    <th class="border-0 fw-semibold text-uppercase small" style="letter-spacing:.5px;">Ormawa</th>
                    <th class="border-0 fw-semibold text-uppercase small" style="letter-spacing:.5px;">Poin</th>
                    <th class="border-0 fw-semibold text-uppercase small" style="letter-spacing:.5px;">Kehadiran</th>
                    <th class="border-0 fw-semibold text-uppercase small" style="letter-spacing:.5px;">Status</th>
                </tr>
            </thead>
            <tbody class="border-top-0">
                @forelse($allStudentList as $w)
                    @php
                        $p = $w['persentase'];
                        $bClass    = match(true){$p>=80=>'bg-success bg-opacity-10 text-success border border-success border-opacity-25',$p>=60=>'bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25',$p>=40=>'bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25',default=>'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25'};
                        $barClass  = match(true){$p>=80=>'bg-success',$p>=60=>'bg-primary',$p>=40=>'bg-warning',default=>'bg-danger'};
                        $textClass = match(true){$p>=80=>'text-success',$p>=60=>'text-primary',$p>=40=>'text-warning',default=>'text-danger'};
                    @endphp
                    <tr class="border-bottom border-secondary border-opacity-10">
                        <td class="py-3">
                            <div class="fw-bold text-dark fs-6">{{ $w['student']->pengguna->nama }}</div>
                            <div class="small text-muted mt-1">NIM: <span class="text-dark">{{ $w['student']->nim }}</span> &bull; KIP: <span class="text-dark">{{ $w['student']->no_kip }}</span></div>
                        </td>
                        <td class="py-3">
                            <div class="small fw-bold text-dark">{{ $w['student']->jurusan ?? '-' }}</div>
                            <div class="small text-muted">{{ $w['student']->prodi ?? '-' }}</div>
                        </td>
                        <td class="py-3 fw-semibold text-secondary">{{ $w['ormawa'] }}</td>
                        <td class="py-3 fw-bolder text-dark">{{ $w['total_poin'] }} <span class="text-muted fw-normal small">/ {{ $w['total_poin_maks'] }}</span></td>
                        <td class="py-3" style="min-width:180px;">
                            <div class="d-flex align-items-center gap-3">
                                <div class="progress flex-grow-1 bg-light shadow-none" style="height:8px;border-radius:10px;">
                                    <div class="progress-bar {{ $barClass }}" role="progressbar" style="width:{{ $p }}%;border-radius:10px;"></div>
                                </div>
                                <span class="fw-bolder {{ $textClass }}">{{ $p }}%</span>
                            </div>
                        </td>
                        <td class="py-3">
                            <span class="badge {{ $bClass }} px-3 py-2 rounded-pill fw-bold" style="letter-spacing:.5px;">{{ $w['status'] }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                            Tidak ada mahasiswa pada kategori ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if(!$statusKeaktifanFilter && count($watchlist) > 0)
<div class="card card-custom p-0 border-0 shadow-sm rounded-4 overflow-hidden mb-4">
    <div class="p-4 border-bottom border-light d-flex align-items-center bg-danger bg-opacity-10">
        <div class="bg-danger text-white p-2 rounded-circle me-3 d-flex align-items-center justify-content-center shadow-sm" style="width:45px;height:45px;">
            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
        </div>
        <div>
            <h5 class="fw-bolder text-danger mb-0">Mahasiswa KIP-K Perlu Perhatian</h5>
            <p class="text-danger opacity-75 small mb-0 fw-semibold">Tingkat Keaktifan &lt; 60%</p>
        </div>
    </div>
    <div class="table-responsive p-3">
        <table class="table table-hover align-middle border-0 mb-0">
            <thead class="table-light text-muted">
                <tr>
                    <th class="border-0 fw-semibold text-uppercase small">Mahasiswa</th>
                    <th class="border-0 fw-semibold text-uppercase small">Jurusan / Prodi</th>
                    <th class="border-0 fw-semibold text-uppercase small">Ormawa</th>
                    <th class="border-0 fw-semibold text-uppercase small">Poin</th>
                    <th class="border-0 fw-semibold text-uppercase small">Kehadiran</th>
                    <th class="border-0 fw-semibold text-uppercase small">Status</th>
                </tr>
            </thead>
            <tbody class="border-top-0">
                @foreach($watchlist as $w)
                    @php
                        $wBadge = $w['persentase']>=40
                            ? 'bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25'
                            : 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25';
                    @endphp
                    <tr class="border-bottom border-secondary border-opacity-10">
                        <td class="py-3">
                            <div class="fw-bold text-dark fs-6">{{ $w['student']->pengguna->nama }}</div>
                            <div class="small text-muted mt-1">NIM: <span class="text-dark">{{ $w['student']->nim }}</span> &bull; KIP: <span class="text-dark">{{ $w['student']->no_kip }}</span></div>
                        </td>
                        <td class="py-3">
                            <div class="small fw-bold text-dark">{{ $w['student']->jurusan ?? '-' }}</div>
                            <div class="small text-muted">{{ $w['student']->prodi ?? '-' }}</div>
                        </td>
                        <td class="py-3 fw-semibold text-secondary">{{ $w['ormawa'] }}</td>
                        <td class="py-3 fw-bolder text-dark">{{ $w['total_poin'] }} <span class="text-muted fw-normal small">/ {{ $w['total_poin_maks'] }}</span></td>
                        <td class="py-3" style="min-width:180px;">
                            <div class="d-flex align-items-center gap-3">
                                <div class="progress flex-grow-1 bg-light shadow-none" style="height:8px;border-radius:10px;">
                                    <div class="progress-bar {{ $w['persentase']>=40 ? 'bg-warning' : 'bg-danger' }}" role="progressbar" style="width:{{ $w['persentase'] }}%;border-radius:10px;"></div>
                                </div>
                                <span class="fw-bolder {{ $w['persentase']>=40 ? 'text-warning' : 'text-danger' }}">{{ $w['persentase'] }}%</span>
                            </div>
                        </td>
                        <td class="py-3">
                            <span class="badge {{ $wBadge }} px-3 py-2 rounded-pill fw-bold">{{ $w['status'] }}</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('ormawaChart').getContext('2d');
        const chartLabels = {!! json_encode($chartLabels) !!};
        const chartData   = {!! json_encode($chartData) !!};

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Rata-rata Keaktifan (%)',
                    data: chartData,
                    backgroundColor: chartData.map(v =>
                        v >= 80 ? 'rgba(16,185,129,.75)' :
                        v >= 60 ? 'rgba(59,130,246,.75)' :
                        v >= 40 ? 'rgba(245,158,11,.75)' : 'rgba(239,68,68,.75)'
                    ),
                    borderColor: chartData.map(v =>
                        v >= 80 ? 'rgba(16,185,129,1)' :
                        v >= 60 ? 'rgba(59,130,246,1)' :
                        v >= 40 ? 'rgba(245,158,11,1)' : 'rgba(239,68,68,1)'
                    ),
                    borderWidth: 1,
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(c) {
                                const v = c.parsed.y;
                                const s = v>=80 ? 'Sangat Aktif' : v>=60 ? 'Aktif' : v>=40 ? 'Cukup' : 'Tidak Aktif';
                                return ` ${v}% — ${s}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: { callback: v => v + '%' }
                    }
                }
            }
        });
    });
</script>
@endsection
