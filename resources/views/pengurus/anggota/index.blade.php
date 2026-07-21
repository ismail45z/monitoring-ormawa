@extends('layouts.app')
@section('title', 'Manajemen Anggota')

@section('styles')
<style>
    .request-badge-tambah { background: #d1fae5; color: #065f46; }
    .timeline-item { border-left: 3px solid #e2e8f0; padding-left: 1rem; margin-left: 0.5rem; position: relative; }
    .timeline-item::before { content: ''; width: 10px; height: 10px; border-radius: 50%; background: #4f46e5; position: absolute; left: -7px; top: 4px; }
    .timeline-item.disetujui::before { background: #10b981; }
    .timeline-item.ditolak::before  { background: #ef4444; }
    .timeline-item.pending::before  { background: #f59e0b; }
</style>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0"><i class="bi bi-people-fill text-primary me-2"></i>Manajemen Anggota</h4>
        <p class="text-muted mb-0">{{ $ormawa->nama_ormawa }}</p>
    </div>
    @if($pendingRequests->count() > 0)
        <span class="badge bg-warning text-dark px-3 py-2 fs-6">
            <i class="bi bi-person-plus-fill me-1"></i> {{ $pendingRequests->count() }} Pendaftaran Baru
        </span>
    @endif
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="row g-4">

    {{-- ===== COLUMN 1: Current Members & Pending Requests ===== --}}
    <div class="col-lg-7">
        
        {{-- PENDING REQUESTS --}}
        @if($pendingRequests->count() > 0)
        <div class="card card-custom p-4 mb-4 border-warning border-start border-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0"><i class="bi bi-person-lines-fill text-warning me-2"></i>Permintaan Bergabung</h5>
            </div>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Mahasiswa</th>
                            <th>NIM / Prodi</th>
                            <th>Pesan</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingRequests as $req)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $req->mahasiswa->pengguna->nama }}</div>
                                    <small class="text-muted">Angkatan {{ $req->mahasiswa->angkatan }}</small>
                                </td>
                                <td>
                                    <div>{{ $req->mahasiswa->nim }}</div>
                                    <small class="text-muted">{{ $req->mahasiswa->prodi }}</small>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $req->catatan ?? '-' }}</small>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-success rounded-pill px-3"
                                        data-bs-toggle="modal" data-bs-target="#modalApprove{{ $req->id }}">
                                        Setujui
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger rounded-pill px-3 mt-1"
                                        data-bs-toggle="modal" data-bs-target="#modalReject{{ $req->id }}">
                                        Tolak
                                    </button>
                                </td>
                            </tr>

                            {{-- Modal Approve --}}
                            <div class="modal fade" id="modalApprove{{ $req->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('pengurus.anggota.approve', $req->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-header">
                                                <h6 class="modal-title fw-bold">Setujui Pendaftaran</h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Terima <strong>{{ $req->mahasiswa->pengguna->nama }}</strong> sebagai anggota?</p>
                                                <div class="mb-2">
                                                    <label class="form-label small text-muted">Catatan/Pesan (Opsional)</label>
                                                    <textarea name="catatan_pengurus" class="form-control form-control-sm" rows="2" placeholder="Selamat bergabung..."></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer pt-0 border-top-0">
                                                <button type="button" class="btn btn-sm btn-light rounded-pill" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-sm btn-success rounded-pill px-4">Terima</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            {{-- Modal Reject --}}
                            <div class="modal fade" id="modalReject{{ $req->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('pengurus.anggota.reject', $req->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-header">
                                                <h6 class="modal-title fw-bold">Tolak Pendaftaran</h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Tolak <strong>{{ $req->mahasiswa->pengguna->nama }}</strong>?</p>
                                                <div class="mb-3">
                                                    <label class="form-label small text-muted">Alasan Penolakan (Opsional)</label>
                                                    <textarea name="catatan_pengurus" class="form-control form-control-sm" rows="2" placeholder="Kapasitas penuh..."></textarea>
                                                </div>
                                                <div class="mb-3">
                                                    <div class="form-check form-switch mb-2">
                                                        <input class="form-check-input" type="checkbox" name="is_permanen" id="isPermanen{{ $req->id }}" value="1" onchange="toggleCooldownInput({{ $req->id }})">
                                                        <label class="form-check-label small" for="isPermanen{{ $req->id }}">Tolak Secara Permanen</label>
                                                    </div>
                                                </div>
                                                <div class="mb-2" id="cooldownDiv{{ $req->id }}">
                                                    <label class="form-label small text-muted">Jeda Waktu Daftar Ulang (Hari)</label>
                                                    <input type="number" name="cooldown_hari" id="cooldownInput{{ $req->id }}" class="form-control form-control-sm" placeholder="Kosongkan jika bisa langsung daftar" min="0">
                                                    <small class="text-muted" style="font-size: 0.75rem;">Mahasiswa tidak bisa mendaftar ulang sebelum jeda berakhir.</small>
                                                </div>
                                            </div>
                                            <div class="modal-footer pt-0 border-top-0">
                                                <button type="button" class="btn btn-sm btn-light rounded-pill" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-sm btn-danger rounded-pill px-4">Tolak</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- CURRENT MEMBERS --}}
        <div class="card card-custom p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0"><i class="bi bi-person-check-fill text-success me-2"></i>Anggota Aktif ({{ $anggota->count() }})</h5>
            </div>

            @if($anggota->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-person-x fs-1 d-block mb-2 opacity-50"></i>
                    Belum ada anggota di Ormawa ini.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Mahasiswa</th>
                                <th>NIM / Prodi</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($anggota as $mhs)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $mhs->pengguna->nama }}</div>
                                        <small class="text-muted">Angkatan {{ $mhs->angkatan }}</small>
                                    </td>
                                    <td>
                                        <div>{{ $mhs->nim }}</div>
                                        <small class="text-muted">{{ $mhs->prodi }}</small>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill"
                                            data-bs-toggle="modal" data-bs-target="#modalHapus{{ $mhs->id }}">
                                            <i class="bi bi-person-dash me-1"></i> Keluarkan
                                        </button>
                                    </td>
                                </tr>

                                {{-- Modal Keluarkan --}}
                                <div class="modal fade" id="modalHapus{{ $mhs->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('pengurus.anggota.hapus', $mhs->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-header">
                                                    <h6 class="modal-title fw-bold"><i class="bi bi-person-dash text-danger me-2"></i>Keluarkan Anggota</h6>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p class="text-muted">Anda akan mengeluarkan <strong>{{ $mhs->pengguna->nama }}</strong> dari keanggotaan Ormawa.</p>
                                                    <p class="small text-danger">Tindakan ini akan langsung menghapus relasi mahasiswa ini dengan Ormawa Anda.</p>
                                                </div>
                                                <div class="modal-footer pt-0 border-top-0">
                                                    <button type="button" class="btn btn-sm btn-light rounded-pill" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-sm btn-danger rounded-pill px-4">Keluarkan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- ===== COLUMN 2: History ===== --}}
    <div class="col-lg-5">
        <div class="card card-custom p-4 h-100">
            <h5 class="fw-bold mb-4"><i class="bi bi-clock-history text-secondary me-2"></i>Riwayat Pendaftaran</h5>
            
            @if($processedRequests->isEmpty())
                <div class="text-center text-muted py-4">
                    <p class="small mb-0">Belum ada riwayat pendaftaran mahasiswa.</p>
                </div>
            @else
                <div class="timeline-container ps-2 mt-2">
                    @foreach($processedRequests as $history)
                        @php
                            $isApproved = $history->status === 'disetujui';
                        @endphp
                        <div class="timeline-item mb-4 {{ $history->status }}">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="badge {{ $isApproved ? 'bg-success' : 'bg-danger' }} rounded-pill small">
                                    {{ ucfirst($history->status) }}
                                </span>
                                <small class="text-muted" style="font-size: 0.7rem;">{{ $history->processed_at->diffForHumans() }}</small>
                            </div>
                            
                            <p class="mb-1 small fw-semibold text-dark">
                                {{ $history->mahasiswa->pengguna->nama }} mendaftar
                            </p>
                            
                            <div class="bg-light p-2 rounded-3 mt-2" style="font-size: 0.75rem;">
                                <div class="text-muted mb-1"><i class="bi bi-person-badge me-1"></i>Diproses oleh: {{ $history->pemroses->nama ?? 'Sistem' }}</div>
                                @if($history->catatan_pengurus)
                                    <div><i class="bi bi-chat-left-text me-1"></i>Pesan: "{{ $history->catatan_pengurus }}"</div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function toggleCooldownInput(reqId) {
        const isPermanen = document.getElementById('isPermanen' + reqId).checked;
        const cooldownInput = document.getElementById('cooldownInput' + reqId);
        
        if (isPermanen) {
            cooldownInput.disabled = true;
            cooldownInput.value = '';
        } else {
            cooldownInput.disabled = false;
        }
    }
</script>
@endsection
