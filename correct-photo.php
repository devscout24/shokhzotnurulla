<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Inventory\VehiclePhoto;
use Illuminate\Support\Facades\Storage;

$photo = VehiclePhoto::find(14);
if ($photo) {
    if ($photo->path !== $photo->original_path) {
        echo "Deleting primary overlaid file...\n";
        Storage::disk($photo->disk)->delete($photo->path);
    }
    
    $dealer = $photo->vehicle->dealer;
    $dealerDomain = $dealer?->domain ?? $dealer?->staging_domain;
    $originalUrl = $dealerDomain
        ? 'https://'.$dealerDomain.'/storage/'.$photo->original_path
        : Storage::disk($photo->disk)->url($photo->original_path);
        
    $photo->update([
        'path' => $photo->original_path,
        'url' => $originalUrl
    ]);
    
    echo "Corrected photo ID 14 in database.\n";
}
