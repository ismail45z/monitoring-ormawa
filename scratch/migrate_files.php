<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Storage;

$publicDisk = Storage::disk('public');
$localDisk = Storage::disk('local');

$dirsToMove = ['bukti_kip', 'bukti_kehadiran'];

foreach ($dirsToMove as $dir) {
    if ($publicDisk->exists($dir)) {
        if (!$localDisk->exists($dir)) {
            $localDisk->makeDirectory($dir);
        }
        
        $files = $publicDisk->files($dir);
        foreach ($files as $file) {
            $localDisk->put($file, $publicDisk->get($file));
            $publicDisk->delete($file);
            echo "Moved $file\n";
        }
    }
}
echo "Migration complete.\n";
