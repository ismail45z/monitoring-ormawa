@extends('layouts.app')
@section('title', 'Audit Log (Riwayat Perubahan)')

@section('styles')
<style>
    .audit-old { background-color: rgba(239, 68, 68, 0.15); color: #ef4444; text-decoration: line-through; padding: 0.2rem 0.4rem; border-radius: 4px; display: block; word-break: break-all; font-family: monospace; font-size: 0.8rem; margin-bottom: 0.25rem; border: 1px solid rgba(239, 68, 68, 0.2); }
    .audit-new { background-color: rgba(16, 185, 129, 0.15); color: #10b981; padding: 0.2rem 0.4rem; border-radius: 4px; display: block; word-break: break-all; font-family: monospace; font-size: 0.8rem; border: 1px solid rgba(16, 185, 129, 0.2); }
    .audit-value { font-family: monospace; word-break: break-all; font-size: 0.8rem; }
    .table-audit th { background-color: rgba(255, 255, 255, 0.05); font-weight: 600; text-transform: uppercase; font-size: 0.75rem; color: #94a3b8; }
    .table-audit td { vertical-align: top; }
    .table-detail-audit { background: transparent; }
    .table-detail-audit td { padding: 0.5rem; border-color: rgba(255,255,255,0.1); }
    .table-detail-audit .audit-key { background-color: rgba(255,255,255,0.03); color: #94a3b8; font-weight: 600; font-size: 0.8rem; text-transform: capitalize; }
</style>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0"><i class="bi bi-shield-lock-fill text-primary me-2"></i>Audit Log</h4>
        <p class="text-muted mb-0">Riwayat perubahan data pada sistem</p>
    </div>
</div>

<div class="card card-custom p-4 mb-4">
    <form action="{{ route('admin.audit.index') }}" method="GET" class="row g-3 align-items-end">
        <div class="col-md-3">
            <label class="form-label fw-semibold small">Filter Model</label>
            <select name="model" class="form-select form-select-sm">
                <option value="">Semua Model</option>
                <option value="Pengguna" {{ request('model') == 'Pengguna' ? 'selected' : '' }}>Pengguna</option>
                <option value="Mahasiswa" {{ request('model') == 'Mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                <option value="Ormawa" {{ request('model') == 'Ormawa' ? 'selected' : '' }}>Ormawa</option>
                <option value="Kegiatan" {{ request('model') == 'Kegiatan' ? 'selected' : '' }}>Kegiatan</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label fw-semibold small">Filter Event</label>
            <select name="event" class="form-select form-select-sm">
                <option value="">Semua Event</option>
                <option value="created" {{ request('event') == 'created' ? 'selected' : '' }}>Created</option>
                <option value="updated" {{ request('event') == 'updated' ? 'selected' : '' }}>Updated</option>
                <option value="deleted" {{ request('event') == 'deleted' ? 'selected' : '' }}>Deleted</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-sm btn-primary w-100"><i class="bi bi-search me-1"></i> Filter</button>
        </div>
        <div class="col-md-2">
            <a href="{{ route('admin.audit.index') }}" class="btn btn-sm btn-light w-100">Reset</a>
        </div>
    </form>
</div>

<div class="card card-custom p-4">
    @if($audits->isEmpty())
        <div class="text-center py-5 text-muted">
            <i class="bi bi-clock-history fs-1 d-block mb-2 opacity-50"></i>
            Belum ada riwayat perubahan data.
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover table-audit">
                <thead>
                    <tr>
                        <th width="15%">Waktu</th>
                        <th width="15%">Pelaku (User)</th>
                        <th width="10%">Event</th>
                        <th width="15%">Model</th>
                        <th width="45%">Detail Perubahan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($audits as $audit)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $audit->created_at->translatedFormat('d M Y') }}</div>
                                <small class="text-muted">{{ $audit->created_at->format('H:i:s') }}</small>
                            </td>
                            <td>
                                @if($audit->user)
                                    <strong>{{ $audit->user->nama }}</strong><br>
                                    <small class="text-muted">{{ $audit->user->role }}</small>
                                @else
                                    <span class="text-muted fst-italic">Sistem / Guest</span>
                                @endif
                                <br><small class="text-muted" style="font-size: 0.65rem;">IP: {{ $audit->ip_address }}</small>
                            </td>
                            <td>
                                @if($audit->event == 'created')
                                    <span class="badge bg-success">Created</span>
                                @elseif($audit->event == 'updated')
                                    <span class="badge bg-warning text-dark">Updated</span>
                                @elseif($audit->event == 'deleted')
                                    <span class="badge bg-danger">Deleted</span>
                                @else
                                    <span class="badge bg-secondary">{{ $audit->event }}</span>
                                @endif
                            </td>
                            <td>
                                <div>{{ class_basename($audit->auditable_type) }}</div>
                                <small class="text-muted">ID: {{ $audit->auditable_id }}</small>
                            </td>
                            <td>
                                @if(empty($audit->old_values) && empty($audit->new_values))
                                    <span class="text-muted fst-italic">-</span>
                                @else
                                    <div style="max-height: 250px; overflow-y: auto; overflow-x: hidden; border-radius: 8px; border: 1px solid rgba(255,255,255,0.05);">
                                        <table class="table table-sm table-bordered table-detail-audit mb-0" style="table-layout: fixed; width: 100%;">
                                            @php
                                                $allKeys = array_unique(array_merge(array_keys($audit->old_values), array_keys($audit->new_values)));
                                                // Exclude some internal fields if necessary
                                                $allKeys = array_filter($allKeys, fn($k) => !in_array($k, ['created_at', 'updated_at', 'remember_token']));
                                            @endphp
                                            @foreach($allKeys as $key)
                                                <tr>
                                                    <td width="30%" class="audit-key">{{ str_replace('_', ' ', $key) }}</td>
                                                    <td width="70%">
                                                        @if($audit->event == 'updated')
                                                            <div class="audit-old">{{ is_string($audit->old_values[$key] ?? '') ? $audit->old_values[$key] ?? 'null' : json_encode($audit->old_values[$key] ?? 'null') }}</div>
                                                            <div class="audit-new">{{ is_string($audit->new_values[$key] ?? '') ? $audit->new_values[$key] ?? 'null' : json_encode($audit->new_values[$key] ?? 'null') }}</div>
                                                        @elseif($audit->event == 'created')
                                                            <div class="audit-new">{{ is_string($audit->new_values[$key] ?? '') ? $audit->new_values[$key] ?? 'null' : json_encode($audit->new_values[$key] ?? 'null') }}</div>
                                                        @elseif($audit->event == 'deleted')
                                                            <div class="audit-old">{{ is_string($audit->old_values[$key] ?? '') ? $audit->old_values[$key] ?? 'null' : json_encode($audit->old_values[$key] ?? 'null') }}</div>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </table>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="mt-4 d-flex justify-content-end">
            {{ $audits->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
