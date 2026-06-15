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
        $studentsQuery = Mahasiswa::with(['pengguna', 'ormawa']);

        if ($ormawaId) {
            $studentsQuery->where('ormawa_id', $ormawaId);
        }

        $students = $studentsQuery->get();
        $reportData = [];

        foreach ($students as $student) {
            $studentOrmawaId = $student->ormawa_id;

            $kegiatanQuery = Kegiatan::query();
            if ($studentOrmawaId) {
                $kegiatanQuery->where('ormawa_id', $studentOrmawaId);
            } else {
                $kegiatanQuery->whereRaw('1 = 0'); // No activities if no ormawa
            }

            if ($startDate) {
                $kegiatanQuery->where('tanggal', '>=', $startDate);
            }
            if ($endDate) {
                $kegiatanQuery->where('tanggal', '<=', $endDate);
            }

            $totalKegiatan = $studentOrmawaId ? $kegiatanQuery->count() : 0;

            $hadirQuery = Kehadiran::where('mahasiswa_id', $student->id)
                ->where('status_kehadiran', 'Hadir')
                ->where('status_verifikasi', 'Disetujui');

            if ($studentOrmawaId) {
                $hadirQuery->whereHas('kegiatan', function ($q) use ($studentOrmawaId, $startDate, $endDate) {
                    $q->where('ormawa_id', $studentOrmawaId);
                    if ($startDate) {
                        $q->where('tanggal', '>=', $startDate);
                    }
                    if ($endDate) {
                        $q->where('tanggal', '<=', $endDate);
                    }
                });
            } else {
                $hadirQuery->whereRaw('1 = 0');
            }

            $totalHadir = $studentOrmawaId ? $hadirQuery->count() : 0;

            $persentase = $totalKegiatan > 0 ? round(($totalHadir / $totalKegiatan) * 100, 1) : 0;
            $status = $persentase >= 60 ? 'AKTIF' : 'TIDAK AKTIF';

            $reportData[] = [
                'nama' => $student->pengguna->nama,
                'nim' => $student->nim,
                'no_kip' => $student->no_kip,
                'prodi' => $student->prodi,
                'ormawa' => $student->ormawa ? $student->ormawa->nama_ormawa : 'Tidak Mengikuti',
                'total_kegiatan' => $totalKegiatan,
                'total_hadir' => $totalHadir,
                'persentase' => $persentase,
                'status' => $status,
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
        $selectedOrmawaName = $ormawaId ? Ormawa::find($ormawaId)->nama_ormawa : 'Semua Ormawa';

        $pdf = Pdf::loadView('pdf.laporan_wadir', compact('reportData', 'selectedOrmawaName', 'startDate', 'endDate'));
        return $pdf->download('laporan-keaktifan-kipk.pdf');
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

        $headers = [
            'Content-type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="laporan-keaktifan-kipk.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($reportData) {
            $file = fopen('php://output', 'w');
            // Write BOM for Excel UTF-8 support
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header Row
            fputcsv($file, [
                'No', 
                'Nama Mahasiswa', 
                'NIM', 
                'No KIP', 
                'Program Studi', 
                'Organisasi Mahasiswa (Ormawa)', 
                'Total Kegiatan', 
                'Total Hadir', 
                'Persentase Kehadiran', 
                'Status Keaktifan'
            ]);

            $no = 1;
            foreach ($reportData as $row) {
                fputcsv($file, [
                    $no++,
                    $row['nama'],
                    $row['nim'],
                    $row['no_kip'],
                    $row['prodi'],
                    $row['ormawa'],
                    $row['total_kegiatan'],
                    $row['total_hadir'],
                    $row['persentase'] . '%',
                    $row['status']
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
