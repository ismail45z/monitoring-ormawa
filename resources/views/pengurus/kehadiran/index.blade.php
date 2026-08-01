@extends('layouts.app')
@section('title', 'Verifikasi Kehadiran')

@section('content')
<div class="card card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h4 class="fw-bold mb-0"><i class="bi bi-check-circle-fill text-primary me-2"></i>Verifikasi Kehadiran Mahasiswa KIP-K</h4>
        <div>
            <button type="button" id="btnBulkApprove" class="btn btn-success rounded-pill px-4 shadow-sm disabled" onclick="bulkApprove()">
                <i class="bi bi-check-all me-1"></i>Setujui Massal (<span id="selectedCount">0</span>)
            </button>
        </div>
    </div>

    <!-- Alert Placeholder for Floating Toast notifications -->
    <div id="ajaxAlertContainer" class="position-fixed bottom-0 end-0 p-3" style="z-index: 1080;"></div>

    <div class="table-responsive">
        <table class="table align-middle" id="attendanceTable">
            <thead>
                <tr>
                    <th width="40"><input type="checkbox" id="selectAll" class="form-check-input"></th>
                    <th>Nama Mahasiswa</th>
                    <th>NIM</th>
                    <th>Kegiatan</th>
                    <th>Tanggal Kegiatan</th>
                    <th>Waktu Pencatatan</th>
                    <th>Pilihan Kehadiran</th>
                    <th>Keterangan Mandiri</th>
                    <th>Bukti Foto</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kehadirans as $kh)
                    <tr id="row-{{ $kh->id }}">
                        <td>
                            <input type="checkbox" class="form-check-input attendance-checkbox" value="{{ $kh->id }}" onchange="updateSelectedCount()">
                        </td>
                        <td><span class="fw-semibold text-dark-toggle">{{ $kh->keanggotaan->mahasiswa->pengguna->nama }}</span></td>
                        <td>{{ $kh->keanggotaan->mahasiswa->nim }}</td>
                        <td>{{ $kh->kegiatan->nama_kegiatan }}</td>
                        <td>{{ \Carbon\Carbon::parse($kh->kegiatan->tanggal)->translatedFormat('d M Y') }}</td>
                        <td>
                            <small class="text-muted d-block">Mencatat pada:</small>
                            {{ $kh->created_at->translatedFormat('d M Y, H:i') }}
                        </td>
                        <td>
                            @if($kh->status_kehadiran == 'Hadir')
                                <span class="badge bg-success">Hadir</span>
                            @elseif($kh->status_kehadiran == 'Tidak Hadir')
                                <span class="badge bg-danger">Tidak Hadir</span>
                            @else
                                <span class="badge bg-warning text-dark">Izin</span>
                            @endif
                        </td>
                        <td>{{ $kh->keterangan ?? '-' }}</td>
                        <td>
                            @if($kh->bukti_foto)
                                <div class="d-flex justify-content-center">
                                    <img src="{{ route('secure.file', ['path' => $kh->bukti_foto]) }}" 
                                         alt="Bukti Foto" 
                                         class="bukti-foto-thumb rounded shadow-sm border border-2 border-primary" 
                                         style="width: 44px; height: 44px; object-fit: cover; cursor: pointer; transition: transform 0.2s;"
                                         title="Klik untuk memperbesar"
                                         onmouseover="this.style.transform='scale(1.15)'"
                                         onmouseout="this.style.transform='scale(1)'"
                                         onclick="showQuickPhoto('{{ route('secure.file', ['path' => $kh->bukti_foto]) }}', '{{ $kh->keanggotaan->mahasiswa->pengguna->nama }}')">
                                </div>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td>
                            <!-- Tombol Verifikasi (Modal Trigger) -->
                            <button type="button" class="btn btn-sm btn-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#verifikasiModal{{ $kh->id }}">
                                <i class="bi bi-shield-check"></i> Verifikasi
                            </button>
                            
                            <!-- Modal Verifikasi -->
                            <div class="modal fade" id="verifikasiModal{{ $kh->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Verifikasi Kehadiran</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body text-start">
                                            <p>Mahasiswa: <strong>{{ $kh->keanggotaan->mahasiswa->pengguna->nama }}</strong><br>
                                            Kegiatan: {{ $kh->kegiatan->nama_kegiatan }}</p>
                                            
                                            <!-- Setuju Form -->
                                            <form action="{{ route('pengurus.kehadiran.approve', $kh->id) }}" method="POST" class="ajax-verify-form mb-4 border-bottom pb-4">
                                                @csrf
                                                <div class="mb-2">
                                                    <label class="form-label text-success fw-bold"><i class="bi bi-check-circle"></i> Opsi Setuju</label>
                                                    <textarea name="keterangan_verifikasi" class="form-control" rows="2" placeholder="Catatan persetujuan (Opsional)"></textarea>
                                                </div>
                                                <button type="submit" class="btn btn-success w-100"><i class="bi bi-check-lg"></i> Setujui Kehadiran</button>
                                            </form>
                                            
                                            <!-- Tolak Form -->
                                            <form action="{{ route('pengurus.kehadiran.reject', $kh->id) }}" method="POST" class="ajax-verify-form" onsubmit="return confirm('Apakah Anda yakin ingin MENOLAK kehadiran ini?')">
                                                @csrf
                                                <div class="mb-2">
                                                    <label class="form-label text-danger fw-bold"><i class="bi bi-x-circle"></i> Opsi Tolak</label>
                                                    <textarea name="keterangan_verifikasi" class="form-control" rows="2" placeholder="Alasan penolakan (Wajib diisi)" required></textarea>
                                                </div>
                                                <button type="submit" class="btn btn-outline-danger w-100"><i class="bi bi-x-lg"></i> Tolak Kehadiran</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted py-4">Semua absensi telah diverifikasi!</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Quick Photo Preview -->
