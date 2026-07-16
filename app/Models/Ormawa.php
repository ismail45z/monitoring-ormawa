<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use OwenIt\Auditing\Contracts\Auditable;

#[Fillable(['nama_ormawa', 'jenis', 'periode', 'ketua', 'pembina', 'deskripsi', 'is_open_recruitment'])]
class Ormawa extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ormawa';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_open_recruitment' => 'boolean',
        ];
    }

    /**
     * Get the board members/pengurus for this ormawa.
     */
    public function pengurus(): HasMany
    {
        return $this->hasMany(Pengguna::class, 'ormawa_id');
    }

    /**
     * Get the activities for this ormawa.
     */
    public function kegiatan(): HasMany
    {
        return $this->hasMany(Kegiatan::class, 'ormawa_id');
    }

    /**
     * Get all students registered in this ormawa (Many-to-Many).
     */
    public function mahasiswas(): BelongsToMany
    {
        return $this->belongsToMany(Mahasiswa::class, 'mahasiswa_ormawa', 'ormawa_id', 'mahasiswa_id')
            ->withTimestamps();
    }
}
