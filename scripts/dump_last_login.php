<?php
require __DIR__ . '/../vendor/autoload.php';
$app    = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\LoginHistory;

$record = LoginHistory::latest()->first();
if (! $record) {
    echo "No login history records found\n";
    exit(0);
}

print_r($record->toArray());
