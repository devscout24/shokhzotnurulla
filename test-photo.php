<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Inventory\VehiclePhoto;

$photos = VehiclePhoto::all();
foreach ($photos as $photo) {
    echo "ID: {$photo->id}\n";
    echo "Vehicle ID: {$photo->vehicle_id}\n";
    echo "Path: {$photo->path}\n";
    echo "Original Path: {$photo->original_path}\n";
    echo "Is Primary: " . ($photo->is_primary ? 'YES' : 'NO') . "\n";
    echo "Status: {$photo->status}\n";
    echo "File Exists: " . (Illuminate\Support\Facades\Storage::disk($photo->disk)->exists($photo->path) ? 'YES' : 'NO') . "\n";
    echo "---------------------------------\n";
}