<div class="modal fade" id="quickPhotoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border-0 rounded-4 overflow-hidden shadow-lg">
            <!-- Header -->
            <div class="modal-header border-0 pb-0" style="background: linear-gradient(135deg, #1e3a5f, #0d6efd);">
                <div class="d-flex align-items-center gap-2">
                    <div class="p-2 rounded-circle bg-white bg-opacity-25">
                        <i class="bi bi-camera-fill text-white"></i>
                    </div>
                    <div>
                        <h6 class="modal-title text-white fw-bold mb-0">Bukti Foto Kehadiran</h6>
                        <small id="quickPhotoTitle" class="text-white text-opacity-75"></small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <!-- Body -->
            <div class="modal-body p-0 text-center bg-dark" style="min-height: 300px;">
                <!-- Loading spinner -->
                <div id="quickPhotoLoading" class="d-flex align-items-center justify-content-center" style="height: 300px;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                <img src="" id="quickPhotoImg"
                     class="img-fluid w-100 d-none"
                     style="max-height: 70vh; object-fit: contain;"
                     onload="document.getElementById('quickPhotoLoading').classList.add('d-none'); this.classList.remove('d-none');"
                     alt="Bukti Foto">
            </div>
            <!-- Footer -->
            <div class="modal-footer border-0 bg-dark justify-content-between py-2 px-3">
                <span class="text-white text-opacity-50 small"><i class="bi bi-shield-check me-1"></i>Dokumen Kehadiran Terverifikasi</span>
                <a id="quickPhotoDownload" href="#" target="_blank" class="btn btn-sm btn-outline-light rounded-pill px-3">
                    <i class="bi bi-download me-1"></i>Unduh
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Quick Photo Preview
    function showQuickPhoto(url, name) {
        // Reset state
        const img = document.getElementById('quickPhotoImg');
        const loading = document.getElementById('quickPhotoLoading');
        img.classList.add('d-none');
        loading.classList.remove('d-none');

        // Set content
        img.src = url;
        document.getElementById('quickPhotoTitle').innerText = name;
        document.getElementById('quickPhotoDownload').href = url;

        const modal = new bootstrap.Modal(document.getElementById('quickPhotoModal'), {
            backdrop: true,
            keyboard: true
        });
        modal.show();
    }

    // Toggle Select All
    $('#selectAll').on('change', function() {
        $('.attendance-checkbox').prop('checked', this.checked);
        updateSelectedCount();
    });

    // Update selected count & enable/disable button
    function updateSelectedCount() {
        const selected = $('.attendance-checkbox:checked').length;
        $('#selectedCount').text(selected);
        
        if (selected > 0) {
            $('#btnBulkApprove').removeClass('disabled');
        } else {
            $('#btnBulkApprove').addClass('disabled');
            $('#selectAll').prop('checked', false);
        }
    }

    // Show floating alert (toast-like)
    function showFloatingAlert(type, message) {
        const alertClass = type === 'success' ? 'bg-success text-white' : 'bg-danger text-white';
        const icon = type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill';
        
        const alertHtml = `
            <div class="toast show align-items-center ${alertClass} border-0 shadow" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi ${icon} me-2"></i> ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;
        
        const container = $('#ajaxAlertContainer');
        container.append(alertHtml);
        
        // Auto remove toast after 4 seconds
        setTimeout(() => {
            container.find('.toast').first().fadeOut(300, function() {
                $(this).remove();
            });
        }, 4000);
    }

    // Update Notification Badge in Sidebar
    function updateNotificationBadge(decrementBy = 1) {
        const badge = $('.sidebar-attendance-badge');
        if (badge.length > 0) {
            let currentCount = parseInt(badge.text()) || 0;
            let newCount = currentCount - decrementBy;
            if (newCount > 0) {
                badge.text(newCount);
            } else {
                badge.remove();
            }
        }
    }

    // Individual AJAX Verification Form Submit
    $(document).ready(function() {
        // Handle select all change state when row checkboxes are checked
        $(document).on('change', '.attendance-checkbox', function() {
            const allCheckboxCount = $('.attendance-checkbox').length;
            const checkedCheckboxCount = $('.attendance-checkbox:checked').length;
            $('#selectAll').prop('checked', allCheckboxCount === checkedCheckboxCount);
        });

        // Intercept form submit
        $(document).on('submit', '.ajax-verify-form', function(e) {
            e.preventDefault();
            const form = $(this);
            const url = form.attr('action');
            const data = form.serialize();
            const modalElement = form.closest('.modal');
            const modalId = modalElement.attr('id');
            const row = form.closest('tr');
            
            // Disable buttons to prevent double click
            form.find('button[type="submit"]').addClass('disabled');

            $.ajax({
                url: url,
                method: 'POST',
                data: data,
                success: function(response) {
                    // Close modal using bootstrap API
                    const modalInstance = bootstrap.Modal.getInstance(document.getElementById(modalId));
                    if (modalInstance) {
                        modalInstance.hide();
                    }
                    
                    // Remove row with slideUp/fadeOut
                    row.fadeOut(400, function() {
                        $(this).remove();
                        updateSelectedCount();
                        // Check if no more records
                        if ($('#attendanceTable tbody tr[id^="row-"]').length === 0) {
                            $('#attendanceTable tbody').html('<tr><td colspan="10" class="text-center text-muted py-4">Semua absensi telah diverifikasi!</td></tr>');
                        }
                    });
                    
                    showFloatingAlert('success', response.message || 'Verifikasi berhasil disimpan.');
                    updateNotificationBadge(1);
                },
                error: function(xhr) {
                    form.find('button[type="submit"]').removeClass('disabled');
                    const errorMsg = xhr.responseJSON ? xhr.responseJSON.message : 'Gagal memproses verifikasi. Silakan coba lagi.';
                    showFloatingAlert('danger', errorMsg);
                }
            });
        });
    });

    // Bulk Approve AJAX Submit
    function bulkApprove() {
        const selectedIds = [];
        $('.attendance-checkbox:checked').each(function() {
            selectedIds.push($(this).val());
        });
        
        if (selectedIds.length === 0) return;
        
        if (!confirm(`Setujui ${selectedIds.length} absensi yang terpilih secara massal?`)) return;
        
        const btn = $('#btnBulkApprove');
        btn.addClass('disabled');
        
        $.ajax({
            url: "{{ route('pengurus.kehadiran.bulk-approve') }}",
            method: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                ids: selectedIds
            },
            success: function(response) {
                if (response.success) {
                    // Remove selected rows
                    selectedIds.forEach(id => {
                        $(`#row-${id}`).fadeOut(400, function() {
                            $(this).remove();
                            if ($('#attendanceTable tbody tr[id^="row-"]').length === 0) {
                                $('#attendanceTable tbody').html('<tr><td colspan="10" class="text-center text-muted py-4">Semua absensi telah diverifikasi!</td></tr>');
                            }
                        });
                    });
                    
                    // Reset select all and count
                    $('#selectAll').prop('checked', false);
                    setTimeout(() => {
                        updateSelectedCount();
                    }, 500);
                    
                    showFloatingAlert('success', response.message);
                    updateNotificationBadge(selectedIds.length);
                } else {
                    btn.removeClass('disabled');
                    showFloatingAlert('danger', response.message);
                }
            },
            error: function() {
                btn.removeClass('disabled');
                showFloatingAlert('danger', 'Terjadi kesalahan saat memproses secara massal.');
            }
        });
    }
</script>
@endsection
