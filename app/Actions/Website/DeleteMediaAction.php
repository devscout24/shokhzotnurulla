<?php

namespace App\Actions\Website;

use App\Models\Website\Media;
use App\Models\Inventory\VehiclePhoto;
use App\Actions\Inventory\DeletePhotoAction;
use Illuminate\Support\Facades\Storage;

class DeleteMediaAction
{
    public function __construct(
        private readonly DeletePhotoAction $deletePhoto,
    ) {}

    public function execute(Media $media): void
    {
        $vehiclePhoto = VehiclePhoto::where('path', $media->path)->first();

        if ($vehiclePhoto) {
            ($this->deletePhoto)($vehiclePhoto);
        } else {
            Storage::disk($media->disk)->delete($media->path);
        }

        $media->delete();
    }
}