<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

#[Fillable(['pengguna_id', 'nim', 'no_kip', 'jurusan', 'prodi', 'angkatan', 'status_kip', 'bukti_kip'])]
class Mahasiswa extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mahasiswa';

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'no_kip' => 'encrypted',
    ];

    /**
     * Get the user account details for this student.
     */
    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'pengguna_id');
    }

    /**
     * Get all memberships for this student.
     */
    public function keanggotaans(): HasMany
    {
        return $this->hasMany(Keanggotaan::class, 'mahasiswa_id');
    }

    /**
     * Get the timeline of attendance (history).
     */
    public function timelineKehadiran()
    {
        return $this->kehadiran()->with(['kegiatan', 'kegiatan.ormawa'])->orderBy('created_at', 'desc');
    }

    /**
     * Check if the student's profile is complete.
     * At registration, dummy values ('-') are used.
     */
    public function isProfileComplete(): bool
    {
        if (
            empty($this->jurusan) || $this->jurusan === '-' ||
            empty($this->prodi) || $this->prodi === '-' ||
            empty($this->no_kip) || $this->no_kip === '-'
        ) {
            return false;
        }

        return true;
    }

    /**
     * Get the attendance records for this student.
     */
    public function kehadiran(): HasMany
    {
        return $this->hasMany(Kehadiran::class, 'mahasiswa_id');
    }

    /**
     * Get the ormawas for this student.
     */
    public function ormawas(): BelongsToMany
    {
        return $this->belongsToMany(Ormawa::class, 'keanggotaans', 'mahasiswa_id', 'ormawa_id')
                    ->withPivot(['periode_id', 'jabatan_id', 'tgl_masuk', 'tgl_selesai', 'status'])
                    ->withTimestamps();
    }
}
