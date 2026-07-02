<?php

namespace App\Jobs;

use App\Models\Inventory\VehiclePhoto;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ApplyPhotoOverlay implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    public function __construct(
        public VehiclePhoto $photo,
        public string $overlayPath,
    ) {}

    public function handle(): void
    {
        $vehicle = $this->photo->vehicle;

        if (! $vehicle) {
            return;
        }

        $disk = Storage::disk($this->photo->disk);

        $sourcePath = $disk->path($this->photo->original_path);

        if (! file_exists($sourcePath)) {
            return;
        }

        // Build primary folder path: dealers/{dealer_id}/media/primary/{vehicle_slug}/{filename}
        $filename = basename($this->photo->original_path);
        $primaryPath = "dealers/{$vehicle->dealer_id}/media/primary/{$vehicle->slug}/{$filename}";

        $outputPath = $disk->path($primaryPath);

        // Ensure the directory exists
        $outputDir = dirname($outputPath);
        if (! is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        $manager = new ImageManager(new Driver);
        $image = $manager->decodePath($sourcePath);

        $image->insert($this->overlayPath);

        $image->save($outputPath);

        // Update photo record to point to the new primary path
        $this->photo->update([
            'path' => $primaryPath,
            'url' => $disk->url($primaryPath),
        ]);
    }
}
