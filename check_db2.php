<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

$results = DB::table('sensor_data')->orderBy('created_at', 'desc')->limit(20)->get();
echo "Total records: " . count($results) . "\n";
foreach ($results as $r) {
    printf("[%s] %s | %s | %s\n", $r->created_at, $r->device_id, $r->tipo, $r->recibido_en);
}
