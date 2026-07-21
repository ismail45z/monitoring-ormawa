<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
use Illuminate\Support\Facades\DB;

$tables = DB::select('SHOW TABLES');
$schema = [];
foreach($tables as $table) {
    $tableName = reset($table);
    $columns = DB::select('SHOW COLUMNS FROM ' . $tableName);
    $schema[$tableName] = $columns;
}
file_put_contents(__DIR__.'/schema2.json', json_encode($schema, JSON_PRETTY_PRINT));
