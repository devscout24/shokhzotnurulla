<?php

namespace App\Actions\Inventory;

use App\Models\Inventory\Vehicle;
use Illuminate\Support\Facades\DB;

class UpdateTagsAction
{
    public function __invoke(Vehicle $vehicle, array $data): void
    {
        $tags = collect($data['tags'] ?? [])
            ->filter()
            ->unique()
            ->map(fn (string $tag) => ['vehicle_id' => $vehicle->id, 'tag' => trim($tag)])
            ->values()
            ->all();

        DB::transaction(function () use ($vehicle, $tags) {
            $vehicle->tags()->delete();
            $vehicle->tags()->createMany($tags);
        });
    }
}