<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Keaktifan Mahasiswa KIP-Kuliah</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #000000;
            padding: 8px;
            text-align: left;
            vertical-align: middle;
        }
        th {
            background-color: #d9ead3;
            font-weight: bold;
            text-align: center;
        }
        .title {
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            border: none;
            background-color: transparent;
        }
        .center {
            text-align: center;
        }
    </style>
</head>
<body>
    <table>
        <tr>
            <td colspan="10" class="title">LAPORAN KEAKTIFAN MAHASISWA KIP-KULIAH</td>
        </tr>
        <tr>
            <td colspan="10" class="title" style="font-size: 12px; font-weight: normal;">
                {{ $selectedOrmawaName }} | Periode: {{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d/m/Y') : '-' }} s/d {{ $endDate ? \Carbon\Carbon::parse($endDate)->format('d/m/Y') : '-' }}
            </td>
        </tr>
        <tr>
            <td colspan="10" style="border: none;"></td>
        </tr>
        <tr>
            <th style="width: 50px;">No</th>
            <th style="width: 200px;">Nama Mahasiswa</th>
            <th style="width: 120px;">NIM</th>
            <th style="width: 150px;">No KIP</th>
            <th style="width: 200px;">Program Studi</th>
            <th style="width: 250px;">Organisasi Mahasiswa (Ormawa)</th>
            <th style="width: 100px;">Total Kegiatan</th>
            <th style="width: 120px;">Total Poin</th>
            <th style="width: 100px;">Kehadiran</th>
            <th style="width: 150px;">Status Keaktifan</th>
        </tr>
        
        @php $no = 1; @endphp
        @foreach($reportData as $row)
        <tr>
            <td class="center">{{ $no++ }}</td>
            <td>{{ $row['nama'] }}</td>
            <td class="center" style="mso-number-format:'\@';">{{ $row['nim'] }}</td>
            <!-- Menggunakan style khusus agar nomor KIP tidak jadi format eksponensial di Excel -->
            <td style="mso-number-format:'\@';">{{ $row['no_kip'] }}</td>
            <td>{{ $row['jurusan'] ?? '' }} / {{ $row['prodi'] ?? '' }}</td>
            <td>{{ $row['ormawa'] }}</td>
            <td class="center">{{ $row['total_kegiatan'] }}</td>
            <td class="center">{{ $row['total_poin'] }} / {{ $row['total_poin_maks'] ?? 0 }}</td>
            <td class="center">{{ $row['persentase'] }}%</td>
            <td class="center">
                @if($row['persentase'] >= 40)
                    <span style="color: #008000; font-weight: bold;">Cukup</span>
                @else
                    <span style="color: #FF0000; font-weight: bold;">Tidak Aktif</span>
                @endif
            </td>
        </tr>
        @endforeach
    </table>
</body>
</html>
