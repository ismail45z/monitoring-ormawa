<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Models\Kehadiran;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class MahasiswaFeaturesController extends Controller
{
    private function getMahasiswa()
    {
        $mahasiswa = Auth::user()->mahasiswa;
        if (!$mahasiswa) {
            abort(403, 'Profil mahasiswa KIP Anda belum terdaftar.');
        }
        return $mahasiswa;
    }

    public function kegiatanIndex()
    {
        $mahasiswa = $this->getMahasiswa();
        $ormawas = $mahasiswa->ormawas;

        if ($ormawas->isEmpty()) {
            // Gunakan LengthAwarePaginator kosong agar $kegiatans->hasPages() tidak error di view
            $kegiatans = new LengthAwarePaginator([], 0, 12);
            $alreadyLogged = [];
            $msg = 'Anda belum terdaftar di Ormawa manapun. Silakan minta Admin untuk menghubungkan Anda dengan Ormawa.';
            return view('mahasiswa.kegiatan.index', compact('kegiatans', 'alreadyLogged'))->with('info', $msg);
        }

        $ormawaIds = $ormawas->pluck('id')->toArray();

        // List activities from all joined ormawas
        $kegiatans = Kegiatan::whereIn('ormawa_id', $ormawaIds)
            ->with('ormawa')
            ->orderBy('tanggal', 'desc')
            ->paginate(12);

        // Get activities they already logged attendance for
        $alreadyLogged = Kehadiran::whereHas('keanggotaan', fn($q) => $q->where('mahasiswa_id', $mahasiswa->id))
            ->pluck('kegiatan_id')
            ->toArray();

        return view('mahasiswa.kegiatan.index', compact('kegiatans', 'alreadyLogged'));
    }

    public function showCatatForm(Kegiatan $kegiatan)
    {
        $mahasiswa = $this->getMahasiswa();
        $ormawaIds = $mahasiswa->ormawas->pluck('id')->toArray();

        if (!in_array($kegiatan->ormawa_id, $ormawaIds)) {
            abort(403, 'Kegiatan ini diselenggarakan oleh Ormawa yang tidak Anda ikuti.');
        }

        if ($kegiatan->isAttendanceNotYetOpen()) {
            return redirect()->route('mahasiswa.kegiatan.index')->with('error', 'Waktu pencatatan kehadiran untuk kegiatan ini belum dimulai.');
        }

        if (!$kegiatan->isAttendanceOpen()) {
            return redirect()->route('mahasiswa.kegiatan.index')->with('error', 'Pencatatan kehadiran untuk kegiatan ini sudah ditutup.');
        }

        // Check if attendance is already recorded
        $exists = Kehadiran::whereHas('keanggotaan', fn($q) => $q->where('mahasiswa_id', $mahasiswa->id))
            ->where('kegiatan_id', $kegiatan->id)
            ->first();

        if ($exists) {
            return redirect()->route('mahasiswa.riwayat')->with('error', 'Anda sudah mencatat kehadiran untuk kegiatan ini.');
        }

        return view('mahasiswa.kehadiran.catat', compact('kegiatan'));
    }

    public function storeKehadiran(Request $request, Kegiatan $kegiatan)
    {
        $mahasiswa = $this->getMahasiswa();
        $ormawaIds = $mahasiswa->ormawas->pluck('id')->toArray();

        if (!in_array($kegiatan->ormawa_id, $ormawaIds)) {
            abort(403, 'Kegiatan ini diselenggarakan oleh Ormawa yang tidak Anda ikuti.');
        }

        if ($kegiatan->isAttendanceNotYetOpen()) {
            return redirect()->route('mahasiswa.kegiatan.index')->with('error', 'Waktu pencatatan kehadiran untuk kegiatan ini belum dimulai.');
        }

        if (!$kegiatan->isAttendanceOpen()) {
            return redirect()->route('mahasiswa.kegiatan.index')->with('error', 'Pencatatan kehadiran untuk kegiatan ini sudah ditutup.');
        }

        // Check again
        $exists = Kehadiran::whereHas('keanggotaan', fn($q) => $q->where('mahasiswa_id', $mahasiswa->id))
            ->where('kegiatan_id', $kegiatan->id)
            ->first();

        if ($exists) {
            return redirect()->route('mahasiswa.riwayat')->with('error', 'Anda sudah mencatat kehadiran untuk kegiatan ini.');
        }

        $validated = $request->validate([
            'status_kehadiran' => 'required|in:Hadir,Tidak Hadir,Izin,Sakit',
            'bukti_foto'       => 'required_if:status_kehadiran,Hadir,Izin,Sakit|image|mimes:jpeg,png,jpg,gif|max:2048',
            'keterangan'       => 'nullable|string',
        ]);

        $buktiFotoPath = null;
        if ($request->hasFile('bukti_foto')) {
            $buktiFotoPath = $this->compressAndStoreImage($request->file('bukti_foto'), 'bukti_kehadiran');
        }

        // Bungkus dalam transaksi untuk mencegah race condition double-submission
        $created = DB::transaction(function () use ($validated, $mahasiswa, $kegiatan, $buktiFotoPath) {
            $keanggotaan = \App\Models\Keanggotaan::where('mahasiswa_id', $mahasiswa->id)
                ->where('ormawa_id', $kegiatan->ormawa_id)
                ->first();

            if (!$keanggotaan) {
                return null;
            }

            // Cek sekali lagi di dalam transaksi untuk mencegah double-submit
            $alreadyExists = Kehadiran::where('kegiatan_id', $kegiatan->id)
                ->where('keanggotaan_id', $keanggotaan->id)
                ->lockForUpdate()
                ->exists();

            if ($alreadyExists) {
                return false;
            }

            Kehadiran::create([
                'kegiatan_id'       => $kegiatan->id,
                'keanggotaan_id'    => $keanggotaan->id,
                'status_kehadiran'  => $validated['status_kehadiran'],
                'status_verifikasi' => 'Pending',
                'keterangan'        => $validated['keterangan'],
                'bukti_foto'        => $buktiFotoPath,
            ]);

            return true;
        });

        if ($created === null) {
            return redirect()->route('mahasiswa.riwayat')->with('error', 'Anda belum menjadi anggota Ormawa ini.');
        }

        if ($created === false) {
            return redirect()->route('mahasiswa.riwayat')->with('error', 'Anda sudah mencatat kehadiran untuk kegiatan ini.');
        }

        return redirect()->route('mahasiswa.riwayat')->with('success', 'Kehadiran berhasil dicatat dan sedang menunggu verifikasi Pengurus.');
    }

    public function riwayatIndex()
    {
        $mahasiswa = $this->getMahasiswa();
        $kehadirans = Kehadiran::with('kegiatan.ormawa')
            ->whereHas('keanggotaan', fn($q) => $q->where('mahasiswa_id', $mahasiswa->id))
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('mahasiswa.riwayat.index', compact('kehadirans'));
    }

    public function rekapDiri()
    {
        $mahasiswa = $this->getMahasiswa();
        $ormawas = $mahasiswa->ormawas;

        if ($ormawas->isEmpty()) {
            return view('mahasiswa.rekap.index', [
                'rekapPerOrmawa' => collect(),
            ]);
        }

        // Calculate rekap per ormawa using KeaktifanService
        $keaktifanService = new \App\Services\KeaktifanService();
        $rekapPerOrmawa = $ormawas->map(function ($ormawa) use ($mahasiswa, $keaktifanService) {
            return $keaktifanService->hitungRekap($mahasiswa, $ormawa);
        });

        return view('mahasiswa.rekap.index', compact('rekapPerOrmawa'));
    }

    public function timeline(Request $request)
    {
        $mahasiswa = $this->getMahasiswa();
        $ormawas = $mahasiswa->ormawas;

        if ($ormawas->isEmpty()) {
            return redirect()->route('mahasiswa.dashboard')->with('error', 'Anda belum terdaftar di Ormawa manapun.');
        }

        $ormawaIds = $ormawas->pluck('id')->toArray();

        // Filter by selected ormawa if specified, otherwise show all
        $selectedOrmawaId = $request->input('ormawa_id', null);

        // Ambil semua periode unik
        $kegiatanQuery = Kegiatan::whereIn('ormawa_id', $ormawaIds);
        if ($selectedOrmawaId) {
            $kegiatanQuery->where('ormawa_id', $selectedOrmawaId);
        }

        $periodeIds = (clone $kegiatanQuery)
            ->whereNotNull('periode_id')
            ->distinct()
            ->pluck('periode_id');
            
        $periodes = \App\Models\Periode::whereIn('id', $periodeIds)
            ->orderBy('tanggal_mulai', 'desc')
            ->get();

        $selectedPeriode = $request->input('periode_id', $periodes->first()->id ?? null);

        $kegiatansQuery = (clone $kegiatanQuery)->with(['ormawa', 'periode'])->orderBy('tanggal', 'asc')->orderBy('waktu_mulai', 'asc');

        if ($selectedPeriode) {
            $kegiatansQuery->where('periode_id', $selectedPeriode);
        }

        $kegiatans = $kegiatansQuery->get();

        return view('mahasiswa.timeline.index', compact('kegiatans', 'periodes', 'selectedPeriode', 'ormawas', 'selectedOrmawaId'));
    }

    public function pendaftaranIndex()
    {
        $mahasiswa = $this->getMahasiswa();
        $ormawas = \App\Models\Ormawa::paginate(9);
        $isProfileComplete = $mahasiswa->isProfileComplete();
        
        // Get all requests for this student (pending, disetujui, ditolak)
        $riwayatRequests = \App\Models\AnggotaRequest::where('mahasiswa_id', $mahasiswa->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('mahasiswa.pendaftaran.index', compact('ormawas', 'mahasiswa', 'riwayatRequests', 'isProfileComplete'));
    }

    public function daftarOrmawa(Request $request)
    {
        $request->validate([
            'ormawa_id' => 'required|exists:ormawa,id',
            'alasan' => 'nullable|string|max:500'
        ]);

        $mahasiswa = $this->getMahasiswa();
        
        if (!$mahasiswa->isProfileComplete()) {
            return back()->with('error', 'Silakan lengkapi data profil Anda (Jurusan, Program Studi, dll) terlebih dahulu sebelum mendaftar.');
        }

        $ormawaId = $request->ormawa_id;

        $ormawa = \App\Models\Ormawa::findOrFail($ormawaId);
        if (!$ormawa->is_open_recruitment) {
            return back()->with('error', 'Maaf, pendaftaran anggota baru untuk Ormawa ini sedang ditutup.');
        }

        // Cek Pembatasan Jurusan / Prodi
        if ($ormawa->kategori_jurusan && $mahasiswa->jurusan !== $ormawa->kategori_jurusan) {
            return back()->with('error', "Maaf, pendaftaran Ormawa ini dibatasi hanya untuk mahasiswa dengan Jurusan {$ormawa->kategori_jurusan}.");
        }

        if ($ormawa->kategori_prodi && $mahasiswa->prodi !== $ormawa->kategori_prodi) {
            return back()->with('error', "Maaf, pendaftaran Ormawa ini dibatasi hanya untuk mahasiswa dengan Program Studi {$ormawa->kategori_prodi}.");
        }

        // Cek apakah sudah tergabung
        if ($mahasiswa->ormawas->contains($ormawaId)) {
            return back()->with('error', 'Anda sudah terdaftar di Ormawa ini.');
        }

        // Cek apakah sudah ada request pending atau ditolak (untuk mengecek jeda/permanen)
        $existingRequest = \App\Models\AnggotaRequest::where('mahasiswa_id', $mahasiswa->id)
            ->where('ormawa_id', $ormawaId)
            ->whereIn('status', ['pending', 'ditolak'])
            ->orderBy('created_at', 'desc')
            ->first();

        if ($existingRequest) {
            if ($existingRequest->status === 'pending') {
                return back()->with('error', 'Anda sudah memiliki permintaan bergabung yang sedang menunggu persetujuan untuk Ormawa ini.');
            }

            if ($existingRequest->status === 'ditolak') {
                if ($existingRequest->is_permanen) {
                    return back()->with('error', 'Pendaftaran Anda ke Ormawa ini telah ditolak secara permanen.');
                }
                
                if ($existingRequest->cooldown_hari !== null && $existingRequest->cooldown_hari > 0) {
                    $processedAt = \Carbon\Carbon::parse($existingRequest->processed_at ?? $existingRequest->updated_at);
                    $daysPassed = $processedAt->diffInDays(now());
                    
                    if ($daysPassed < $existingRequest->cooldown_hari) {
                        $sisaHari = $existingRequest->cooldown_hari - (int) $daysPassed;
                        return back()->with('error', "Pendaftaran Anda sebelumnya ditolak. Anda baru dapat mendaftar lagi ke Ormawa ini dalam $sisaHari hari.");
                    }
                }
            }
        }

        // Buat request baru
        \App\Models\AnggotaRequest::create([
            'ormawa_id' => $ormawaId,
            'mahasiswa_id' => $mahasiswa->id,
            'tipe' => 'tambah',
            'status' => 'pending',
            'catatan' => $request->alasan,
        ]);

        return back()->with('success', 'Permintaan bergabung berhasil dikirim. Menunggu persetujuan Pengurus Ormawa.');
    }

    /**
     * Compress and store an uploaded image using PHP GD.
     * Resizes to max 1024px wide and saves at 80% JPEG quality.
     */
    private function compressAndStoreImage($uploadedFile, string $directory): string
    {
        $extension = strtolower($uploadedFile->getClientOriginalExtension());
        $filename  = uniqid() . '.' . $extension;
        $destPath  = storage_path('app/' . $directory . '/' . $filename);

        if (!is_dir(dirname($destPath))) {
            mkdir(dirname($destPath), 0755, true);
        }

        // Jika ekstensi GD tidak tersedia, simpan file asli tanpa kompresi
        if (!extension_loaded('gd')) {
            $uploadedFile->storeAs($directory, $filename, 'local');
            return $directory . '/' . $filename;
        }

        $sourcePath = $uploadedFile->getRealPath();

        $srcImage = match ($extension) {
            'jpg', 'jpeg' => imagecreatefromjpeg($sourcePath),
            'png'         => imagecreatefrompng($sourcePath),
            'gif'         => imagecreatefromgif($sourcePath),
            default       => null,
        };

        if (!$srcImage) {
            $uploadedFile->storeAs($directory, $filename, 'local');
            return $directory . '/' . $filename;
        }

        $origW = imagesx($srcImage);
        $origH = imagesy($srcImage);
        $maxW  = 1024;

        if ($origW > $maxW) {
            $newW = $maxW;
            $newH = (int) round($origH * ($maxW / $origW));
        } else {
            $newW = $origW;
            $newH = $origH;
        }

        $resized = imagecreatetruecolor($newW, $newH);

        if ($extension === 'png') {
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
        }

        imagecopyresampled($resized, $srcImage, 0, 0, 0, 0, $newW, $newH, $origW, $origH);

        if ($extension === 'png') {
            imagepng($resized, $destPath, 7);
        } else {
            imagejpeg($resized, $destPath, 80);
        }

        imagedestroy($srcImage);
        imagedestroy($resized);

        return $directory . '/' . $filename;
    }
}
