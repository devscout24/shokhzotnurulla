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
        | TOP HEADER
        |--------------------------------------------------------------------------
        */



        $leftWidth =
            (int)($imgW * .65);




        // Dark dealer section

        $image->drawRectangle(function($rect) use ($leftWidth){

            $rect->at(0,0);

            $rect->size(
                $leftWidth,
                self::HEADER_HEIGHT
            );

            $rect->background(
                'rgba(15,25,50,0.95)'
            );

        });




        // White logo section

        $image->drawRectangle(function($rect) use ($imgW,$leftWidth){

            $rect->at(
                $leftWidth + 35,
                0
            );

            $rect->size(
                $imgW - $leftWidth,
                self::HEADER_HEIGHT
            );

            $rect->background(
                'ffffff'
            );

        });




        /*
        |--------------------------------------------------------------------------
        | CHEVRON
        |--------------------------------------------------------------------------
        */

        $image->drawPolygon(function($polygon) use ($leftWidth) {
            $polygon->point($leftWidth - 10, 0);
            $polygon->point($leftWidth + 25, 0);
            $polygon->point($leftWidth + 70, 47);
            $polygon->point($leftWidth + 25, 95);
            $polygon->point($leftWidth - 10, 95);
            $polygon->point($leftWidth + 25, 47);
            $polygon->background('ffffff');
        });




        // Dealer name

        $image->text(
            $dealerName,
            25,
            35,
            function(FontFactory $font) use ($fontBold){

                $font->file($fontBold);

                $font->size(26);

                $font->color('ffffff');

                $font->align('left');

            }
        );




        // Spanish text

        $image->text(
            'HABLAMOS ESPAÑOL',
            25,
            70,
            function(FontFactory $font) use ($fontBold){

                $font->file($fontBold);

                $font->size(14);

                $font->color('ffffff');

                $font->align('left');

            }
        );
                /*
        |--------------------------------------------------------------------------
        | LOGO
        |--------------------------------------------------------------------------
        */


        if (file_exists($logoPath)) {


            $logoManager =
                new ImageManager(
                    new Driver()
                );


            $logo =
                $logoManager->decode($logoPath);



            $maxWidth = 220;



            $scale =
                min(
                    $maxWidth / $logo->width(),
                    (self::HEADER_HEIGHT - 20) / $logo->height()
                );



            $logo->resize(
                (int)($logo->width() * $scale),
                (int)($logo->height() * $scale)
            );



            $image->insert(
                $logo,
                $imgW - $logo->width() - 20,
                (int)((self::HEADER_HEIGHT - $logo->height()) / 2)
            );

        }




        /*
        |--------------------------------------------------------------------------
        | CONTACT BAR
        |--------------------------------------------------------------------------
        */


        $contactY =
            $imgH -
            self::GUARANTEE_HEIGHT -
            self::CONTACT_HEIGHT;




        $image->drawRectangle(function($rect) use ($imgW,$contactY){

            $rect->at(
                0,
                $contactY
            );


            $rect->size(
                $imgW,
                self::CONTACT_HEIGHT
            );


            $rect->background(
                'ffffff'
            );

        });





        /*
        |--------------------------------------------------------------------------
        | CALL / TEXT BLOCK
        |--------------------------------------------------------------------------
        */


        $image->drawRectangle(function($rect) use ($contactY){

            $rect->at(
                0,
                $contactY
            );


            $rect->size(
                90,
                self::CONTACT_HEIGHT
            );


            $rect->background(
                'rgba(15,25,50,0.95)'
            );

        });




        $image->text(
            "CALL\nTEXT",
            45,
            $contactY + 28,
            function(FontFactory $font) use ($fontBold){


                $font->file($fontBold);

                $font->size(11);

                $font->color('ffffff');

                $font->align('center');


            }
        );





        /*
        |--------------------------------------------------------------------------
        | PHONE
        |--------------------------------------------------------------------------
        */


        $image->text(
            $dealerPhone,
            (int)($imgW * .35),
            $contactY + 35,
            function(FontFactory $font) use ($fontBold){


                $font->file($fontBold);

                $font->size(30);

                $font->color('0f1932');

                $font->align('center');


            }
        );






        /*
        |--------------------------------------------------------------------------
        | DOMAIN
        |--------------------------------------------------------------------------
        */


        $image->text(
            strtoupper($displayDomain),
            $imgW - 160,
            $contactY + 35,
            function(FontFactory $font) use ($fontBold){


                $font->file($fontBold);

                $font->size(17);

                $font->color('0f1932');

                $font->align('center');


            }
        );





        /*
        |--------------------------------------------------------------------------
        | GUARANTEE BAR
        |--------------------------------------------------------------------------
        */


        $guaranteeY =
            $imgH -
            self::GUARANTEE_HEIGHT;





        $image->drawRectangle(function($rect) use ($imgW,$guaranteeY){


            $rect->at(
                0,
                $guaranteeY
            );


            $rect->size(
                $imgW,
                self::GUARANTEE_HEIGHT
            );


            $rect->background(
                'rgba(15,25,50,0.95)'
            );


        });





        $image->text(
            'GUARANTEED APPROVAL & 3-MONTH/3000-MILE WARRANTY',
            $imgW / 2,
            $guaranteeY + 20,
            function(FontFactory $font) use ($fontBold){


                $font->file($fontBold);

                $font->size(15);

                $font->color('ffffff');

                $font->align('center');


            }
        );
                /*
        |--------------------------------------------------------------------------
        | SAVE IMAGE
        |--------------------------------------------------------------------------
        */


        $image->save($outputPath);




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