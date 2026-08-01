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

    @if(!$isProfileComplete)
        <div class="alert alert-warning shadow-sm border-warning border-opacity-50" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-exclamation-triangle fs-3 text-warning me-3"></i>
                <div>
                    <h6 class="alert-heading fw-bold mb-1">Profil Anda Belum Lengkap!</h6>
                    <p class="mb-0 small">Anda wajib melengkapi data Jurusan, Program Studi, dan Nomor KIP-Kuliah sebelum dapat mendaftar ke Ormawa manapun. <a href="{{ route('profile.edit') }}" class="fw-bold text-decoration-underline text-warning-emphasis">Lengkapi Profil Sekarang</a></p>
                </div>
            </div>
        </div>
    @endif

    <div class="row">
        <!-- Status Pendaftaran Section -->
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h5 class="fw-bold mb-0">Riwayat Pendaftaran</h5>
                </div>
                <div class="card-body">
                    @if($riwayatRequests->isEmpty())
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            <p class="mb-0 small">Belum ada riwayat pendaftaran.</p>
                        </div>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($riwayatRequests as $req)
                                <div class="list-group-item px-0 py-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <h6 class="mb-0 fw-semibold">{{ $req->ormawa->nama_ormawa }}</h6>
                                        @if($req->status === 'pending')
                                            <span class="badge bg-warning text-dark rounded-pill">Pending</span>
                                        @elseif($req->status === 'disetujui')
                                            <span class="badge bg-success rounded-pill">Disetujui</span>
                                        @elseif($req->status === 'ditolak')
                                            <span class="badge bg-danger rounded-pill">Ditolak</span>
                                        @endif
                                    </div>
                                    <p class="mb-0 text-muted small">Dikirim: {{ $req->created_at->format('d M Y H:i') }}</p>
                                    @if($req->catatan_pengurus && $req->status === 'ditolak')
                                        <div class="mt-2 p-2 bg-danger bg-opacity-10 border border-danger border-opacity-25 rounded-3">
                                            <p class="mb-0 small text-danger"><i class="bi bi-info-circle-fill me-1"></i> <strong>Alasan:</strong> {{ $req->catatan_pengurus }}</p>
                                        </div>
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
                                    
                                    $ormawaRequests = $riwayatRequests->where('ormawa_id', $ormawa->id)->sortByDesc('created_at');
                                    $isPending = $ormawaRequests->where('status', 'pending')->isNotEmpty();
                                    
                                    $latestRejected = $ormawaRequests->where('status', 'ditolak')->first();
                                    $isPermanen = false;
                                    $sisaCooldown = 0;

                                    if ($latestRejected) {
                                        if ($latestRejected->is_permanen) {
                                            $isPermanen = true;
                                        } elseif ($latestRejected->cooldown_hari !== null && $latestRejected->cooldown_hari > 0) {
                                            $processedAt = \Carbon\Carbon::parse($latestRejected->processed_at ?? $latestRejected->updated_at);
                                            $daysPassed = $processedAt->diffInDays(now());
                                            if ($daysPassed < $latestRejected->cooldown_hari) {
                                                $sisaCooldown = $latestRejected->cooldown_hari - (int) $daysPassed;
                                            }
                                        }
                                    }

                                    $isJurusanBlocked = $ormawa->kategori_jurusan && $mahasiswa->jurusan !== $ormawa->kategori_jurusan;
                                    $isProdiBlocked = $ormawa->kategori_prodi && $mahasiswa->prodi !== $ormawa->kategori_prodi;
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
                                            @elseif($isPermanen)
                                                <button class="btn btn-danger btn-sm w-100" disabled>
                                                    <i class="bi bi-x-circle me-1"></i> Ditolak Permanen
                                                </button>
                                            @elseif($sisaCooldown > 0)
                                                <button class="btn btn-danger btn-sm w-100" disabled>
                                                    <i class="bi bi-clock-history me-1"></i> Ditolak (Jeda {{ $sisaCooldown }} Hari)
                                                </button>
                                            @elseif(!$ormawa->is_open_recruitment)
                                                <button class="btn btn-secondary btn-sm w-100" disabled>
                                                    <i class="bi bi-door-closed-fill me-1"></i> Pendaftaran Ditutup
                                                </button>
                                            @elseif(!$isProfileComplete)
                                                <a href="{{ route('profile.edit') }}" class="btn btn-warning btn-sm w-100">
                                                    <i class="bi bi-person-exclamation me-1"></i> Lengkapi Profil Dulu
                                                </a>
                                            @elseif($isJurusanBlocked)
                                                <button class="btn btn-secondary btn-sm w-100" disabled title="Khusus Mahasiswa Jurusan {{ $ormawa->kategori_jurusan }}">
                                                    <i class="bi bi-shield-lock-fill me-1"></i> Khusus Jurusan {{ $ormawa->kategori_jurusan }}
                                                </button>
                                            @elseif($isProdiBlocked)
                                                <button class="btn btn-secondary btn-sm w-100" disabled title="Khusus Mahasiswa Program Studi {{ $ormawa->kategori_prodi }}">
                                                    <i class="bi bi-shield-lock-fill me-1"></i> Khusus Prodi {{ $ormawa->kategori_prodi }}
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
                                @if(!$isJoined && !$isPending && !$isPermanen && $sisaCooldown == 0 && $ormawa->is_open_recruitment && $isProfileComplete && !$isJurusanBlocked && !$isProdiBlocked)
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

                        {{-- Pagination Ormawa --}}
                        @if($ormawas->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $ormawas->links() }}
                        </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
