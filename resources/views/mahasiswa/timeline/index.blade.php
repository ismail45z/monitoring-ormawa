@extends('layouts.app')
@section('title', 'Timeline Progja')

@section('styles')
<style>
    .timeline {
        position: relative;
        padding-left: 3rem;
        margin-top: 2rem;
    }
    .timeline::before {
        content: '';
        position: absolute;
        top: 0;
        left: 14px;
        height: 100%;
        width: 3px;
        background: #e2e8f0;
        border-radius: 3px;
    }
    .timeline-item {
        position: relative;
        margin-bottom: 2rem;
    }
    .timeline-dot {
        position: absolute;
        left: -3rem;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 0.8rem;
        z-index: 1;
        box-shadow: 0 0 0 4px #fff, inset 0 2px 0 rgba(0,0,0,0.08), 0 3px 0 4px rgba(0,0,0,0.05);
    }
    .timeline-dot.bg-success {
        background: linear-gradient(135deg, #10b981, #059669);
    }
    .timeline-dot.bg-primary {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
    }
    .timeline-dot.bg-warning {
        background: linear-gradient(135deg, #fbbf24, #d97706);
    }
    .timeline-content {
        background: #fff;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        transition: transform 0.3s;
        border: 1px solid #f1f5f9;
    }
    .timeline-content:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05);
    }
    .timeline-date {
        font-size: 0.85rem;
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
        display: block;
    }
</style>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0"><i class="bi bi-calendar-range-fill text-primary me-2"></i>Timeline Program Kerja</h4>
            
            <form action="{{ route('mahasiswa.timeline') }}" method="GET" class="d-flex gap-2">
                @if($ormawas->count() > 1)
                    <select name="ormawa_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Semua Ormawa</option>
                        @foreach($ormawas as $orm)
                            <option value="{{ $orm->id }}" {{ $selectedOrmawaId == $orm->id ? 'selected' : '' }}>{{ $orm->nama_ormawa }}</option>
                        @endforeach
                    </select>
                @endif
                <select name="periode" class="form-select form-select-sm" onchange="this.form.submit()">
                    @if($periodes->isEmpty())
                        <option value="">Tidak ada periode</option>
                    @endif
                    @foreach($periodes as $p)
                        <option value="{{ $p }}" {{ $selectedPeriode == $p ? 'selected' : '' }}>Periode {{ $p }}</option>
                    @endforeach
                </select>
                <noscript><button type="submit" class="btn btn-sm btn-primary">Filter</button></noscript>
            </form>
        </div>

        @if($kegiatans->isEmpty())
            <div class="card card-custom p-5 text-center">
                <i class="bi bi-calendar-x text-muted mb-3" style="font-size: 3rem;"></i>
                <h5 class="fw-bold text-muted">Belum Ada Agenda</h5>
                <p class="text-muted mb-0">Belum ada program kerja yang diagendakan untuk periode ini.</p>
            </div>
        @else
            <div class="timeline">
                @foreach($kegiatans as $kegiatan)
                    @php
                        $isPast = \Carbon\Carbon::parse($kegiatan->tanggal)->isPast();
                        $isToday = \Carbon\Carbon::parse($kegiatan->tanggal)->isToday();
                        
                        if($isToday) {
                            $dotColor = 'bg-warning';
                            $icon = 'bi-star-fill';
                        } elseif($isPast) {
                            $dotColor = 'bg-success';
                            $icon = 'bi-check-lg';
                        } else {
                            $dotColor = 'bg-primary';
                            $icon = 'bi-calendar-event';
                        }
                    @endphp

                    <div class="timeline-item">
                        <div class="timeline-dot {{ $dotColor }}">
                            <i class="bi {{ $icon }}"></i>
                        </div>
                        <div class="timeline-content">
                            <span class="timeline-date">
                                <i class="bi bi-clock me-1"></i> {{ \Carbon\Carbon::parse($kegiatan->tanggal)->translatedFormat('l, d F Y') }}
                                &bull; {{ \Carbon\Carbon::parse($kegiatan->waktu_mulai)->format('H:i') }} - Selesai
                            </span>
                            <h5 class="fw-bold text-dark mb-2">{{ $kegiatan->nama_kegiatan }}</h5>
                            <p class="text-muted mb-3">{{ $kegiatan->deskripsi ?? 'Tidak ada deskripsi.' }}</p>
                            
                            <div class="d-flex align-items-center flex-wrap gap-2">
                                <span class="badge bg-light text-dark border"><i class="bi bi-geo-alt-fill text-danger me-1"></i> {{ $kegiatan->tempat }}</span>
                                <span class="badge bg-light text-dark border"><i class="bi bi-award-fill text-warning me-1"></i> Bobot: {{ $kegiatan->bobot_poin }} Poin</span>
                                @if($isToday)
                                    <span class="badge bg-warning text-dark"><i class="bi bi-exclamation-circle-fill me-1"></i> Sedang Berlangsung / Hari Ini</span>
                                @elseif($isPast)
                                    <span class="badge bg-success"><i class="bi bi-check-circle-fill me-1"></i> Selesai</span>
                                @else
                                    <span class="badge bg-primary"><i class="bi bi-hourglass-split me-1"></i> Akan Datang</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

    </div>
</div>
@endsection
