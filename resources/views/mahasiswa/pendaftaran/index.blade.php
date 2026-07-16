@extends('layouts.app')

@section('title', 'Gabung Ormawa')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-bold">Pendaftaran Ormawa</h4>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <!-- Status Pendaftaran Section -->
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h5 class="fw-bold mb-0">Status Pendaftaran</h5>
                </div>
                <div class="card-body">
                    @if($pendingRequests->isEmpty())
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            <p class="mb-0 small">Belum ada pendaftaran yang sedang diproses.</p>
                        </div>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($pendingRequests as $req)
                                <div class="list-group-item px-0 py-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <h6 class="mb-0 fw-semibold">{{ $req->ormawa->nama_ormawa }}</h6>
                                        <span class="badge bg-warning text-dark rounded-pill">Pending</span>
                                    </div>
                                    <p class="mb-0 text-muted small">Dikirim: {{ $req->created_at->format('d M Y H:i') }}</p>
                                    @if($req->catatan)
                                        <p class="mb-0 mt-2 small text-muted"><i class="bi bi-chat-text me-1"></i> "{{ $req->catatan }}"</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Daftar Ormawa Section -->
        <div class="col-md-8 mb-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h5 class="fw-bold mb-0">Daftar Ormawa Tersedia</h5>
                </div>
                <div class="card-body">
                    @if($ormawas->isEmpty())
                        <div class="text-center text-muted py-5">
                            <p class="mb-0">Belum ada data Ormawa di sistem.</p>
                        </div>
                    @else
                        <div class="row row-cols-1 row-cols-md-2 g-4">
                            @foreach($ormawas as $ormawa)
                                @php
                                    $isJoined = $mahasiswa->ormawas->contains($ormawa->id);
                                    $isPending = $pendingRequests->contains('ormawa_id', $ormawa->id);
                                @endphp
                                <div class="col">
                                    <div class="card h-100 border rounded-3 {{ $isJoined ? 'bg-light' : '' }}">
                                        <div class="card-body">
                                            <h5 class="card-title fw-bold">{{ $ormawa->nama_ormawa }}</h5>
                                            <p class="card-text text-muted small mb-3">{{ Str::limit($ormawa->deskripsi ?? 'Tidak ada deskripsi.', 100) }}</p>
                                            
                                            @if($isJoined)
                                                <button class="btn btn-success btn-sm w-100" disabled>
                                                    <i class="bi bi-check-circle me-1"></i> Tergabung
                                                </button>
                                            @elseif($isPending)
                                                <button class="btn btn-warning btn-sm w-100" disabled>
                                                    <i class="bi bi-hourglass-split me-1"></i> Menunggu Konfirmasi
                                                </button>
                                            @elseif(!$ormawa->is_open_recruitment)
                                                <button class="btn btn-danger btn-sm w-100" disabled>
                                                    <i class="bi bi-door-closed-fill me-1"></i> Pendaftaran Ditutup
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-primary btn-sm w-100" data-bs-toggle="modal" data-bs-target="#daftarModal-{{ $ormawa->id }}">
                                                    <i class="bi bi-person-plus me-1"></i> Gabung Ormawa
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal Daftar -->
                                @if(!$isJoined && !$isPending && $ormawa->is_open_recruitment)
                                <div class="modal fade" id="daftarModal-{{ $ormawa->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 rounded-4 shadow">
                                            <div class="modal-header border-bottom-0 pb-0">
                                                <h5 class="modal-title fw-bold">Konfirmasi Bergabung</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('mahasiswa.pendaftaran.store') }}" method="POST">
                                                @csrf
                                                <div class="modal-body">
                                                    <p>Anda akan mendaftar ke <strong>{{ $ormawa->nama_ormawa }}</strong>.</p>
                                                    <input type="hidden" name="ormawa_id" value="{{ $ormawa->id }}">
                                                    
                                                    <div class="mb-3">
                                                        <label class="form-label text-muted small fw-semibold">Alasan Bergabung (Opsional)</label>
                                                        <textarea name="alasan" class="form-control" rows="3" placeholder="Tuliskan motivasi atau alasan Anda ingin bergabung..."></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-top-0 pt-0">
                                                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary rounded-pill px-4">Kirim Permintaan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
