<?php
// Script to dispatch a local request through the Laravel kernel and show output
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
// Change the URI to test
$uri = '/jurnal/get-siswa-ekskul/1';
$request = Illuminate\Http\Request::create($uri, 'GET');
$response = $kernel->handle($request);
$status = $response->getStatusCode();
$headers = $response->headers->allPreserveCase();
$content = $response->getContent();
echo "Status: $status\n";
echo "Headers:\n";
foreach ($headers as $k => $v) {
    echo "  $k: " . implode(', ', $v) . "\n";
}

echo "\nContent:\n";
echo $content . "\n";
$kernel->terminate($request, $response);
