<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nama_ormawa', 'jenis', 'periode', 'ketua', 'pembina', 'deskripsi'])]
class Ormawa extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ormawa';

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
}
