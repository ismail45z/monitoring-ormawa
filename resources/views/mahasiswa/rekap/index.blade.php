@extends('layouts.app')
@section('title', 'Rekap Keaktifan Mandiri')

@section('styles')
<style>
/* ── Page Header ───────────────────────────────────────── */
.rekap-header {
    background: linear-gradient(135deg, #1e3a5f 0%, #0f6ecd 60%, #38bdf8 100%);
    border-radius: 20px;
    padding: 2rem 2.5rem;
    color: #fff;
    margin-bottom: 2rem;
    position: relative;
    overflow: hidden;
}
.rekap-header::before {
    content: '';
    position: absolute;
    top: -40px; right: -40px;
    width: 200px; height: 200px;
    border-radius: 50%;
    background: rgba(255,255,255,.07);
}
.rekap-header::after {
    content: '';
    position: absolute;
    bottom: -60px; left: 30%;
    width: 300px; height: 300px;
    border-radius: 50%;
    background: rgba(255,255,255,.04);
}

/* ── Ormawa Card ───────────────────────────────────────── */
.ormawa-card {
    background: #fff;
    border-radius: 20px;
    border: none;
    overflow: hidden;
    box-shadow: 0 4px 24px rgba(0,0,0,.07);
    transition: transform .25s ease, box-shadow .25s ease;
}
.ormawa-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 12px 40px rgba(0,0,0,.13);
}

