<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Keaktifan - {{ $ormawa->nama_ormawa }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10pt; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #000; padding: 6px 8px; vertical-align: middle; }
        .title  { font-size: 14px; font-weight: bold; text-align: center; border: none; background: transparent; }
        .sub    { font-size: 11px; text-align: center; border: none; background: transparent; }
        .empty  { border: none; background: transparent; }
        .center { text-align: center; }
        th { background-color: #4472C4; color: #ffffff; font-weight: bold; text-align: center; }
        tr:nth-child(even) td { background-color: #EEF2FF; }
        .status-sangat-aktif { color: #1a6b3c; font-weight: bold; }
        .status-aktif        { color: #1e40af; font-weight: bold; }
        .status-cukup        { color: #92400e; font-weight: bold; }
        .status-tidak-aktif  { color: #991b1b; font-weight: bold; }
        .info-label { font-weight: bold; background: #f3f4f6; }
    </style>
</head>
<body>
    <table>
        {{-- Judul --}}
        <tr>
            <td colspan="8" class="title">REKAP KEAKTIFAN ANGGOTA KIP-KULIAH</td>
        </tr>
        <tr>
            <td colspan="8" class="sub">{{ $ormawa->nama_ormawa }} | Periode: {{ $ormawa->periode }}</td>
        </tr>
        <tr>
            <td colspan="8" class="sub" style="font-size:9px; color:#555;">
                Dicetak: {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB | Oleh: {{ Auth::user()->nama }}
            </td>
        </tr>
        <tr>
            <td colspan="8" class="empty"></td>
        </tr>

        {{-- Info Ormawa --}}
        <tr>
            <td class="info-label">Organisasi</td>
            <td colspan="2">{{ $ormawa->nama_ormawa }}</td>
            <td class="info-label">Ketua</td>
            <td colspan="2">{{ $ormawa->ketua }}</td>
            <td class="info-label">Pembina</td>
            <td>{{ $ormawa->pembina ?? '-' }}</td>
        </tr>
        <tr>
            <td colspan="8" class="empty"></td>
        </tr>

        {{-- Summary row --}}
        @php
            $totalSangatAktif = collect($rekapData)->where('status', 'Sangat Aktif')->count();
            $totalAktif       = collect($rekapData)->where('status', 'Aktif')->count();
            $totalCukup       = collect($rekapData)->where('status', 'Cukup')->count();
            $totalTidakAktif  = collect($rekapData)->where('status', 'Tidak Aktif')->count();
            $avgPersentase    = count($rekapData) > 0 ? round(collect($rekapData)->avg('persentase')) : 0;
        @endphp
        <tr>
            <td colspan="2" class="info-label center">Total Anggota: {{ count($rekapData) }}</td>
            <td class="center" style="background:#d1fae5; color:#065f46; font-weight:bold;">Sangat Aktif: {{ $totalSangatAktif }}</td>
            <td class="center" style="background:#dbeafe; color:#1e40af; font-weight:bold;">Aktif: {{ $totalAktif }}</td>
            <td class="center" style="background:#fef3c7; color:#92400e; font-weight:bold;">Cukup: {{ $totalCukup }}</td>
            <td class="center" style="background:#fee2e2; color:#991b1b; font-weight:bold;">Tidak Aktif: {{ $totalTidakAktif }}</td>
            <td colspan="2" class="center" style="background:#ede9fe; color:#4f46e5; font-weight:bold;">Rata-rata: {{ $avgPersentase }}%</td>
        </tr>
        <tr>
            <td colspan="8" class="empty"></td>
        </tr>

        {{-- Header Tabel --}}
        <tr>
            <th style="width:40px;">No</th>
            <th style="width:180px;">Nama Mahasiswa</th>
            <th style="width:120px;">NIM</th>
            <th style="width:200px;">Program Studi</th>
            <th style="width:100px;">Total Kegiatan</th>
            <th style="width:120px;">Poin Didapat</th>
            <th style="width:100px;">Kehadiran (%)</th>
            <th style="width:120px;">Status Keaktifan</th>
        </tr>

        {{-- Data Rows --}}
        @php $no = 1; @endphp
        @forelse($rekapData as $row)
            @php
                $statusClass = match($row['status']) {
                    'Sangat Aktif' => 'status-sangat-aktif',
                    'Aktif'        => 'status-aktif',
                    'Cukup'        => 'status-cukup',
                    default        => 'status-tidak-aktif',
                };
            @endphp
            <tr>
                <td class="center">{{ $no++ }}</td>
                <td>{{ $row['nama'] }}</td>
                {{-- mso-number-format prevents NIM from being converted to scientific notation --}}
                <td class="center" style="mso-number-format:'\@';">{{ $row['nim'] }}</td>
                <td>{{ $row['prodi'] }}</td>
                <td class="center">{{ $row['total_kegiatan'] }}</td>
                <td class="center">{{ $row['total_poin'] }} / {{ $row['total_poin_maks'] ?? 0 }}</td>
                <td class="center">{{ $row['persentase'] }}%</td>
                <td class="center {{ $statusClass }}">{{ strtoupper($row['status']) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="center" style="color:#9ca3af; padding:16px;">
                    Belum ada anggota KIP-K yang terdaftar.
                </td>
            </tr>
        @endforelse

        {{-- Footer note --}}
        <tr>
            <td colspan="8" class="empty"></td>
        </tr>
        <tr>
            <td colspan="8" style="border:none; font-size:8pt; color:#6b7280; font-style:italic;">
                * Poin Izin/Sakit dihitung setengah (50%) dari poin kehadiran penuh.
                * Laporan otomatis dari Sistem Monitoring KIP-K.
            </td>
        </tr>
    </table>
</body>
</html>
