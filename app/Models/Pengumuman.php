<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['ormawa_id', 'judul', 'isi', 'lampiran', 'is_aktif'])]
class Pengumuman extends Model
{
    use HasFactory;

    protected $table = 'pengumuman';

    protected $casts = [
        'is_aktif' => 'boolean',
    ];

    public function ormawa(): BelongsTo
    {
        return $this->belongsTo(Ormawa::class, 'ormawa_id');
    }
}
