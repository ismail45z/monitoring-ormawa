<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML('<h1>Test</h1>');
$pdf->save(storage_path('test.pdf'));
echo 'SUCCESS';
