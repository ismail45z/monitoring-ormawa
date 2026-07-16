@extends('layouts.app')
@section('title', 'Pengurus Dashboard')

@section('styles')
<style>
    .stat-card {
        border-left: 4px solid;
        transition: transform 0.2s;
    }
    .stat-card:hover { transform: translateY(-4px); }
    .stat-card.blue  { border-color: #4f46e5; }
    .stat-card.green { border-color: #10b981; }
    .stat-card.orange{ border-color: #f59e0b; }
    .stat-card.red   { border-color: #ef4444; }

    /* FullCalendar overrides */
    .fc-toolbar-title { font-size: 1rem !important; font-weight: 700; }
    .fc-button { font-size: 0.8rem !important; padding: 4px 10px !important; }
    .fc-daygrid-event { border-radius: 4px !important; font-size: 0.75rem; }
    .fc-event-title { white-space: normal !important; }
    .upcoming-badge {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        color: white;
        border-radius: 50%;
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.85rem;
        flex-shrink: 0;
    }
</style>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-0">Dashboard Pengurus Ormawa</h3>
        <p class="text-muted mb-0">Selamat datang, Pengurus {{ $ormawa ? $ormawa->nama_ormawa : 'Ormawa' }}</p>
    </div>
    @if($ormawa)
        <span class="badge bg-primary px-3 py-2 text-uppercase">{{ $ormawa->jenis }} - Periode {{ $ormawa->periode }}</span>
    @endif
</div>

@if(!$ormawa)
    <div class="alert alert-warning card-custom p-4 text-center">
        <i class="bi bi-exclamation-triangle-fill fs-1 text-warning mb-2 d-block"></i>
        <h5>Akun Anda Belum Terhubung</h5>
        <p class="mb-0">Mohon hubungi Administrator untuk menghubungkan akun pengguna Anda dengan data Organisasi Mahasiswa (Ormawa).</p>
    </div>
@else

    {{-- ===== ROW 1: STAT CARDS ===== --}}
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card card-custom stat-card blue p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted small text-uppercase mb-1">Total Kegiatan</p>
                        <h2 class="fw-bold mb-0">{{ $totalKegiatan }}</h2>
                    </div>
                    <div class="fs-1 text-primary opacity-75"><i class="bi bi-calendar-event"></i></div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card card-custom stat-card green p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted small text-uppercase mb-1">Anggota KIP-K</p>
                        <h2 class="fw-bold mb-0">{{ $totalAnggota }}</h2>
                    </div>
                    <div class="fs-1 text-success opacity-75"><i class="bi bi-mortarboard"></i></div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card card-custom stat-card orange p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted small text-uppercase mb-1">Perlu Verifikasi</p>
                        <h2 class="fw-bold mb-0">{{ $pendingCount }}</h2>
                    </div>
                    <div class="fs-1 text-warning opacity-75"><i class="bi bi-hourglass-split"></i></div>
                </div>
                @if($pendingCount > 0)
                    <a href="{{ route('pengurus.kehadiran.index') }}" class="btn btn-sm btn-warning mt-2 w-100 rounded-pill">
                        <i class="bi bi-shield-check me-1"></i> Verifikasi Sekarang
                    </a>
                @endif
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card card-custom stat-card red p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted small text-uppercase mb-1">Mendatang (7hr)</p>
                        <h2 class="fw-bold mb-0">{{ $upcomingEvents->count() }}</h2>
                    </div>
                    <div class="fs-1 text-danger opacity-75"><i class="bi bi-bell"></i></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== ROW 2: TREND CHART + UPCOMING EVENTS ===== --}}
    <div class="row g-4 mb-4">
        {{-- Attendance Trend Chart --}}
        <div class="col-lg-8">
            <div class="card card-custom p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0"><i class="bi bi-bar-chart-line-fill text-primary me-2"></i>Tren Kehadiran 6 Bulan Terakhir</h5>
                    <small class="text-muted">Berdasarkan tanggal pencatatan</small>
                </div>
                <div style="position: relative; height: 250px;">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Upcoming Events Widget --}}
        <div class="col-lg-4">
            <div class="card card-custom p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0"><i class="bi bi-rocket-takeoff-fill text-danger me-2"></i>Mendatang</h5>
                    <small class="text-muted">7 hari ke depan</small>
                </div>
                @forelse($upcomingEvents as $evt)
                    <div class="d-flex align-items-start gap-3 mb-3 pb-3 border-bottom">
                        <div class="upcoming-badge">
                            {{ \Carbon\Carbon::parse($evt->tanggal)->format('d') }}
                        </div>
                        <div class="flex-grow-1 overflow-hidden">
                            <div class="fw-semibold text-truncate">{{ $evt->nama_kegiatan }}</div>
                            <small class="text-muted d-block">
                                <i class="bi bi-calendar2 me-1"></i>
                                {{ \Carbon\Carbon::parse($evt->tanggal)->translatedFormat('l, d M') }}
                            </small>
                            <small class="text-muted">
                                <i class="bi bi-clock me-1"></i>
                                {{ substr($evt->waktu_mulai, 0, 5) }} - {{ substr($evt->waktu_selesai, 0, 5) }}
                                &bull; {{ $evt->tempat }}
                            </small>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-calendar-check fs-2 d-block mb-2 opacity-50"></i>
                        <small>Tidak ada kegiatan dalam 7 hari ke depan</small>
                    </div>
                @endforelse
                <a href="{{ route('pengurus.kegiatan.index') }}" class="btn btn-outline-primary btn-sm w-100 mt-2 rounded-pill">
                    <i class="bi bi-list-ul me-1"></i> Lihat Semua Kegiatan
                </a>
            </div>
        </div>
    </div>

    {{-- ===== ROW 3: CALENDAR + ORMAWA PROFILE ===== --}}
    <div class="row g-4">
        {{-- FullCalendar --}}
        <div class="col-lg-8">
            <div class="card card-custom p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0"><i class="bi bi-calendar3-fill text-success me-2"></i>Kalender Kegiatan</h5>
                    <div class="d-flex gap-2">
                        <span class="badge" style="background:#4f46e5;">Mendatang</span>
                        <span class="badge bg-secondary">Sudah Lewat</span>
                    </div>
                </div>
                <div id="calendarEl"></div>
            </div>
        </div>

        {{-- Ormawa Profile + Recent Table --}}
        <div class="col-lg-4">
            <div class="card card-custom p-4 h-100">
                <h5 class="fw-bold mb-4"><i class="bi bi-info-circle-fill me-2 text-primary"></i>Profil Ormawa</h5>
                <div class="mb-3">
                    <label class="text-muted small d-block">Nama Organisasi</label>
                    <div class="fw-bold">{{ $ormawa->nama_ormawa }}</div>
                </div>
                <div class="mb-3">
                    <label class="text-muted small d-block">Ketua Umum</label>
                    <div class="fw-semibold">{{ $ormawa->ketua }}</div>
                </div>
                <div class="mb-3">
                    <label class="text-muted small d-block">Pembina</label>
                    <div class="fw-semibold">{{ $ormawa->pembina ?? '-' }}</div>
                </div>
                <div class="mb-3">
                    <label class="text-muted small d-block">Deskripsi</label>
                    <p class="text-muted mb-0 small">{{ $ormawa->deskripsi ?? 'Tidak ada deskripsi.' }}</p>
                </div>
                <div class="mb-3">
                    <label class="text-muted small d-block">Status Pendaftaran Anggota</label>
                    <div class="d-flex align-items-center gap-2 mt-1">
                        @if($ormawa->is_open_recruitment)
                            <span class="badge bg-success px-3 py-2"><i class="bi bi-door-open-fill me-1"></i> DIBUKA</span>
                        @else
                            <span class="badge bg-danger px-3 py-2"><i class="bi bi-door-closed-fill me-1"></i> DITUTUP</span>
                        @endif
                        <form action="{{ route('pengurus.ormawa.toggle-recruitment') }}" method="POST" class="m-0">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-secondary rounded-pill">
                                Ubah Status
                            </button>
                        </form>
                    </div>
                </div>
                <hr>
                <div class="d-flex gap-2">
                    <a href="{{ route('pengurus.kegiatan.create') }}" class="btn btn-gradient-primary btn-sm flex-fill rounded-pill">
                        <i class="bi bi-plus-circle me-1"></i> Tambah Kegiatan
                    </a>
                    <a href="{{ route('pengurus.kehadiran.index') }}" class="btn btn-outline-warning btn-sm flex-fill rounded-pill">
                        <i class="bi bi-shield-check me-1"></i> Verifikasi
                        @if($pendingCount > 0)
                            <span class="badge bg-danger rounded-pill ms-1">{{ $pendingCount }}</span>
                        @endif
                    </a>
                </div>
            </div>
        </div>
    </div>

