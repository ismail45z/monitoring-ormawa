<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Pindahkan kegiatan ID 9 ke periode yang benar (id=6: 2026/2027 Ganjil)
$kegiatan = App\Models\Kegiatan::find(9);
if ($kegiatan) {
    $old = $kegiatan->periode_id;
    $kegiatan->periode_id = 6;
    $kegiatan->save();
    echo "Kegiatan '{$kegiatan->nama_kegiatan}' dipindah dari periode_id={$old} ke periode_id=6 (2026/2027 - Ganjil)" . PHP_EOL;
} else {
    echo "Kegiatan ID 9 tidak ditemukan." . PHP_EOL;
}
