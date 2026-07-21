<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['kegiatan_id', 'keanggotaan_id', 'status_kehadiran', 'status_verifikasi', 'keterangan', 'bukti_foto', 'keterangan_verifikasi', 'waktu_absen'])]
class Kehadiran extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'kehadiran';

    /**
     * Get the activity related to this attendance record.
     */
    public function kegiatan(): BelongsTo
    {
        return $this->belongsTo(Kegiatan::class, 'kegiatan_id');
    }

    /**
     * Get the membership related to this attendance record.
     */
    public function keanggotaan(): BelongsTo
    {
        return $this->belongsTo(Keanggotaan::class, 'keanggotaan_id');
    }
}
