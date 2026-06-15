<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['ormawa_id', 'nama_kegiatan', 'deskripsi', 'tanggal', 'waktu_mulai', 'waktu_selesai', 'tempat'])]
class Kegiatan extends Model
{
    use HasFactory;

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
}
