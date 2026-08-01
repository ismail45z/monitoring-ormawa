<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Models\Kehadiran;
use App\Models\Mahasiswa;
use App\Models\Ormawa;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class WadirReportController extends Controller
{
    /**
     * Helper to get compiled report data based on filters.
     */
    private function getReportData($ormawaId = null, $startDate = null, $endDate = null)
    {
        $studentsQuery = Mahasiswa::with(['pengguna', 'ormawas']);

        // Filter mahasiswa yang bergabung dengan ormawa tertentu
        if ($ormawaId) {
            $studentsQuery->whereHas('ormawas', fn($q) => $q->where('ormawa.id', $ormawaId));
        }

        $students = $studentsQuery->get();
        $reportData = [];

        foreach ($students as $student) {
            // Mahasiswa bisa join banyak ormawa - ambil semua ormawa yang diikuti
            $studentOrmawas = $student->ormawas;

            // Jika filter ormawa aktif, batasi hanya ke ormawa tersebut
            if ($ormawaId) {
                $studentOrmawas = $studentOrmawas->where('id', $ormawaId);
            }

            $ormawaIds = $studentOrmawas->pluck('id')->toArray();
            $ormawaNama = $studentOrmawas->pluck('nama_ormawa')->join(', ') ?: 'Tidak Mengikuti';

            if (empty($ormawaIds)) {
                $reportData[] = [
                    'nama'           => $student->pengguna->nama ?? '-',
                    'nim'            => $student->nim,
                    'no_kip'         => $student->no_kip,
                    'prodi'          => $student->prodi,
                    'ormawa'         => 'Tidak Mengikuti',
                    'total_kegiatan' => 0,
                    'total_poin'     => 0,
                    'persentase'     => 0,
                    'status'         => 'TIDAK AKTIF',
                ];
                continue;
            }

            // Hitung total kegiatan dari semua ormawa yang diikuti
            $kegiatanQuery = Kegiatan::whereIn('ormawa_id', $ormawaIds);
            if ($startDate) $kegiatanQuery->where('tanggal', '>=', $startDate);
            
            // Batasi hingga hari ini agar kegiatan yang belum terjadi tidak mengurangi persentase
            $effectiveEndDate = $endDate ? min($endDate, now()->toDateString()) : now()->toDateString();
            $kegiatanQuery->where('tanggal', '<=', $effectiveEndDate);
            
            $totalKegiatan = (clone $kegiatanQuery)->count();
            $totalPoinMaksimal = (clone $kegiatanQuery)->sum('poin');

            // Hitung total poin kehadiran yang sudah diverifikasi
            $hadirQuery = Kehadiran::whereHas('keanggotaan', fn($q) => $q->where('mahasiswa_id', $student->id))
                ->whereIn('status_kehadiran', ['Hadir', 'Izin', 'Sakit'])
                ->where('status_verifikasi', 'Disetujui')
                ->whereHas('kegiatan', function ($q) use ($ormawaIds, $startDate, $effectiveEndDate) {
                    $q->whereIn('ormawa_id', $ormawaIds);
                    if ($startDate) $q->where('tanggal', '>=', $startDate);
                    $q->where('tanggal', '<=', $effectiveEndDate);
                });

            $totalPoin = $hadirQuery->with('kegiatan')->get()->sum(function ($kehadiran) {
                if ($kehadiran->status_kehadiran === 'Hadir') {
                    return $kehadiran->kegiatan->poin ?? 0;
                } elseif (in_array($kehadiran->status_kehadiran, ['Izin', 'Sakit'])) {
                    return ($kehadiran->kegiatan->poin ?? 0) / 2;
                }
                return 0;
            });

            $persentase = $totalPoinMaksimal > 0 ? round(($totalPoin / $totalPoinMaksimal) * 100) : 0;
            
            if ($persentase >= 80) {
                $status = 'Sangat Aktif';
            } elseif ($persentase >= 60) {
                $status = 'Aktif';
            } elseif ($persentase >= 40) {
                $status = 'Cukup';
            } else {
                $status = 'Tidak Aktif';
            }

            $reportData[] = [
                'nama'           => $student->pengguna->nama ?? '-',
                'nim'            => $student->nim,
                'no_kip'         => $student->no_kip,
                'prodi'          => $student->prodi,
                'ormawa'         => $ormawaNama,
                'total_kegiatan' => $totalKegiatan,
                'total_poin_maks'=> $totalPoinMaksimal,
                'total_poin'     => $totalPoin,
                'persentase'     => $persentase,
                'status'         => $status,
            ];
        }

        return $reportData;
    }

    /**
     * Export Report to PDF.
     */
    public function exportPdf(Request $request)
    {
        $ormawaId = $request->input('ormawa_id');
        $startDate = $request->input('tanggal_mulai');
        $endDate = $request->input('tanggal_selesai');

        $reportData = $this->getReportData($ormawaId, $startDate, $endDate);
        $selectedOrmawaName = $ormawaId ? (Ormawa::find($ormawaId)?->nama_ormawa ?? 'Tidak Diketahui') : 'Semua Ormawa';

        $pdf = Pdf::loadView('pdf.laporan_wadir', compact('reportData', 'selectedOrmawaName', 'startDate', 'endDate'));
        return $pdf->stream('laporan-keaktifan-kipk.pdf');
    }

    /**
     * Export Report to Excel (CSV Stream).
     */
    public function exportExcel(Request $request)
    {
        $ormawaId = $request->input('ormawa_id');
        $startDate = $request->input('tanggal_mulai');
        $endDate = $request->input('tanggal_selesai');

        $reportData = $this->getReportData($ormawaId, $startDate, $endDate);
        $selectedOrmawaName = $ormawaId ? (\App\Models\Ormawa::find($ormawaId)?->nama_ormawa ?? 'Tidak Diketahui') : 'Semua Ormawa';

        $headers = [
            'Content-type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="laporan-keaktifan-kipk.xls"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response(view('excel.laporan_wadir', compact('reportData', 'selectedOrmawaName', 'startDate', 'endDate')))->withHeaders($headers);
    }
}
