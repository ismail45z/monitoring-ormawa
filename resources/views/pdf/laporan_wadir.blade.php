<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Keaktifan Mahasiswa KIP-K</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11pt;
            color: #333;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .header h2 {
            margin: 0;
            text-transform: uppercase;
            font-size: 16pt;
        }
        .header p {
            margin: 5px 0 0 0;
            font-size: 10pt;
            color: #666;
        }
        .filter-info {
            margin-bottom: 20px;
            font-size: 10pt;
        }
        .filter-info table {
            width: 100%;
            border-collapse: collapse;
        }
        .filter-info td {
            padding: 3px 0;
        }
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .report-table th, .report-table td {
            border: 1px solid #ddd;
            padding: 8px 10px;
            text-align: left;
            font-size: 9pt;
        }
        .report-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-transform: uppercase;
        }
        .report-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .badge-sangat-aktif {
            background-color: #d1fae5;
            color: #065f46;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
        }
        .badge-aktif {
            background-color: #dbeafe;
            color: #1e40af;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
        }
        .badge-cukup {
            background-color: #fef3c7;
            color: #92400e;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
        }
        .badge-tidak-aktif {
            background-color: #fee2e2;
            color: #991b1b;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
        }
        .footer {
            margin-top: 50px;
            text-align: right;
            font-size: 10pt;
        }
        .footer-date {
            margin-bottom: 60px;
        }
        .footer-name {
            font-weight: bold;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Keaktifan Mahasiswa KIP-Kuliah</h2>
        <p>Sistem Monitoring Keaktifan Mahasiswa Penerima KIP-Kuliah</p>
    </div>

    <div class="filter-info">
        <table>
            <tr>
                <td style="width: 15%; font-weight: bold;">Organisasi (Ormawa)</td>
                <td style="width: 35%;">: {{ $selectedOrmawaName }}</td>
                <td style="width: 15%; font-weight: bold;">Tanggal Unduh</td>
                <td style="width: 35%;">: {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Rentang Tanggal</td>
                <td>: 
                    @if($startDate && $endDate)
                        {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') }}
                    @elseif($startDate)
                        Mulai {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') }}
                    @elseif($endDate)
                        Hingga {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') }}
                    @else
                        Semua Tanggal
                    @endif
                </td>
                <td style="font-weight: bold;">Unduh Oleh</td>
                <td>: {{ Auth::user()->nama }} (Wadir)</td>
            </tr>
        </table>
    </div>

    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">No</th>
                <th style="width: 25%;">Nama Mahasiswa</th>
                <th style="width: 12%;">NIM</th>
                <th style="width: 15%;">No KIP</th>
                <th style="width: 18%;">Program Studi</th>
                <th style="width: 10%; text-align: center;">Total Kegiatan</th>
                <th style="width: 12%; text-align: center;">Poin Didapat / Maks</th>
                <th style="width: 10%; text-align: center;">Persentase</th>
                <th style="width: 15%; text-align: center;">Status</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @forelse($reportData as $row)
                <tr>
                    <td style="text-align: center;">{{ $no++ }}</td>
                    <td><strong style="color: #1a202c;">{{ $row['nama'] }}</strong></td>
                    <td>{{ $row['nim'] }}</td>
                    <td>{{ $row['no_kip'] }}</td>
                    <td>{{ $row['prodi'] }}</td>
                    <td style="text-align: center;">{{ $row['total_kegiatan'] }}</td>
                    <td style="text-align: center;">{{ $row['total_poin'] }} / {{ $row['total_poin_maks'] ?? 0 }}</td>
                    <td style="text-align: center; font-weight: bold;">{{ $row['persentase'] }}%</td>
                        @php
                            $badgeClass = match($row['status']) {
                                'Sangat Aktif' => 'badge-sangat-aktif',
                                'Aktif' => 'badge-aktif',
                                'Cukup' => 'badge-cukup',
                                default => 'badge-tidak-aktif',
                            };
                        @endphp
                        <td style="text-align: center;">
                            <span class="{{ $badgeClass }}">{{ strtoupper($row['status']) }}</span>
                        </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align: center; color: #999; padding: 20px;">Tidak ada data laporan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <div class="footer-date">Kota Kediri, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</div>
        <p style="margin-bottom: 50px;">Mengetahui,<br>Wakil Direktur Kemahasiswaan</p>
        <p class="footer-name">{{ Auth::user()->nama }}</p>
        <p style="margin: 0; font-size: 9pt; color: #666;">NIP. 19780512 200501 1 002</p>
    </div>
</body>
</html>
