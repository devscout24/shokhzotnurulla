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


    private const LOGO_PATH =
        'assets/Images/overlay/1781076736_angel-motors-logo-top-dealer-logo.jpg';


    private const FONT_REGULAR =
        'assets/Images/overlay/arial.ttf';


    private const FONT_BOLD =
        'assets/Images/overlay/arialbd.ttf';


    public function __construct(
        public VehiclePhoto $photo,
        public string $overlayPath,
    ) {}



    public function handle(): void
    {
        $vehicle = $this->photo->vehicle;

        if (!$vehicle) {
            return;
        }


        $dealer = $vehicle->dealer;

        if (!$dealer) {
            return;
        }



        $disk = Storage::disk($this->photo->disk);


        $sourcePath =
            $disk->path($this->photo->original_path);



        if (!file_exists($sourcePath)) {
            return;
        }



        $filename =
            basename($this->photo->original_path);



        $primaryPath =
            "dealers/{$vehicle->dealer_id}/media/primary/{$vehicle->slug}/{$filename}";



        $outputPath =
            $disk->path($primaryPath);



        if (!is_dir(dirname($outputPath))) {

            mkdir(
                dirname($outputPath),
                0755,
                true
            );

        }



        $fontBold =
            public_path(self::FONT_BOLD);



        $logoPath =
            public_path(self::LOGO_PATH);




        $dealerName =
            strtoupper(
                $dealer->name ??
                $dealer->company_name ??
                'DEALER'
            );



        $dealerPhone =
            $dealer->phone ?? '';



        $dealerDomain =
            $dealer->domain ??
            $dealer->staging_domain ??
            '';



        $displayDomain =
            preg_replace(
                '#^https?://#',
                '',
                $dealerDomain
            );




        $manager =
            new ImageManager(
                new Driver()
            );



        $image =
            $manager->decode($sourcePath);



        $imgW = $image->width();
        $imgH = $image->height();

        // Ensure high-resolution overlay by upscaling if the image is below 1600px width
        $targetWidth = 1600;
        if ($imgW < $targetWidth) {
            $aspectRatio = $imgH / $imgW;
            $targetHeight = (int)($targetWidth * $aspectRatio);
            $image->resize($targetWidth, $targetHeight);
            $imgW = $targetWidth;
            $imgH = $targetHeight;
        }




        /*
        |--------------------------------------------------------------------------
        | SCALING FACTOR (Based on cqw - 1% of image width)
        |--------------------------------------------------------------------------
        */
        $cqw = $imgW * 0.01;
        $topBarH = 13.5 * $cqw;
        $topLeftW = 56 * $cqw;
        $blueBannerH = 9.0 * $cqw;
        $blackSubBannerH = 4.5 * $cqw;
        $topRightH = 10 * $cqw;

        $footerH = 4.8 * $cqw;
        $bottomBarH = 8.5 * $cqw;
        $contactY = $imgH - $footerH - $bottomBarH;

        /*
        |--------------------------------------------------------------------------
        | TOP HEADER
        |--------------------------------------------------------------------------
        */

        // 1. Draw full white header background first (top-right fallback + panel)
        $image->drawRectangle(function($rect) use ($imgW, $topRightH) {
            $rect->at(0, 0);
            $rect->size($imgW, (int)$topRightH);
            $rect->background('ffffff');
        });

        // 2. Draw Top Left Blue Banner (pointing end shape)
        $image->drawPolygon(function($polygon) use ($topLeftW, $blueBannerH, $cqw) {
            $polygon->point(0, 0);
            $polygon->point((int)$topLeftW, 0);
            $polygon->point((int)($topLeftW - 4.5 * $cqw), (int)$blueBannerH);
            $polygon->point(0, (int)$blueBannerH);
            $polygon->background('3b698a');
        });

        // 3. Draw Top Left Black Sub-Banner (slanted end shape)
        $image->drawPolygon(function($polygon) use ($topLeftW, $blueBannerH, $topBarH, $cqw) {
            $polygon->point(0, (int)$blueBannerH);
            $polygon->point((int)($topLeftW - 6.5 * $cqw), (int)$blueBannerH);
            $polygon->point((int)($topLeftW - 8.5 * $cqw), (int)$topBarH);
            $polygon->point(0, (int)$topBarH);
            $polygon->background('000000');
        });

        // 4. Render Dealer Name (white text on blue banner)
        $dealerNameSize = 3.5 * $cqw;
        $image->text(
            $dealerName,
            (int)(3.5 * $cqw),
            (int)(($blueBannerH / 2) + ($dealerNameSize * 0.35)),
            function($font) use ($fontBold, $dealerNameSize) {
                $font->file($fontBold);
                $font->size($dealerNameSize);
                $font->color('ffffff');
                $font->align('left');
            }
        );

        // 5. Render Spanish Sub-Text (white text on black banner)
        $subBannerSize = 1.4 * $cqw;
        $image->text(
            'HABLAMOS ESPAÑOL',
            (int)(3.5 * $cqw),
            (int)($blueBannerH + ($blackSubBannerH / 2) + ($subBannerSize * 0.35)),
            function($font) use ($fontBold, $subBannerSize) {
                $font->file($fontBold);
                $font->size($subBannerSize);
                $font->color('ffffff');
                $font->align('left');
            }
        );

        // 6. Insert Dealer Logo to Top Right (if exists, scaled to fit)
        if (file_exists($logoPath)) {
            $logoManager = new ImageManager(new Driver());
            $logo = $logoManager->decode($logoPath);

            $logoTargetH = (int)(7.5 * $cqw);
            $logoScale = $logoTargetH / $logo->height();
            $logoW = (int)($logo->width() * $logoScale);

            $logo->resize($logoW, $logoTargetH);

            $image->insert(
                $logo,
                (int)($imgW - $logoW - 3.5 * $cqw),
                (int)(($topRightH - $logoTargetH) / 2),
                'top-left'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | CONTACT BAR (BOTTOM)
        |--------------------------------------------------------------------------
        */

        // 1. Draw black background for the bottom bar first
        $image->drawRectangle(function($rect) use ($imgW, $contactY, $bottomBarH) {
            $rect->at(0, (int)$contactY);
            $rect->size($imgW, (int)$bottomBarH);
            $rect->background('000000');
        });

        // 2. Draw white polygon on the left (slanting down-right)
        $bottomLeftW = 48 * $cqw;
        $bottomLeftSlant = 4.5 * $cqw;

        $image->drawPolygon(function($polygon) use ($bottomLeftW, $contactY, $bottomBarH, $bottomLeftSlant) {
            $polygon->point(0, (int)$contactY);
            $polygon->point((int)$bottomLeftW, (int)$contactY);
            $polygon->point((int)($bottomLeftW - $bottomLeftSlant), (int)($contactY + $bottomBarH));
            $polygon->point(0, (int)($contactY + $bottomBarH));
            $polygon->background('ffffff');
        });

        // 3. Render CALL/TEXT stacked label in white section
        $lblSize = 1.3 * $cqw;
        $image->text('CALL', (int)(4.5 * $cqw), (int)($contactY + 2.8 * $cqw), function($font) use ($fontBold, $lblSize) {
            $font->file($fontBold);
            $font->size($lblSize);
            $font->color('0f1932');
            $font->align('center');
        });
        $image->text('TEXT', (int)(4.5 * $cqw), (int)($contactY + 5.3 * $cqw), function($font) use ($fontBold, $lblSize) {
            $font->file($fontBold);
            $font->size($lblSize);
            $font->color('0f1932');
            $font->align('center');
        });

        // 4. Render Phone Number in white section
        $phoneSize = 4.0 * $cqw;
        $image->text(
            $dealerPhone,
            (int)(9.5 * $cqw),
            (int)($contactY + ($bottomBarH / 2) + ($phoneSize * 0.32)),
            function($font) use ($fontBold, $phoneSize) {
                $font->file($fontBold);
                $font->size($phoneSize);
                $font->color('3b698a');
                $font->align('left');
            }
        );

        // 5. Render Domain in black section
        if ($displayDomain) {
            $displayDomain = strtoupper($displayDomain);
            $domainSize = 2.2 * $cqw;
            $rightX = $imgW - (int)(3.5 * $cqw);
            $centerY = $contactY + ($bottomBarH / 2) + ($domainSize * 0.32);

            if (str_starts_with($displayDomain, 'WWW.')) {
                $prefix = 'WWW.';
                $mainDomain = substr($displayDomain, 4);

                // Right-align the main domain in white
                $image->text($mainDomain, $rightX, (int)$centerY, function($font) use ($fontBold, $domainSize) {
                    $font->file($fontBold);
                    $font->size($domainSize);
                    $font->color('ffffff');
                    $font->align('right');
                });

                // Calculate width of main domain to place WWW. in blue (chars width approx 0.60 of size)
                $charWidth = $domainSize * 0.60;
                $mainDomainLength = strlen($mainDomain);
                $prefixX = $rightX - (int)($mainDomainLength * $charWidth);

                $image->text($prefix, $prefixX, (int)$centerY, function($font) use ($fontBold, $domainSize) {
                    $font->file($fontBold);
                    $font->size($domainSize);
                    $font->color('3b698a');
                    $font->align('right');
                });
            } else {
                $image->text($displayDomain, $rightX, (int)$centerY, function($font) use ($fontBold, $domainSize) {
                    $font->file($fontBold);
                    $font->size($domainSize);
                    $font->color('ffffff');
                    $font->align('right');
                });
            }
        }

        /*
        |--------------------------------------------------------------------------
        | GUARANTEE BAR (FOOTER)
        |--------------------------------------------------------------------------
        */

        $guaranteeY = $imgH - $footerH;

        // 1. Draw blue background for the footer
        $image->drawRectangle(function($rect) use ($imgW, $guaranteeY, $footerH) {
            $rect->at(0, (int)$guaranteeY);
            $rect->size($imgW, (int)$footerH);
            $rect->background('3b698a');
        });

        // 2. Render footer text
        $footerSize = 1.9 * $cqw;
        $image->text(
            'GUARANTEED APPROVAL & 3-MONTH/3000-MILE WARRANTY',
            (int)($imgW / 2),
            (int)($guaranteeY + ($footerH / 2) + ($footerSize * 0.32)),
            function($font) use ($fontBold, $footerSize) {
                $font->file($fontBold);
                $font->size($footerSize);
                $font->color('ffffff');
                $font->align('center');
            }
        );

        /*
        |--------------------------------------------------------------------------
        | SAVE IMAGE
        |--------------------------------------------------------------------------
        */

        $image->save($outputPath, quality: 95);;




        /*
        |--------------------------------------------------------------------------
        | UPDATE DATABASE
        |--------------------------------------------------------------------------
        */


        $dealerDomainForUrl =
            $dealer->domain ??
            $dealer->staging_domain;




        $url =
            $dealerDomainForUrl

                ? 'https://'.$dealerDomainForUrl.'/storage/'.$primaryPath

                : $disk->url($primaryPath);





        $this->photo->update([

            'path' => $primaryPath,

            'url' => $url,

        ]);

    }
}