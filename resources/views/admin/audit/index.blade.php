@extends('layouts.app')
@section('title', 'Audit Log (Riwayat Perubahan)')

@php
    // Fields that should NEVER be shown in audit log for security
    $sensitiveFields = ['password', 'remember_token', 'token', 'api_token', 'secret', 'no_kip'];
@endphp

@section('styles')
<style>
    .audit-old {
        background-color: rgba(239, 68, 68, 0.12);
        color: #f87171;
        text-decoration: line-through;
        padding: 0.2rem 0.5rem;
        border-radius: 6px;
        display: block;
        word-break: break-word;
        font-family: monospace;
        font-size: 0.8rem;
        margin-bottom: 0.2rem;
        border: 1px solid rgba(239, 68, 68, 0.15);
    }
    .audit-new {
        background-color: rgba(16, 185, 129, 0.12);
        color: #34d399;
        padding: 0.2rem 0.5rem;
        border-radius: 6px;
        display: block;
        word-break: break-word;
        font-family: monospace;
        font-size: 0.8rem;
        border: 1px solid rgba(16, 185, 129, 0.15);
    }
    .audit-value {
        font-family: monospace;
        word-break: break-word;
        font-size: 0.8rem;
    }
    .table-audit th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.72rem;
        letter-spacing: 0.05em;
        color: #94a3b8;
        border-bottom: 2px solid rgba(255,255,255,0.06);
        padding: 0.85rem 1rem;
        white-space: nowrap;
    }
    .table-audit td {
        vertical-align: middle;
        padding: 0.85rem 1rem;
        border-bottom: 1px solid rgba(255,255,255,0.04);
    }
    .table-audit tr:last-child td { border-bottom: none; }
    .table-audit tr:hover td { background: rgba(255,255,255,0.02); }

    .detail-table td {
        padding: 0.3rem 0.5rem;
        font-size: 0.82rem;
        border-color: rgba(255,255,255,0.06) !important;
        vertical-align: top;
    }
    .audit-key {
        color: #94a3b8;
        font-weight: 600;
        text-transform: capitalize;
        font-size: 0.78rem;
        width: 30%;
        white-space: nowrap;
    }
    .audit-sensitive-mask {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        background: rgba(245, 158, 11, 0.1);
        border: 1px solid rgba(245, 158, 11, 0.2);
        color: #fbbf24;
        border-radius: 6px;
        padding: 0.15rem 0.5rem;
        font-size: 0.78rem;
        font-family: monospace;
    }
    .toggle-detail-btn {
        font-size: 0.75rem;
        padding: 0.2rem 0.6rem;
        border-radius: 20px;
        transition: all 0.2s;
    }
    .toggle-detail-btn:hover { transform: scale(1.05); }
    .detail-collapse { margin-top: 0.5rem; }
    .event-icon-badge {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
        flex-shrink: 0;
    }
    .audit-row-created { border-left: 3px solid #10b981; }
    .audit-row-updated { border-left: 3px solid #f59e0b; }
    .audit-row-deleted { border-left: 3px solid #ef4444; }
    .filter-card { border-radius: 16px; }
    .stat-summary-badge {
        background: rgba(255,255,255,0.05);
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 12px;
        padding: 0.5rem 1rem;
        font-size: 0.82rem;
    }
</style>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-0"><i class="bi bi-shield-lock-fill text-primary me-2"></i>Audit Log</h4>
        <p class="text-muted mb-0 small">Riwayat perubahan data pada sistem &mdash; total {{ $audits->total() }} entri</p>
    </div>
    <div class="d-flex gap-2">
        <div class="stat-summary-badge text-success"><i class="bi bi-plus-circle me-1"></i> Created</div>
        <div class="stat-summary-badge text-warning"><i class="bi bi-pencil me-1"></i> Updated</div>
        <div class="stat-summary-badge text-danger"><i class="bi bi-trash me-1"></i> Deleted</div>
    </div>
</div>

{{-- Filter Card --}}
<div class="card card-custom p-4 mb-4 filter-card">
    <form action="{{ route('admin.audit.index') }}" method="GET">
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-semibold small mb-1"><i class="bi bi-box me-1"></i>Filter Model</label>
                <select name="model" class="form-select form-select-sm">
                    <option value="">Semua Model</option>
                    <option value="Pengguna"  {{ request('model') == 'Pengguna'  ? 'selected' : '' }}>Pengguna</option>
                    <option value="Mahasiswa" {{ request('model') == 'Mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                    <option value="Ormawa"    {{ request('model') == 'Ormawa'    ? 'selected' : '' }}>Ormawa</option>
                    <option value="Kegiatan"  {{ request('model') == 'Kegiatan'  ? 'selected' : '' }}>Kegiatan</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold small mb-1"><i class="bi bi-lightning me-1"></i>Filter Event</label>
                <select name="event" class="form-select form-select-sm">
                    <option value="">Semua Event</option>
                    <option value="created" {{ request('event') == 'created' ? 'selected' : '' }}>✅ Created</option>
                    <option value="updated" {{ request('event') == 'updated' ? 'selected' : '' }}>✏️ Updated</option>
                    <option value="deleted" {{ request('event') == 'deleted' ? 'selected' : '' }}>🗑️ Deleted</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold small mb-1"><i class="bi bi-person me-1"></i>Cari Pelaku</label>
                <input type="text" name="user" class="form-control form-control-sm" placeholder="Nama pengguna..." value="{{ request('user') }}">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary flex-fill rounded-pill">
                    <i class="bi bi-search me-1"></i> Filter
                </button>
                <a href="{{ route('admin.audit.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>
        </div>
    </form>
</div>

{{-- Audit Table --}}
<div class="card card-custom p-0 overflow-hidden">
    @if($audits->isEmpty())
        <div class="text-center py-5 text-muted p-4">
            <i class="bi bi-clock-history fs-1 d-block mb-2 opacity-50"></i>
            <p class="mb-0">Belum ada riwayat perubahan data.</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover table-audit mb-0">
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th width="14%">Waktu</th>
                        <th width="18%">Pelaku</th>
                        <th width="10%">Event</th>
                        <th width="13%">Model</th>
                        <th width="40%">Perubahan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($audits as $i => $audit)
                        @php
                            $eventClass = match($audit->event) {
                                'created' => 'audit-row-created',
                                'updated' => 'audit-row-updated',
                                'deleted' => 'audit-row-deleted',
                                default   => ''
                            };
                            $eventIcon = match($audit->event) {
                                'created' => ['icon' => 'bi-plus-lg',   'bg' => 'rgba(16,185,129,0.15)',  'color' => '#34d399'],
                                'updated' => ['icon' => 'bi-pencil',    'bg' => 'rgba(245,158,11,0.15)', 'color' => '#fbbf24'],
                                'deleted' => ['icon' => 'bi-trash',     'bg' => 'rgba(239,68,68,0.15)',  'color' => '#f87171'],
                                default   => ['icon' => 'bi-question',  'bg' => 'rgba(148,163,184,0.15)','color' => '#94a3b8'],
                            };
                            $allKeys = array_unique(array_merge(
                                array_keys($audit->old_values ?? []),
                                array_keys($audit->new_values ?? [])
                            ));
                            $allKeys = array_filter($allKeys, fn($k) =>
                                !in_array($k, ['created_at', 'updated_at', 'remember_token', 'email_verified_at'])
                            );
                            $hasChanges = !empty($allKeys);
                            $changeCount = count($allKeys);
                        @endphp
                        <tr class="{{ $eventClass }}">
                            {{-- Row Number --}}
                            <td class="text-muted small">{{ $audits->firstItem() + $i }}</td>

                            {{-- Waktu --}}
                            <td>
                                <div class="fw-semibold small">{{ $audit->created_at->translatedFormat('d M Y') }}</div>
                                <small class="text-muted">{{ $audit->created_at->format('H:i:s') }}</small>
                            </td>

                            {{-- Pelaku --}}
                            <td>
                                @if($audit->user)
                                    <div class="fw-semibold small">{{ $audit->user->nama }}</div>
                                    <small class="text-muted d-block">{{ $audit->user->role }}</small>
                                @else
                                    <span class="text-muted fst-italic small">Sistem / Guest</span>
                                @endif
                                <small class="text-muted" style="font-size: 0.68rem; opacity: 0.6;">{{ $audit->ip_address }}</small>
                            </td>

                            {{-- Event Badge --}}
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="event-icon-badge"
                                         style="background:{{ $eventIcon['bg'] }}; color:{{ $eventIcon['color'] }};">
                                        <i class="bi {{ $eventIcon['icon'] }}"></i>
                                    </div>
                                    @if($audit->event == 'created')
                                        <span class="badge rounded-pill" style="background:rgba(16,185,129,0.15); color:#34d399; border:1px solid rgba(16,185,129,0.2);">Created</span>
                                    @elseif($audit->event == 'updated')
                                        <span class="badge rounded-pill" style="background:rgba(245,158,11,0.15); color:#fbbf24; border:1px solid rgba(245,158,11,0.2);">Updated</span>
                                    @elseif($audit->event == 'deleted')
                                        <span class="badge rounded-pill" style="background:rgba(239,68,68,0.15); color:#f87171; border:1px solid rgba(239,68,68,0.2);">Deleted</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $audit->event }}</span>
                                    @endif
                                </div>
                            </td>

                            {{-- Model --}}
                            <td>
                                <div class="fw-semibold small">{{ class_basename($audit->auditable_type) }}</div>
                                <small class="text-muted">ID: {{ $audit->auditable_id }}</small>
                            </td>

                            {{-- Detail Perubahan (Collapsible) --}}
                            <td>
                                @if(!$hasChanges)
                                    <span class="text-muted fst-italic small">—</span>
                                @else
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="text-muted small">{{ $changeCount }} field berubah</span>
                                        <button class="btn btn-sm btn-outline-secondary toggle-detail-btn"
                                                type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#detail-{{ $audit->id }}"
                                                aria-expanded="false">
                                            <i class="bi bi-chevron-down me-1" style="font-size:0.7rem;"></i>Lihat Detail
                                        </button>
                                    </div>
                                    <div class="collapse detail-collapse" id="detail-{{ $audit->id }}">
                                        <table class="table table-sm table-bordered detail-table mb-0 rounded-3 overflow-hidden">
                                            <thead>
                                                <tr>
                                                    <th class="audit-key" style="font-size:0.72rem;">Field</th>
                                                    <th style="font-size:0.72rem; color:#94a3b8;">Nilai</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($allKeys as $key)
                                                    <tr>
                                                        <td class="audit-key">{{ str_replace('_', ' ', $key) }}</td>
                                                        <td>
                                                            @if(in_array($key, $sensitiveFields))
                                                                {{-- MASK sensitive fields --}}
                                                                <span class="audit-sensitive-mask">
                                                                    <i class="bi bi-shield-lock-fill"></i>
                                                                    <em>[ Data Sensitif Tersembunyi ]</em>
                                                                </span>
                                                            @else
                                                                @if($audit->event == 'updated')
                                                                    @if(array_key_exists($key, $audit->old_values))
                                                                        <span class="audit-old">{{ is_string($audit->old_values[$key] ?? '') ? ($audit->old_values[$key] ?? 'null') : json_encode($audit->old_values[$key] ?? 'null') }}</span>
                                                                    @endif
                                                                    @if(array_key_exists($key, $audit->new_values))
                                                                        <span class="audit-new">{{ is_string($audit->new_values[$key] ?? '') ? ($audit->new_values[$key] ?? 'null') : json_encode($audit->new_values[$key] ?? 'null') }}</span>
                                                                    @endif
                                                                @elseif($audit->event == 'created')
                                                                    <span class="audit-new">{{ is_string($audit->new_values[$key] ?? '') ? ($audit->new_values[$key] ?? 'null') : json_encode($audit->new_values[$key] ?? 'null') }}</span>
                                                                @elseif($audit->event == 'deleted')
                                                                    <span class="audit-old">{{ is_string($audit->old_values[$key] ?? '') ? ($audit->old_values[$key] ?? 'null') : json_encode($audit->old_values[$key] ?? 'null') }}</span>
                                                                @endif
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="px-4 py-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2"
             style="border-color: rgba(255,255,255,0.05) !important;">
            <small class="text-muted">
                Menampilkan {{ $audits->firstItem() }}–{{ $audits->lastItem() }} dari {{ $audits->total() }} entri
            </small>
            {{ $audits->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    // Toggle button text when collapse opens/closes
    document.querySelectorAll('.toggle-detail-btn').forEach(btn => {
        const target = document.querySelector(btn.getAttribute('data-bs-target'));
        if (!target) return;

        target.addEventListener('show.bs.collapse', () => {
            btn.innerHTML = '<i class="bi bi-chevron-up me-1" style="font-size:0.7rem;"></i>Sembunyikan';
            btn.classList.replace('btn-outline-secondary', 'btn-outline-primary');
        });
        target.addEventListener('hide.bs.collapse', () => {
            btn.innerHTML = '<i class="bi bi-chevron-down me-1" style="font-size:0.7rem;"></i>Lihat Detail';
            btn.classList.replace('btn-outline-primary', 'btn-outline-secondary');
        });
    });
</script>
@endsection
