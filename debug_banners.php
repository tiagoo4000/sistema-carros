<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$banners = App\Models\Banner::all();
echo json_encode($banners, JSON_PRETTY_PRINT);
