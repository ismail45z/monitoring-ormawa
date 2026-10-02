<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Models\Kehadiran;
use App\Models\Mahasiswa;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RekapAnggotaController extends Controller
{
    private function getOrmawaId()
    {
        $ormawa = Auth::user()->ormawa;
        if (!$ormawa) {
            abort(403, 'Akun Anda tidak terhubung dengan Ormawa manapun.');
        }
        return $ormawa->id;
    }

    /**
     * Build rekap data for the pengurus's own ormawa.
     */
    private function buildRekapData()
    {
        $ormawaId = $this->getOrmawaId();
        $ormawa   = Auth::user()->ormawa;
        $students = $ormawa->mahasiswas()->with('pengguna')->get();

        $kegiatanQuery    = Kegiatan::where('ormawa_id', $ormawaId)
            ->where('tanggal', '<=', now()->toDateString()); // hanya kegiatan yang sudah/sedang terjadi
        $totalKegiatan    = (clone $kegiatanQuery)->count();
        $totalPoinMaksimal = (clone $kegiatanQuery)->sum('poin');
        $rekapData        = [];

        foreach ($students as $student) {
            $totalPoin = Kehadiran::whereHas('keanggotaan', fn($q) => $q->where('mahasiswa_id', $student->id))
                ->whereHas('kegiatan', fn($q) => $q->where('ormawa_id', $ormawaId))
                ->whereIn('status_kehadiran', ['Hadir', 'Izin', 'Sakit'])
                ->where('status_verifikasi', 'Disetujui')
                ->with('kegiatan')
                ->get()
                ->sum(function ($kehadiran) {
                    if ($kehadiran->status_kehadiran === 'Hadir') {
                        return $kehadiran->kegiatan->poin ?? 0;
                    } elseif (in_array($kehadiran->status_kehadiran, ['Izin', 'Sakit'])) {
                        return ($kehadiran->kegiatan->poin ?? 0) / 2;
                    }
                    return 0;
                });

            $persentase = $totalPoinMaksimal > 0 ? round(($totalPoin / $totalPoinMaksimal) * 100) : 0;

            if ($persentase >= 80) {
                $statusKeaktifan = 'Sangat Aktif';
            } elseif ($persentase >= 60) {
                $statusKeaktifan = 'Aktif';
            } elseif ($persentase >= 40) {
                $statusKeaktifan = 'Cukup';
            } else {
                $statusKeaktifan = 'Tidak Aktif';
            }

            $rekapData[] = [
                'nama'            => $student->pengguna->nama,
                'nim'             => $student->nim,
                'prodi'           => $student->prodi,
                'total_poin_maks' => $totalPoinMaksimal,
                'total_poin'      => $totalPoin,
                'total_kegiatan'  => $totalKegiatan,
                'persentase'      => $persentase,
                'status'          => $statusKeaktifan,
            ];
        }

        return [
            'rekapData'      => $rekapData,
            'ormawa'         => $ormawa,
            'totalKegiatan'  => $totalKegiatan,
        ];
    }

    public function index()
    {
        $data = $this->buildRekapData();
        $rekapData = $data['rekapData'];
        return view('pengurus.rekap.index', compact('rekapData'));
    }

    /**
     * Export rekap to PDF.
     */
    public function exportPdf()
    {
        $data      = $this->buildRekapData();
        $rekapData = $data['rekapData'];
        $ormawa    = $data['ormawa'];

        $pdf = Pdf::loadView('pdf.laporan_pengurus', compact('rekapData', 'ormawa'));
        $filename = 'rekap-keaktifan-' . str_replace(' ', '-', strtolower($ormawa->nama_ormawa)) . '.pdf';
        return $pdf->stream($filename);
    }

    /**
     * Export rekap to Excel.
     */
    public function exportExcel()
    {
        $data      = $this->buildRekapData();
        $rekapData = $data['rekapData'];
        $ormawa    = $data['ormawa'];

        $headers = [
            'Content-type'        => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="rekap-keaktifan-' . str_replace(' ', '-', strtolower($ormawa->nama_ormawa)) . '.xls"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        return response(view('excel.laporan_pengurus', compact('rekapData', 'ormawa')))->withHeaders($headers);
    }
}
