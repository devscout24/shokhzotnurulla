<?php

namespace App\Jobs;

use App\Models\Inventory\VehiclePhoto;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ApplyPhotoOverlay implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    public function __construct(
        public VehiclePhoto $photo,
        public string $overlayPath,
    ) {}

    public function handle(): void
    {
        $disk = Storage::disk($this->photo->disk);

        $sourcePath = $disk->path($this->photo->original_path);
        $outputPath = $disk->path($this->photo->path);

        if (! file_exists($sourcePath)) {
            return;
        }

        $manager = new ImageManager(new Driver());
        $image = $manager->read($sourcePath);

        $image->insert($this->overlayPath);

        $image->save($outputPath);
    }
}
