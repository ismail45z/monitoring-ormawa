<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\Ormawa;
use App\Services\KeaktifanService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class WadirReportController extends Controller
{
    /**
     * Helper to get compiled report data based on filters.
     * Menggunakan KeaktifanService agar logika perhitungan konsisten di seluruh aplikasi.
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
        $keaktifanService = new KeaktifanService();

        foreach ($students as $student) {
            // Mahasiswa bisa join banyak ormawa - ambil semua ormawa yang diikuti
            $studentOrmawas = $student->ormawas;

            // Jika filter ormawa aktif, batasi hanya ke ormawa tersebut
            if ($ormawaId) {
                $studentOrmawas = $studentOrmawas->where('id', $ormawaId);
            }

            if ($studentOrmawas->isEmpty()) {
                $reportData[] = [
                    'nama'           => $student->pengguna->nama ?? '-',
                    'nim'            => $student->nim,
                    'no_kip'         => $student->no_kip,
                    'prodi'          => $student->prodi,
                    'ormawa'         => 'Tidak Mengikuti',
                    'total_kegiatan' => 0,
                    'total_poin_maks'=> 0,
                    'total_poin'     => 0,
                    'persentase'     => 0,
                    'status'         => 'Tidak Aktif',
                ];
                continue;
            }

            // Hitung rekap per ormawa menggunakan KeaktifanService (Single Source of Truth)
            foreach ($studentOrmawas as $ormawa) {
                $rekap = $keaktifanService->hitungRekap($student, $ormawa, $startDate, $endDate);

                $reportData[] = [
                    'nama'           => $student->pengguna->nama ?? '-',
                    'nim'            => $student->nim,
                    'no_kip'         => $student->no_kip,
                    'prodi'          => $student->prodi,
                    'ormawa'         => $ormawa->nama_ormawa,
                    'total_kegiatan' => $rekap['totalKegiatan'],
                    'total_poin_maks'=> $rekap['totalPoinMaks'],
                    'total_poin'     => $rekap['totalPoin'],
                    'persentase'     => $rekap['persentase'],
                    'status'         => $rekap['statusKeaktifan'],
                ];
            }
        }

        return $reportData;
    }

    /**
     * Export Report to PDF.
     */
    public function exportPdf(Request $request)
    {
        $ormawaId  = $request->input('ormawa_id');
        $startDate = $request->input('tanggal_mulai');
        $endDate   = $request->input('tanggal_selesai');

        $reportData         = $this->getReportData($ormawaId, $startDate, $endDate);
        $selectedOrmawaName = $ormawaId ? (Ormawa::find($ormawaId)?->nama_ormawa ?? 'Tidak Diketahui') : 'Semua Ormawa';

        $pdf = Pdf::loadView('pdf.laporan_wadir', compact('reportData', 'selectedOrmawaName', 'startDate', 'endDate'));
        return $pdf->stream('laporan-keaktifan-kipk.pdf');
    }

    /**
     * Export Report to Excel (CSV Stream).
     */
    public function exportExcel(Request $request)
    {
        $ormawaId  = $request->input('ormawa_id');
        $startDate = $request->input('tanggal_mulai');
        $endDate   = $request->input('tanggal_selesai');

        $reportData         = $this->getReportData($ormawaId, $startDate, $endDate);
        $selectedOrmawaName = $ormawaId ? (Ormawa::find($ormawaId)?->nama_ormawa ?? 'Tidak Diketahui') : 'Semua Ormawa';

        $headers = [
            'Content-type'        => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="laporan-keaktifan-kipk.xls"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        return response(view('excel.laporan_wadir', compact('reportData', 'selectedOrmawaName', 'startDate', 'endDate')))->withHeaders($headers);
    }
}
