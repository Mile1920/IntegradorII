<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::capture();
$response = $kernel->handle($request);

// Use Guzzle to POST to the endpoint
$client = new GuzzleHttp\Client(['base_uri' => 'http://localhost:8000']);
try {
    $resp = $client->post('/api/sensor/esp32/recibir', [
        'json' => [
            'device_id' => 'esp32_gases_01',
            'tipo' => 'gases_toxicos',
            'mediciones' => [
                'mq7_co' => 1234,
                'mq135_aire' => 5678,
                'alerta' => false
            ]
        ]
    ]);
    echo "Status: " . $resp->getStatusCode() . "\n";
    echo "Body: " . $resp->getBody() . "\n\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Check DB
$latest = DB::table('sensor_data')->orderBy('created_at', 'desc')->first();
echo "Último registro:\n";
echo json_encode($latest, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
