<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Periode extends Model
{
    use HasFactory;

    protected $fillable = ['tahun', 'semester', 'tanggal_mulai', 'tanggal_selesai', 'status'];

    public function keanggotaans()
    {
        return $this->hasMany(Keanggotaan::class);
    }

    public function kegiatan()
    {
        return $this->hasMany(Kegiatan::class);
    }
}
