<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Keaktifan - {{ $ormawa->nama_ormawa }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11pt;
            color: #1a202c;
            line-height: 1.5;
        }
        .header {
            text-align: center;
            margin-bottom: 24px;
            padding-bottom: 12px;
            border-bottom: 3px solid #4f46e5;
        }
        .header h2 {
            margin: 0 0 4px 0;
            font-size: 16pt;
            text-transform: uppercase;
            color: #1e1b4b;
            letter-spacing: 0.5px;
        }
        .header .subtitle {
            margin: 0;
            font-size: 10pt;
            color: #6b7280;
        }
        .ormawa-badge {
            display: inline-block;
            background-color: #ede9fe;
            color: #4f46e5;
            border: 1px solid #c4b5fd;
            border-radius: 6px;
            padding: 3px 12px;
            font-size: 10pt;
            font-weight: bold;
            margin-top: 8px;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 10pt;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
        }
        .meta-table td {
            padding: 5px 12px;
        }
        .meta-table .label {
            color: #6b7280;
            font-weight: bold;
            width: 18%;
            white-space: nowrap;
        }
        .meta-table .value {
            color: #111827;
            width: 32%;
        }
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
        .report-table th {
            background-color: #4f46e5;
            color: #ffffff;
            padding: 8px 10px;
            text-align: left;
            font-size: 9pt;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }
        .report-table td {
            border: 1px solid #e5e7eb;
            padding: 7px 10px;
            font-size: 9pt;
        }
        .report-table tr:nth-child(even) td {
            background-color: #f5f3ff;
        }
        .text-center { text-align: center; }
        .badge-sangat-aktif { color: #065f46; font-weight: bold; }
        .badge-aktif        { color: #1e40af; font-weight: bold; }
        .badge-cukup        { color: #92400e; font-weight: bold; }
        .badge-tidak-aktif  { color: #991b1b; font-weight: bold; }
        .progress-bar-bg {
            background: #e5e7eb;
            border-radius: 4px;
            width: 100%;
            height: 8px;
            display: block;
        }
        .footer {
            margin-top: 50px;
            font-size: 10pt;
        }
        .footer-right {
            text-align: right;
        }
        .footer-sign-name {
            font-weight: bold;
            text-decoration: underline;
            margin-top: 55px;
        }
        .summary-bar {
            display: flex;
            gap: 12px;
            margin-bottom: 18px;
        }
        .summary-box {
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 6px 16px;
            font-size: 9pt;
            background: #f9fafb;
            text-align: center;
        }
        .summary-box .num { font-size: 16pt; font-weight: bold; color: #4f46e5; }
        .summary-box .lbl { color: #6b7280; font-size: 8pt; }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <h2>Rekap Keaktifan Anggota KIP-K</h2>
        <p class="subtitle">Laporan Keaktifan Mahasiswa Penerima KIP-Kuliah</p>
        <span class="ormawa-badge">{{ $ormawa->nama_ormawa }} &bull; Periode {{ $ormawa->periode }}</span>
    </div>

    {{-- Meta Info --}}
    <table class="meta-table">
        <tr>
            <td class="label">Organisasi</td>
            <td class="value">{{ $ormawa->nama_ormawa }}</td>
            <td class="label">Tanggal Unduh</td>
            <td class="value">{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <td class="label">Ketua</td>
            <td class="value">{{ $ormawa->ketua }}</td>
            <td class="label">Dicetak oleh</td>
            <td class="value">{{ Auth::user()->nama }} (Pengurus)</td>
        </tr>
        <tr>
            <td class="label">Pembina</td>
            <td class="value">{{ $ormawa->pembina ?? '-' }}</td>
            <td class="label">Total Anggota</td>
            <td class="value">{{ count($rekapData) }} Mahasiswa KIP-K</td>
        </tr>
    </table>

    {{-- Summary counts --}}
    @php
        $totalSangatAktif = collect($rekapData)->where('status', 'Sangat Aktif')->count();
        $totalAktif       = collect($rekapData)->where('status', 'Aktif')->count();
        $totalCukup       = collect($rekapData)->where('status', 'Cukup')->count();
        $totalTidakAktif  = collect($rekapData)->where('status', 'Tidak Aktif')->count();
        $avgPersentase    = count($rekapData) > 0 ? round(collect($rekapData)->avg('persentase')) : 0;
    @endphp
    <table style="width:100%; border-collapse:collapse; margin-bottom:18px;">
        <tr>
            <td style="width:20%; text-align:center; padding:6px; background:#d1fae5; border-radius:6px; border:1px solid #a7f3d0;">
                <div style="font-size:20pt; font-weight:bold; color:#065f46;">{{ $totalSangatAktif }}</div>
                <div style="font-size:8pt; color:#065f46;">Sangat Aktif</div>
            </td>
            <td style="width:5%;"></td>
            <td style="width:20%; text-align:center; padding:6px; background:#dbeafe; border-radius:6px; border:1px solid #93c5fd;">
                <div style="font-size:20pt; font-weight:bold; color:#1e40af;">{{ $totalAktif }}</div>
                <div style="font-size:8pt; color:#1e40af;">Aktif</div>
            </td>
            <td style="width:5%;"></td>
            <td style="width:20%; text-align:center; padding:6px; background:#fef3c7; border-radius:6px; border:1px solid #fcd34d;">
                <div style="font-size:20pt; font-weight:bold; color:#92400e;">{{ $totalCukup }}</div>
                <div style="font-size:8pt; color:#92400e;">Cukup</div>
            </td>
            <td style="width:5%;"></td>
            <td style="width:20%; text-align:center; padding:6px; background:#fee2e2; border-radius:6px; border:1px solid #fca5a5;">
                <div style="font-size:20pt; font-weight:bold; color:#991b1b;">{{ $totalTidakAktif }}</div>
                <div style="font-size:8pt; color:#991b1b;">Tidak Aktif</div>
            </td>
            <td style="width:5%;"></td>
            <td style="width:25%; text-align:center; padding:6px; background:#ede9fe; border-radius:6px; border:1px solid #c4b5fd;">
                <div style="font-size:20pt; font-weight:bold; color:#4f46e5;">{{ $avgPersentase }}%</div>
                <div style="font-size:8pt; color:#4f46e5;">Rata-rata Kehadiran</div>
            </td>
        </tr>
    </table>

    {{-- Data Table --}}
    <table class="report-table">
        <thead>
            <tr>
                <th style="width:5%; text-align:center;">No</th>
                <th style="width:28%;">Nama Mahasiswa</th>
                <th style="width:14%;">NIM</th>
                <th style="width:20%;">Program Studi</th>
                <th style="width:12%; text-align:center;">Total Kegiatan</th>
                <th style="width:12%; text-align:center;">Poin (Didapat/Maks)</th>
                <th style="width:9%; text-align:center;">%</th>
                <th style="width:13%; text-align:center;">Status</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @forelse($rekapData as $row)
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td><strong>{{ $row['nama'] }}</strong></td>
                    <td>{{ $row['nim'] }}</td>
                    <td>{{ $row['prodi'] }}</td>
                    <td class="text-center">{{ $row['total_kegiatan'] }}</td>
                    <td class="text-center">{{ $row['total_poin'] }} / {{ $row['total_poin_maks'] ?? 0 }}</td>
                    <td class="text-center"><strong>{{ $row['persentase'] }}%</strong></td>
                    <td class="text-center">
                        @php
                            $cls = match($row['status']) {
                                'Sangat Aktif' => 'badge-sangat-aktif',
                                'Aktif'        => 'badge-aktif',
                                'Cukup'        => 'badge-cukup',
                                default        => 'badge-tidak-aktif',
                            };
                        @endphp
                        <span class="{{ $cls }}">{{ strtoupper($row['status']) }}</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="color:#9ca3af; padding:20px;">
                        Belum ada anggota KIP-K yang terdaftar.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Footer --}}
    <div class="footer">
        <table style="width:100%; border-collapse:collapse;">
            <tr>
                <td style="width:60%; vertical-align:top; padding-top:20px; font-size:9pt; color:#6b7280;">
                    <em>* Laporan ini dicetak secara otomatis dari Sistem Monitoring KIP-K<br>
                    * Poin Izin/Sakit dihitung setengah dari poin normal</em>
                </td>
                <td style="width:40%; text-align:center; vertical-align:top;">
                    <p style="margin:0; font-size:10pt;">{{ $ormawa->nama_ormawa }},<br>{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
                    <p style="margin:55px 0 0 0; font-weight:bold; border-top: 1px solid #000; display:inline-block; padding-top:4px;">
                        {{ Auth::user()->nama }}
                    </p>
                    <p style="margin:0; font-size:9pt; color:#6b7280;">Pengurus {{ $ormawa->nama_ormawa }}</p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
