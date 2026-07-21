<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Keanggotaan extends Model
{
    use HasFactory;

    protected $fillable = ['mahasiswa_id', 'ormawa_id', 'periode_id', 'jabatan_id', 'tgl_masuk', 'tgl_selesai', 'status'];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function ormawa()
    {
        return $this->belongsTo(Ormawa::class);
    }

    public function periode()
    {
        return $this->belongsTo(Periode::class);
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class);
    }

    public function kehadiran()
    {
        return $this->hasMany(Kehadiran::class);
    }
}