/* ── Card accent stripe ────────────────────────────────── */
.ormawa-card .card-stripe { height: 6px; width: 100%; }
.stripe-success { background: linear-gradient(90deg, #10b981, #34d399); }
.stripe-warning { background: linear-gradient(90deg, #f59e0b, #fcd34d); }
.stripe-info    { background: linear-gradient(90deg, #0ea5e9, #7dd3fc); }
.stripe-danger  { background: linear-gradient(90deg, #ef4444, #f87171); }

/* ── Circular SVG Progress ─────────────────────────────── */
.circle-wrap {
    position: relative;
    width: 140px; height: 140px;
    margin: 0 auto 1rem;
}
.circle-wrap svg { transform: rotate(-90deg); }
.circle-bg { fill: none; stroke: #f1f5f9; stroke-width: 10; }
.circle-fg {
    fill: none;
    stroke-width: 10;
    stroke-linecap: round;
    transition: stroke-dashoffset 1.2s cubic-bezier(.4,0,.2,1);
}
.circle-fg.c-success { stroke: #10b981; }
.circle-fg.c-warning { stroke: #f59e0b; }
.circle-fg.c-info    { stroke: #0ea5e9; }
.circle-fg.c-danger  { stroke: #ef4444; }
.circle-label {
    position: absolute;
    inset: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    line-height: 1;
}
.circle-pct  { font-size: 2rem; font-weight: 800; }
.circle-unit { font-size: .85rem; font-weight: 600; opacity: .6; }

/* ── Status Badge ──────────────────────────────────────── */
.status-pill {
    display: inline-block;
    padding: .35em 1.1em;
    border-radius: 999px;
    font-size: .78rem;
    font-weight: 700;
    letter-spacing: .5px;
    text-transform: uppercase;
}
.pill-success { background: #d1fae5; color: #065f46; }
.pill-warning { background: #fef3c7; color: #92400e; }
.pill-info    { background: #e0f2fe; color: #0369a1; }
.pill-danger  { background: #fee2e2; color: #991b1b; }

/* ── Stat Boxes ────────────────────────────────────────── */
.stat-box {
    background: #f8fafc;
    border-radius: 12px;
    padding: 1rem;
    text-align: center;
}
.stat-box .stat-val { font-size: 1.4rem; font-weight: 800; line-height: 1.2; }
.stat-box .stat-lbl { font-size: .72rem; color: #64748b; margin-top: .2rem; }

/* ── Ormawa Icon Badge ─────────────────────────────────── */
.org-icon-wrap {
    width: 48px; height: 48px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 1.3rem;
}

/* ── Info Alert ────────────────────────────────────────── */
.info-note {
    background: linear-gradient(135deg,#eff6ff,#e0f2fe);
    border: 1px solid #bae6fd;
    border-radius: 14px;
    padding: 1rem 1.5rem;
    color: #0369a1;
    font-size: .85rem;
}

/* Dark mode */
[data-bs-theme="dark"] .ormawa-card { background: #1e293b; }
[data-bs-theme="dark"] .stat-box    { background: #273344; }
[data-bs-theme="dark"] .circle-bg   { stroke: #334155; }
[data-bs-theme="dark"] .info-note   { background: linear-gradient(135deg,#1e3a5f,#0c2d48); border-color: #1d4ed8; color: #93c5fd; }
</style>
@endsection

@section('content')

{{-- ── Page Header ─────────────────────────────────────────── --}}
<div class="rekap-header mb-4">
    <div class="d-flex align-items-center gap-4 position-relative">
        <div style="background:rgba(255,255,255,.18);border-radius:18px;width:68px;height:68px;display:flex;align-items:center;justify-content:center;font-size:2rem;flex-shrink:0;box-shadow:0 4px 16px rgba(0,0,0,.15);">
            <i class="bi bi-bar-chart-line-fill"></i>
        </div>
        <div>
            <h4 class="fw-bold mb-1" style="font-size:1.55rem;letter-spacing:-.3px;">Rekap Keaktifan Mandiri</h4>
            <p class="mb-0 opacity-80 small">Pantau perkembangan keaktifan Anda di setiap Ormawa yang diikuti</p>
        </div>
    </div>
</div>

@if($rekapPerOrmawa->isEmpty())
    {{-- Empty State --}}
    <div class="text-center py-5">
        <div style="width:90px;height:90px;background:linear-gradient(135deg,#e0f2fe,#bae6fd);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem;font-size:2.5rem;">
            <i class="bi bi-people text-info"></i>
        </div>
        <h5 class="fw-bold mb-1">Belum Terdaftar di Ormawa</h5>
        <p class="text-muted mb-0 small">Silakan minta Admin untuk menghubungkan Anda dengan Ormawa.</p>
    </div>
@else
    @php
        $total = $rekapPerOrmawa->count();
        $colClass = match(true) {
            $total === 1 => 'col-12 col-md-8 col-xl-6 mx-auto',
            $total === 2 => 'col-md-6',
            default      => 'col-md-6 col-xl-4',
        };
    @endphp
    <div class="row g-4 {{ $total === 1 ? 'justify-content-center' : '' }}">
        @foreach($rekapPerOrmawa as $index => $rekap)
            @php
                $pct    = min($rekap['persentase'], 100);
                $status = $rekap['statusKeaktifan'];

                $scheme = match($status) {
                    'Sangat Aktif' => ['stripe'=>'stripe-success','circ'=>'c-success','pill'=>'pill-success','icon_bg'=>'rgba(16,185,129,.12)','icon_color'=>'#10b981','val_color'=>'#10b981'],
                    'Aktif'        => ['stripe'=>'stripe-info',   'circ'=>'c-info',   'pill'=>'pill-info',   'icon_bg'=>'rgba(14,165,233,.12)','icon_color'=>'#0ea5e9','val_color'=>'#0ea5e9'],
                    'Cukup'        => ['stripe'=>'stripe-warning','circ'=>'c-warning','pill'=>'pill-warning','icon_bg'=>'rgba(245,158,11,.12)','icon_color'=>'#f59e0b','val_color'=>'#f59e0b'],
                    default        => ['stripe'=>'stripe-danger', 'circ'=>'c-danger', 'pill'=>'pill-danger', 'icon_bg'=>'rgba(239,68,68,.12)', 'icon_color'=>'#ef4444','val_color'=>'#ef4444'],
                };

                // Bigger ring for single card
                $r    = $total === 1 ? 70 : 54;
                $circ = 2 * M_PI * $r;
                $dash = $circ - ($pct / 100 * $circ);
                $svgSize = $total === 1 ? 180 : 140;
            @endphp

            <div class="{{ $colClass }}">
                <div class="ormawa-card h-100">

                    <div class="card-stripe {{ $scheme['stripe'] }}"></div>

                    <div class="p-4">

                        {{-- Ormawa Name --}}
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="org-icon-wrap" style="background:{{ $scheme['icon_bg'] }}; color:{{ $scheme['icon_color'] }}">
                                <i class="bi bi-building-fill"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0">{{ $rekap['ormawa']->nama_ormawa }}</h6>
                                @if($rekap['ormawa']->jenis)
                                    <small class="text-muted">{{ $rekap['ormawa']->jenis }}</small>
                                @endif
                            </div>
                        </div>

                        {{-- Circular Progress --}}
                        <div class="circle-wrap" style="width:{{ $svgSize }}px;height:{{ $svgSize }}px;">
                            <svg width="{{ $svgSize }}" height="{{ $svgSize }}" viewBox="0 0 {{ $svgSize }} {{ $svgSize }}">
                                <circle class="circle-bg" cx="{{ $svgSize/2 }}" cy="{{ $svgSize/2 }}" r="{{ $r }}"/>
                                <circle class="circle-fg {{ $scheme['circ'] }}"
                                        cx="{{ $svgSize/2 }}" cy="{{ $svgSize/2 }}" r="{{ $r }}"
                                        stroke-dasharray="{{ $circ }}"
                                        stroke-dashoffset="{{ $circ }}"
                                        data-offset="{{ $dash }}"
                                        id="circ-{{ $index }}"/>
                            </svg>
                            <div class="circle-label">
                                <span class="circle-pct" style="color:{{ $scheme['val_color'] }};font-size:{{ $total===1 ? '2.6rem' : '2rem' }};">{{ $rekap['persentase'] }}</span>
                                <span class="circle-unit" style="color:{{ $scheme['val_color'] }}">%</span>
                            </div>
                        </div>

                        {{-- Status Badge --}}
                        <div class="text-center mb-4">
                            <span class="status-pill {{ $scheme['pill'] }}">{{ $status }}</span>
                        </div>

                        {{-- Stats --}}
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="stat-box">
                                    <div class="stat-val">
                                        <span style="color:{{ $scheme['val_color'] }}">{{ $rekap['totalPoin'] }}</span>
                                        <span class="fs-6 text-muted fw-normal"> / {{ $rekap['totalPoinMaks'] }}</span>
                                    </div>
                                    <div class="stat-lbl"><i class="bi bi-star-fill me-1" style="color:{{ $scheme['icon_color'] }}"></i>Poin / Maks</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-box">
                                    <div class="stat-val" style="color:{{ $scheme['val_color'] }}">{{ $rekap['totalKegiatan'] }}</div>
                                    <div class="stat-lbl"><i class="bi bi-calendar-check-fill me-1" style="color:{{ $scheme['icon_color'] }}"></i>Kegiatan</div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Info Note --}}
    <div class="info-note mt-4">
        <i class="bi bi-info-circle-fill me-2"></i>
        <strong>Syarat Keaktifan:</strong>
        Status dihitung berdasarkan akumulasi poin kegiatan yang diikuti —
        <strong>Sangat Aktif</strong> (≥ 80%),
        <strong>Aktif</strong> (60–79%),
        <strong>Cukup</strong> (40–59%),
        <strong>Tidak Aktif</strong> (&lt; 40%).
        Status dipantau oleh Wakil Direktur Kemahasiswaan.
    </div>
@endif

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Animate circular progress bars
    document.querySelectorAll('.circle-fg').forEach(el => {
        const target = parseFloat(el.dataset.offset);
        setTimeout(() => { el.style.strokeDashoffset = target; }, 200);
    });

    // Card entrance animation
    document.querySelectorAll('.ormawa-card').forEach((card, i) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = `opacity .5s ease ${i * 0.12}s, transform .5s ease ${i * 0.12}s, box-shadow .25s ease, transform .25s ease`;
        setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 50);
    });
});
</script>
@endsection
