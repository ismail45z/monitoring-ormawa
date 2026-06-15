<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['pengguna_id', 'nim', 'no_kip', 'jurusan', 'prodi', 'angkatan', 'status_kip', 'ormawa_id'])]
class Mahasiswa extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mahasiswa';

    /**
     * Get the user account details for this student.
     */
    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'pengguna_id');
    }

    /**
     * Get the ormawa this student is registered in.
     */
    public function ormawa(): BelongsTo
    {
        return $this->belongsTo(Ormawa::class, 'ormawa_id');
    }

    /**
     * Get the attendance records for this student.
     */
    public function kehadiran(): HasMany
    {
        return $this->hasMany(Kehadiran::class, 'mahasiswa_id');
    }
}
