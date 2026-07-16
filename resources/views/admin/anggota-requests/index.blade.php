@extends('layouts.app')
@section('title', 'Pengajuan Anggota Ormawa')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0"><i class="bi bi-person-gear text-primary me-2"></i>Pengajuan Manajemen Anggota</h4>
        <p class="text-muted mb-0">Pengajuan dari Pengurus Ormawa untuk penambahan / penghapusan anggota</p>
    </div>
    @if($pendingRequests->count() > 0)
        <span class="badge bg-danger fs-6 px-3 py-2">
            <i class="bi bi-bell-fill me-1"></i> {{ $pendingRequests->count() }} menunggu
        </span>
    @endif
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

{{-- ===== PENDING REQUESTS ===== --}}
<div class="card card-custom p-4 mb-4">
    <h5 class="fw-bold mb-3"><i class="bi bi-hourglass-split text-warning me-2"></i>Menunggu Persetujuan</h5>

    @if($pendingRequests->isEmpty())
        <div class="text-center py-5 text-muted">
            <i class="bi bi-check-circle fs-1 d-block mb-2 text-success opacity-75"></i>
            <p class="mb-0">Tidak ada pengajuan yang perlu diproses saat ini.</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Mahasiswa</th>
                        <th>Ormawa</th>
                        <th>Tipe</th>
                        <th>Pengurus</th>
                        <th>Catatan</th>
                        <th>Tanggal</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendingRequests as $req)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $req->mahasiswa->pengguna->nama }}</div>
                                <small class="text-muted">{{ $req->mahasiswa->nim }} — {{ $req->mahasiswa->prodi }}</small>
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ $req->ormawa->nama_ormawa }}</span>
                            </td>
                            <td>
                                @if($req->tipe === 'tambah')
                                    <span class="badge" style="background:#d1fae5;color:#065f46;"><i class="bi bi-person-plus-fill me-1"></i>Tambah</span>
                                @else
                                    <span class="badge" style="background:#fee2e2;color:#7f1d1d;"><i class="bi bi-person-dash-fill me-1"></i>Hapus</span>
                                @endif
                            </td>
                            <td>
                                <small>{{ $req->pengurus->nama }}</small>
                            </td>
                            <td>
                                <small class="text-muted">{{ $req->catatan ?? '-' }}</small>
                            </td>
                            <td>
                                <small class="text-muted">{{ $req->created_at->translatedFormat('d M Y') }}</small>
                            </td>
                            <td class="text-center">
                                <div class="d-flex gap-1 justify-content-center">
                                    {{-- Approve --}}
                                    <button type="button" class="btn btn-sm btn-success rounded-pill"
                                        data-bs-toggle="modal" data-bs-target="#modalApprove{{ $req->id }}">
                                        <i class="bi bi-check-lg"></i> Setujui
                                    </button>
                                    {{-- Reject --}}
                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill"
                                        data-bs-toggle="modal" data-bs-target="#modalReject{{ $req->id }}">
                                        <i class="bi bi-x-lg"></i> Tolak
                                    </button>
                                </div>
                            </td>
                        </tr>

                        {{-- Modal Approve --}}
                        <div class="modal fade" id="modalApprove{{ $req->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('admin.anggota-requests.approve', $req->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-header">
                                            <h6 class="modal-title fw-bold"><i class="bi bi-check-circle-fill text-success me-2"></i>Konfirmasi Persetujuan</h6>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p class="text-muted">Anda akan menyetujui pengajuan
                                                <strong>{{ $req->tipe === 'tambah' ? 'PENAMBAHAN' : 'PENGHAPUSAN' }}</strong> anggota:</p>
                                            <div class="alert alert-secondary py-2">
                                                <strong>{{ $req->mahasiswa->pengguna->nama }}</strong> ({{ $req->mahasiswa->nim }})<br>
                                                <small>{{ $req->tipe === 'tambah' ? 'akan ditambahkan ke' : 'akan dihapus dari' }}
                                                    <strong>{{ $req->ormawa->nama_ormawa }}</strong></small>
                                            </div>
                                            <div class="mb-0">
                                                <label class="form-label fw-semibold">Catatan Admin <small class="text-muted fw-normal">(opsional)</small></label>
                                                <textarea name="catatan_admin" class="form-control" rows="2" placeholder="Catatan atau keterangan tambahan..."></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-success rounded-pill"><i class="bi bi-check-lg me-1"></i> Setujui</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- Modal Reject --}}
                        <div class="modal fade" id="modalReject{{ $req->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('admin.anggota-requests.reject', $req->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-header">
                                            <h6 class="modal-title fw-bold"><i class="bi bi-x-circle-fill text-danger me-2"></i>Konfirmasi Penolakan</h6>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p class="text-muted">Anda akan <strong>menolak</strong> pengajuan
                                                {{ $req->tipe === 'tambah' ? 'penambahan' : 'penghapusan' }} anggota:</p>
                                            <div class="alert alert-secondary py-2">
                                                <strong>{{ $req->mahasiswa->pengguna->nama }}</strong> ({{ $req->mahasiswa->nim }}) —
                                                {{ $req->ormawa->nama_ormawa }}
                                            </div>
                                            <div class="mb-0">
                                                <label class="form-label fw-semibold">Alasan Penolakan <small class="text-muted fw-normal">(opsional)</small></label>
                                                <textarea name="catatan_admin" class="form-control" rows="2" placeholder="Jelaskan alasan penolakan..."></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-danger rounded-pill"><i class="bi bi-x-lg me-1"></i> Tolak Pengajuan</button>
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

{{-- ===== PROCESSED HISTORY ===== --}}
<div class="card card-custom p-4">
    <h5 class="fw-bold mb-3"><i class="bi bi-archive-fill text-secondary me-2"></i>Riwayat (30 Terakhir)</h5>

    @if($processedRequests->isEmpty())
        <p class="text-muted text-center py-3 mb-0">Belum ada pengajuan yang sudah diproses.</p>
    @else
        <div class="table-responsive">
            <table class="table align-middle datatable">
                <thead>
                    <tr>
                        <th>Mahasiswa</th>
                        <th>Ormawa</th>
                        <th>Tipe</th>
                        <th>Status</th>
                        <th>Diproses Oleh</th>
                        <th>Catatan Admin</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($processedRequests as $req)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $req->mahasiswa->pengguna->nama }}</div>
                                <small class="text-muted">{{ $req->mahasiswa->nim }}</small>
                            </td>
                            <td><small>{{ $req->ormawa->nama_ormawa }}</small></td>
                            <td>
                                @if($req->tipe === 'tambah')
                                    <span class="badge" style="background:#d1fae5;color:#065f46;">+ Tambah</span>
                                @else
                                    <span class="badge" style="background:#fee2e2;color:#7f1d1d;">− Hapus</span>
                                @endif
                            </td>
                            <td>
                                @if($req->status === 'disetujui')
                                    <span class="badge bg-success">Disetujui</span>
                                @else
                                    <span class="badge bg-danger">Ditolak</span>
                                @endif
                            </td>
                            <td><small>{{ $req->admin?->nama ?? '-' }}</small></td>
                            <td><small class="text-muted">{{ $req->catatan_admin ?? '-' }}</small></td>
                            <td><small class="text-muted">{{ $req->processed_at?->translatedFormat('d M Y') ?? '-' }}</small></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
