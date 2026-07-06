<?php

namespace App\Console\Commands;

use App\Models\Dealership\Dealer;
use App\Models\Inventory\VehiclePhoto;
use Illuminate\Console\Command;

class FixPhotoUrls extends Command
{
    protected $signature = 'photos:fix-urls';

    protected $description = 'Update stored photo URLs to use dealer domain instead of APP_URL';

    public function handle(): int
    {
        $appUrl = rtrim(config('app.url'), '/').'/storage/';
        $count = 0;

        $this->info('Scanning photos with APP_URL-based URLs...');

        VehiclePhoto::query()
            ->where('url', 'like', $appUrl.'%')
            ->chunk(100, function ($photos) use (&$count, $appUrl) {
                foreach ($photos as $photo) {
                    $dealer = Dealer::find($photo->vehicle?->dealer_id);
                    $domain = $dealer?->domain ?? $dealer?->staging_domain;

                    if (! $domain) {
                        $this->warn("Skipping photo {$photo->id} — no domain for dealer {$photo->vehicle?->dealer_id}");

                        continue;
                    }

                    $path = substr($photo->url, strlen($appUrl));
                    $newUrl = 'https://'.$domain.'/storage/'.$path;

                    $photo->update(['url' => $newUrl]);
                    $count++;
                }
            });

        $this->info("Fixed {$count} photo URLs.");

        return Command::SUCCESS;
    }
}
