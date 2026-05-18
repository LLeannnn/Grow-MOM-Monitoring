<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
try {
    $pdf = Barryvdh\DomPDF\Facade\Pdf::loadHTML('<h1>Test</h1>');
    echo get_class($pdf);
    echo "\nPDF loaded successfully.\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
