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



    private const HEADER_HEIGHT = 95;
    private const CONTACT_HEIGHT = 55;
    private const GUARANTEE_HEIGHT = 30;



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



        $imgW =
            $image->width();



        $imgH =
            $image->height();




        /*
        |--------------------------------------------------------------------------
        | SCALING FACTOR (Based on standard 800px baseline width)
        |--------------------------------------------------------------------------
        */
        $scale = $imgW / 800.0;
        $headerH = (int)(self::HEADER_HEIGHT * $scale);
        $contactH = (int)(self::CONTACT_HEIGHT * $scale);
        $guaranteeH = (int)(self::GUARANTEE_HEIGHT * $scale);

        /*
        |--------------------------------------------------------------------------
        | TOP HEADER
        |--------------------------------------------------------------------------
        */

        // 1. Draw full white header background first
        $image->drawRectangle(function($rect) use ($imgW, $headerH) {
            $rect->at(0, 0);
            $rect->size($imgW, $headerH);
            $rect->background('ffffff');
        });

        // 2. Draw Top Left Blue Banner (pointing end shape)
        $blueBannerW = (int)($imgW * 0.53);
        $blueBannerH = (int)(63 * $scale);
        $arrowTipWidth = (int)(35 * $scale);

        $image->drawPolygon(function($polygon) use ($blueBannerW, $blueBannerH, $arrowTipWidth) {
            $polygon->point(0, 0);
            $polygon->point($blueBannerW, 0);
            $polygon->point($blueBannerW + $arrowTipWidth, (int)($blueBannerH / 2));
            $polygon->point($blueBannerW, $blueBannerH);
            $polygon->point(0, $blueBannerH);
            $polygon->background('3b698a');
        });

        // 3. Draw Top Left Black Sub-Banner (slanted end shape)
        $blackBannerH = $headerH - $blueBannerH;

        $image->drawPolygon(function($polygon) use ($blueBannerW, $blueBannerH, $headerH, $scale) {
            $polygon->point(0, $blueBannerH);
            $polygon->point($blueBannerW - (int)(10 * $scale), $blueBannerH);
            $polygon->point($blueBannerW - (int)(45 * $scale), $headerH);
            $polygon->point(0, $headerH);
            $polygon->background('000000');
        });

        // 4. Render Dealer Name (white text on blue banner)
        $dealerNameSize = (int)(26 * $scale);
        $image->text(
            $dealerName,
            (int)(35 * $scale),
            (int)(($blueBannerH / 2) + ($dealerNameSize * 0.35)),
            function($font) use ($fontBold, $dealerNameSize) {
                $font->file($fontBold);
                $font->size($dealerNameSize);
                $font->color('ffffff');
                $font->align('left');
            }
        );

        // 5. Render Spanish Sub-Text (white text on black banner)
        $subBannerSize = (int)(11 * $scale);
        $image->text(
            'HABLAMOS ESPAÑOL',
            (int)(35 * $scale),
            $blueBannerH + (int)(($blackBannerH / 2) + ($subBannerSize * 0.35)),
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

            $maxWidth = (int)(220 * $scale);
            $maxHeight = $headerH - (int)(20 * $scale);

            $scaleLogo = min(
                $maxWidth / $logo->width(),
                $maxHeight / $logo->height()
            );

            $logo->resize(
                (int)($logo->width() * $scaleLogo),
                (int)($logo->height() * $scaleLogo)
            );

            $image->insert(
                $logo,
                $imgW - $logo->width() - (int)(20 * $scale),
                (int)(($headerH - $logo->height()) / 2),
                'top-left'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | CONTACT BAR
        |--------------------------------------------------------------------------
        */

        $contactY = $imgH - $guaranteeH - $contactH;

        // 1. Draw black background for the bottom bar first
        $image->drawRectangle(function($rect) use ($imgW, $contactY, $contactH) {
            $rect->at(0, $contactY);
            $rect->size($imgW, $contactH);
            $rect->background('000000');
        });

        // 2. Draw white polygon on the left (slanting down-right)
        $bottomLeftW = (int)($imgW * 0.48);
        $slant = (int)(36 * $scale);

        $image->drawPolygon(function($polygon) use ($bottomLeftW, $contactY, $contactH, $slant) {
            $polygon->point(0, $contactY);
            $polygon->point($bottomLeftW, $contactY);
            $polygon->point($bottomLeftW - $slant, $contactY + $contactH);
            $polygon->point(0, $contactY + $contactH);
            $polygon->background('ffffff');
        });

        // 3. Render CALL/TEXT stacked label in white section
        $lblSize = (int)(11 * $scale);
        $image->text('CALL', (int)(45 * $scale), $contactY + (int)(21 * $scale), function($font) use ($fontBold, $lblSize) {
            $font->file($fontBold);
            $font->size($lblSize);
            $font->color('0f1932');
            $font->align('center');
        });
        $image->text('TEXT', (int)(45 * $scale), $contactY + (int)(36 * $scale), function($font) use ($fontBold, $lblSize) {
            $font->file($fontBold);
            $font->size($lblSize);
            $font->color('0f1932');
            $font->align('center');
        });

        // 4. Render Phone Number in white section
        $phoneSize = (int)(30 * $scale);
        $image->text(
            $dealerPhone,
            (int)(90 * $scale),
            $contactY + (int)(($contactH / 2) + ($phoneSize * 0.32)),
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
            $domainSize = (int)(18 * $scale);
            $rightX = $imgW - (int)(30 * $scale);
            $centerY = $contactY + (int)(($contactH / 2) + ($domainSize * 0.32));

            if (str_starts_with($displayDomain, 'WWW.')) {
                $prefix = 'WWW.';
                $mainDomain = substr($displayDomain, 4);

                // Right-align the main domain in white
                $image->text($mainDomain, $rightX, $centerY, function($font) use ($fontBold, $domainSize) {
                    $font->file($fontBold);
                    $font->size($domainSize);
                    $font->color('ffffff');
                    $font->align('right');
                });

                // Calculate width of main domain to place WWW. in blue (chars width approx 0.65 of size)
                $charWidth = $domainSize * 0.60;
                $mainDomainLength = strlen($mainDomain);
                $prefixX = $rightX - (int)($mainDomainLength * $charWidth);

                $image->text($prefix, $prefixX, $centerY, function($font) use ($fontBold, $domainSize) {
                    $font->file($fontBold);
                    $font->size($domainSize);
                    $font->color('3b698a');
                    $font->align('right');
                });
            } else {
                $image->text($displayDomain, $rightX, $centerY, function($font) use ($fontBold, $domainSize) {
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

        $guaranteeY = $imgH - $guaranteeH;

        // 1. Draw blue background for the footer
        $image->drawRectangle(function($rect) use ($imgW, $guaranteeY, $guaranteeH) {
            $rect->at(0, $guaranteeY);
            $rect->size($imgW, $guaranteeH);
            $rect->background('3b698a');
        });

        // 2. Render footer text
        $footerSize = (int)(14 * $scale);
        $image->text(
            'GUARANTEED APPROVAL & 3-MONTH/3000-MILE WARRANTY',
            (int)($imgW / 2),
            $guaranteeY + (int)(($guaranteeH / 2) + ($footerSize * 0.32)),
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


        $image->save($outputPath, quality: 95);




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