@endif
@endsection

@section('scripts')
{{-- FullCalendar CDN --}}
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.15/locales/id.global.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // ─── Trend Chart ────────────────────────────────────────────────────────────
    const ctx = document.getElementById('trendChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($trendLabels),
                datasets: [
                    {
                        label: 'Hadir',
                        data: @json($trendHadir),
                        borderColor: '#4f46e5',
                        backgroundColor: 'rgba(79,70,229,0.1)',
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#4f46e5',
                        pointRadius: 5,
                    },
                    {
                        label: 'Izin',
                        data: @json($trendIzin),
                        borderColor: '#f59e0b',
                        backgroundColor: 'rgba(245,158,11,0.08)',
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#f59e0b',
                        pointRadius: 5,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top' },
                    tooltip: { mode: 'index', intersect: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 },
                        grid: { color: 'rgba(0,0,0,0.05)' }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    }

    // ─── FullCalendar ───────────────────────────────────────────────────────────
    const calendarEl = document.getElementById('calendarEl');
    if (calendarEl) {
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'id',
            height: 420,
            headerToolbar: {
                left:   'prev,next today',
                center: 'title',
                right:  'dayGridMonth,listWeek'
            },
            buttonText: {
                today:    'Hari Ini',
                month:    'Bulan',
                listWeek: 'Minggu Ini'
            },
            events: @json($calendarEvents),
            eventClick: function(info) {
                info.jsEvent.preventDefault();
                if (info.event.url) {
                    window.location.href = info.event.url;
                }
            },
            dayMaxEvents: 3,
            eventDisplay: 'block',
        });
        calendar.render();
    }

});
</script>
@endsection
