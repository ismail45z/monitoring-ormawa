<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['ormawa_id', 'mahasiswa_id', 'tipe', 'status', 'catatan', 'catatan_pengurus', 'pemroses_id', 'processed_at'])]
class AnggotaRequest extends Model
{
    use HasFactory;

    protected $table = 'anggota_requests';

    protected $casts = [
        'processed_at' => 'datetime',
    ];

    public function ormawa(): BelongsTo
    {
        return $this->belongsTo(Ormawa::class, 'ormawa_id');
    }

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }

    public function pemroses(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'pemroses_id');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}
