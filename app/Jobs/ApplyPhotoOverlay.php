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
use Intervention\Image\Typography\FontFactory;

class ApplyPhotoOverlay implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    // Overlay logo placed top-right
    private const LOGO_PATH     = 'assets/Images/overlay/1781076736_angel-motors-logo-top-dealer-logo.jpg';
    private const FONT_REGULAR  = 'assets/Images/overlay/arial.ttf';
    private const FONT_BOLD     = 'assets/Images/overlay/arialbd.ttf';

    // Layout constants (tuned for typical vehicle photo widths ~800-1200 px)
    private const TOP_BANNER_HEIGHT   = 70;   // px height of top strip
    private const BOTTOM_BANNER_HEIGHT = 36;  // px height of bottom domain bar
    private const PHONE_STRIP_HEIGHT  = 50;   // px height of bottom-left phone strip
    private const LOGO_MAX_WIDTH      = 200;  // cap logo width in the top-right panel
    private const LOGO_PADDING        = 8;    // padding inside logo panel

    public function __construct(
        public VehiclePhoto $photo,
        public string $overlayPath, // kept for BC but unused — layout is now self-contained
    ) {}

    public function handle(): void
    {
        $vehicle = $this->photo->vehicle;

        if (! $vehicle) {
            return;
        }

        $dealer = $vehicle->dealer;
        if (! $dealer) {
            return;
        }

        $disk = Storage::disk($this->photo->disk);

        $sourcePath = $disk->path($this->photo->original_path);

        if (! file_exists($sourcePath)) {
            return;
        }

        // Build primary folder path
        $filename    = basename($this->photo->original_path);
        $primaryPath = "dealers/{$vehicle->dealer_id}/media/primary/{$vehicle->slug}/{$filename}";
        $outputPath  = $disk->path($primaryPath);

        // Ensure the directory exists
        $outputDir = dirname($outputPath);
        if (! is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        // Resolve font paths
        $fontRegular = public_path(self::FONT_REGULAR);
        $fontBold    = public_path(self::FONT_BOLD);
        $logoPath    = public_path(self::LOGO_PATH);

        // Dealer info
        $dealerName   = strtoupper($dealer->name ?? $dealer->company_name ?? 'DEALER');
        $dealerPhone  = $dealer->phone ?? '';
        $dealerDomain = $dealer->domain ?? $dealer->staging_domain ?? '';
        // Strip protocol for display
        $displayDomain = preg_replace('#^https?://#', '', $dealerDomain);

        $manager = new ImageManager(new Driver);
        $image   = $manager->decode($sourcePath);

        $imgW = $image->width();
        $imgH = $image->height();

        // ── TOP BANNER ────────────────────────────────────────────────────────
        // Left half: dark background, dealer name
        // Right half: white background, logo image
        $halfW = (int) ($imgW / 2);

        // Draw dark left panel
        $image->drawRectangle(function ($rect) use ($halfW) {
            $rect->at(0, 0);
            $rect->size($halfW, self::TOP_BANNER_HEIGHT);
            $rect->background('rgba(15,25,50,0.88)');
        });

        // Dealer name (top-left panel)
        $nameFontSize = max(16, min(26, (int) ($halfW / 14)));
        $image->text($dealerName, 18, (int) (self::TOP_BANNER_HEIGHT / 2) + 2, function (FontFactory $font) use ($fontBold, $nameFontSize) {
            $font->file($fontBold);
            $font->size($nameFontSize);
            $font->color('ffffff');
            $font->align('left');
        });

        // Draw white right panel
        $image->drawRectangle(function ($rect) use ($imgW, $halfW) {
            $rect->at($halfW, 0);
            $rect->size($imgW - $halfW, self::TOP_BANNER_HEIGHT);
            $rect->background('rgba(255,255,255,0.92)');
        });

        // Insert logo into top-right panel, scaled to fit
        if (file_exists($logoPath)) {
            $logoManager = new ImageManager(new Driver);
            $logo        = $logoManager->decode($logoPath);

            $panelW  = $imgW - $halfW - (self::LOGO_PADDING * 2);
            $panelH  = self::TOP_BANNER_HEIGHT - (self::LOGO_PADDING * 2);
            $maxW    = min($panelW, self::LOGO_MAX_WIDTH);

            // Scale logo proportionally to fit panel
            $lW = $logo->width();
            $lH = $logo->height();
            $scale = min($maxW / $lW, $panelH / $lH, 1.0);
            $newLW  = (int) ($lW * $scale);
            $newLH  = (int) ($lH * $scale);
            $logo->resize($newLW, $newLH);

            // Right-align inside the right panel, vertically centered
            $logoX = $imgW - $newLW - self::LOGO_PADDING;
            $logoY = (int) ((self::TOP_BANNER_HEIGHT - $newLH) / 2);

            $image->insert($logo, $logoX, $logoY, 'top-left');
        }

        // ── BOTTOM PHONE STRIP (left side) ───────────────────────────────────
        if ($dealerPhone) {
            $phoneStripW = (int) ($imgW * 0.45);
            $phoneStripY = $imgH - self::BOTTOM_BANNER_HEIGHT - self::PHONE_STRIP_HEIGHT;

            // Dark left segment for "CALL / TEXT" label
            $labelW = (int) ($phoneStripW * 0.22);
            $image->drawRectangle(function ($rect) use ($labelW, $phoneStripY) {
                $rect->at(0, $phoneStripY);
                $rect->size($labelW, self::PHONE_STRIP_HEIGHT);
                $rect->background('rgba(15,25,50,0.88)');
            });

            $labelFontSize = max(8, min(12, (int) ($labelW / 5)));
            $labelCenterX  = (int) ($labelW / 2);
            $labelCenterY  = $phoneStripY + (int) (self::PHONE_STRIP_HEIGHT / 2);

            $image->text('CALL', $labelCenterX, $labelCenterY - 6, function (FontFactory $font) use ($fontBold, $labelFontSize) {
                $font->file($fontBold);
                $font->size($labelFontSize);
                $font->color('ffffff');
                $font->align('center');
            });
            $image->text('TEXT', $labelCenterX, $labelCenterY + 8, function (FontFactory $font) use ($fontBold, $labelFontSize) {
                $font->file($fontBold);
                $font->size($labelFontSize);
                $font->color('ffffff');
                $font->align('center');
            });

            // White phone number segment
            $image->drawRectangle(function ($rect) use ($phoneStripW, $labelW, $phoneStripY) {
                $rect->at($labelW, $phoneStripY);
                $rect->size($phoneStripW - $labelW, self::PHONE_STRIP_HEIGHT);
                $rect->background('rgba(255,255,255,0.92)');
            });

            $phoneFontSize = max(18, min(28, (int) (($phoneStripW - $labelW) / 9)));
            $phoneCenterX  = $labelW + (int) (($phoneStripW - $labelW) / 2);
            $phoneCenterY  = $phoneStripY + (int) (self::PHONE_STRIP_HEIGHT / 2);

            $image->text($dealerPhone, $phoneCenterX, $phoneCenterY, function (FontFactory $font) use ($fontBold, $phoneFontSize) {
                $font->file($fontBold);
                $font->size($phoneFontSize);
                $font->color('0f1932');
                $font->align('center');
            });
        }

        // ── BOTTOM DOMAIN BAR ────────────────────────────────────────────────
        if ($displayDomain) {
            $barY = $imgH - self::BOTTOM_BANNER_HEIGHT;

            $image->drawRectangle(function ($rect) use ($imgW, $barY) {
                $rect->at(0, $barY);
                $rect->size($imgW, self::BOTTOM_BANNER_HEIGHT);
                $rect->background('rgba(15,25,50,0.90)');
            });

            $domainFontSize = max(12, min(20, (int) ($imgW / 40)));
            $image->text(strtoupper($displayDomain), (int) ($imgW / 2), $barY + (int) (self::BOTTOM_BANNER_HEIGHT / 2), function (FontFactory $font) use ($fontBold, $domainFontSize) {
                $font->file($fontBold);
                $font->size($domainFontSize);
                $font->color('7ec8e3');
                $font->align('center');
            });
        }

        $image->save($outputPath);

        // Build URL with dealer domain so frontend images don't use APP_URL
        $dealerDomainForUrl = $dealer->domain ?? $dealer->staging_domain;
        $url = $dealerDomainForUrl
            ? 'https://'.$dealerDomainForUrl.'/storage/'.$primaryPath
            : $disk->url($primaryPath);

        $this->photo->update([
            'path' => $primaryPath,
            'url'  => $url,
        ]);
    }
}
