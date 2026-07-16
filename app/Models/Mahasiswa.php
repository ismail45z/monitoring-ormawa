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
     * Get the user account details for this student.
     */
    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'pengguna_id');
    }

    /**
     * Get all ormawas this student is registered in (Many-to-Many).
     */
    public function ormawas(): BelongsToMany
    {
        return $this->belongsToMany(Ormawa::class, 'mahasiswa_ormawa', 'mahasiswa_id', 'ormawa_id')
            ->withTimestamps();
    }

    /**
     * Get the attendance records for this student.
     */
    public function kehadiran(): HasMany
    {
        return $this->hasMany(Kehadiran::class, 'mahasiswa_id');
    }
}
