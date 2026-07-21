<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

#[Fillable(['ormawa_id', 'nama_kegiatan', 'deskripsi', 'jenis', 'tanggal', 'waktu_mulai', 'waktu_selesai', 'tempat', 'periode_id', 'poin', 'status_absensi'])]
class Kegiatan extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'kegiatan';

    /**
     * Get the ormawa hosting this activity.
     */
    public function ormawa(): BelongsTo
    {
        return $this->belongsTo(Ormawa::class, 'ormawa_id');
    }

    /**
     * Get the attendance records for this activity.
     */
    public function kehadiran(): HasMany
    {
        return $this->hasMany(Kehadiran::class, 'kegiatan_id');
    }

    /**
     * Get the period this activity belongs to.
     */
    public function periode(): BelongsTo
    {
        return $this->belongsTo(Periode::class, 'periode_id');
    }

    /**
     * Check if attendance recording is currently open for this activity.
     */
    public function isAttendanceOpen(): bool
    {
        if ($this->status_absensi === 'buka') {
            return true;
        }
        
        if ($this->status_absensi === 'tutup') {
            return false;
        }

        // Auto mode: open if current time is between start and end time
        if ($this->tanggal && $this->waktu_mulai && $this->waktu_selesai) {
            $startDateTime = \Carbon\Carbon::parse($this->tanggal . ' ' . $this->waktu_mulai);
            $endDateTime = \Carbon\Carbon::parse($this->tanggal . ' ' . $this->waktu_selesai);
            return now()->between($startDateTime, $endDateTime);
        }

        if ($this->tanggal && $this->waktu_selesai) {
            $endDateTime = \Carbon\Carbon::parse($this->tanggal . ' ' . $this->waktu_selesai);
            return now()->lessThanOrEqualTo($endDateTime);
        }

        return false;
    }

    /**
     * Check if attendance is not yet open (before start time)
     */
    public function isAttendanceNotYetOpen(): bool
    {
        if ($this->status_absensi === 'buka') {
            return false;
        }
        
        if ($this->status_absensi === 'tutup') {
            return false;
        }

        if ($this->tanggal && $this->waktu_mulai) {
            $startDateTime = \Carbon\Carbon::parse($this->tanggal . ' ' . $this->waktu_mulai);
            return now()->lessThan($startDateTime);
        }

        return false;
    }

    /**
     * Get text representation of attendance status.
     */
    public function getAttendanceStatusText(): string
    {
        if ($this->status_absensi === 'buka') {
            return 'Buka Paksa';
        }
        if ($this->status_absensi === 'tutup') {
            return 'Tutup Paksa';
        }
        
        if ($this->isAttendanceNotYetOpen()) {
            return 'Belum Mulai';
        }
        
        return $this->isAttendanceOpen() ? 'Buka (Otomatis)' : 'Tutup (Waktu Habis)';
    }
}
