<?php

namespace App\Services\Exports;

use App\Models\Dealership\Dealer;
use App\Models\Inventory\Vehicle;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CarfaxExportService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {}

    public function carfaxXml(Dealer $dealer, Request $request)
    {
        $fileName = 'inventory-feed-carfax_'.date('Y-m-d_H-i-s').'.xml';

        $headers = [
            'Content-type' => 'text/xml; charset=utf-8',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($dealer) {
            $output = fopen('php://output', 'w');

            // XML Declaration and root element
            fwrite($output, "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n");
            fwrite($output, "<listings>\n");

            $dealer->load(['locations', 'domains']);
            $location = $dealer->locations->first();

            // Calculate dealer fee
            $dealerFeeVal = '';
            $fees = $dealer->inventoryFees;
            if ($fees->isNotEmpty()) {
                $matchedFee = $fees->first(fn($f) => preg_match('/dealer|doc|admin|prep/i', $f->name));
                $fee = $matchedFee ?? $fees->first();
                $dealerFeeVal = $fee->type === 'amount'
                    ? '$' . number_format($fee->value, 0)
                    : $fee->value . '%';
            }

            $formatPrice = fn($val) => $val > 0 ? number_format($val, 0) : '0';

            Vehicle::withoutGlobalScopes()->where('dealer_id', $dealer->id)
                ->with([
                    'make', 'makeModel', 'bodyType', 'bodyStyle', 'photos', 'notes',
                    'transmissionType', 'drivetrainType', 'exteriorColor', 'interiorColor',
                    'specs', 'prices', 'features', 'factoryOptions', 'premiumOptions',
                    'location', 'dealer.domains'
                ])
                ->whereIn('status', ['active'])
                ->chunk(100, function ($vehicles) use ($output, $dealer, $location, $dealerFeeVal, $formatPrice) {
                    foreach ($vehicles as $vehicle) {
                        // Resolve domain lookup logic
                        $dealerWebsite = '';
                        if ($vehicle->dealer && $vehicle->dealer->relationLoaded('domains') && $vehicle->dealer->domains->isNotEmpty()) {
                            $primaryDomain = $vehicle->dealer->domains->firstWhere('is_primary', true) ?? $vehicle->dealer->domains->first();
                            $dealerWebsite = $primaryDomain?->domain;
                        }
                        if (empty($dealerWebsite)) {
                            $dealerWebsite = $dealer->domain ?? $dealer->staging_domain ?? '';
                        }

                        // Resolve vehicle specific URL
                        $url = $dealerWebsite ? "https://{$dealerWebsite}/vehicles/{$vehicle->slug}" : '';

                        // Retrieve only live images
                        $livePhotos = collect($vehicle->photos)->where('status', 'live')->pluck('url')->all();

                        $standardFeatures = $vehicle->features->pluck('name')->implode(', ');
                        $optionalFeatures = collect($vehicle->factoryOptions->pluck('label'))
                            ->concat($vehicle->premiumOptions->pluck('name'))
                            ->implode(', ');

                        $listingTime = $vehicle->listed_at
                            ? Carbon::parse($vehicle->listed_at)->format('Y-m-d-H:i:s')
                            : ($vehicle->created_at ? Carbon::parse($vehicle->created_at)->format('Y-m-d-H:i:s') : '');

                        $expireTime = $vehicle->expire_time
                            ? Carbon::parse($vehicle->expire_time)->format('Y-m-d-H:i:s')
                            : '';

                        // Price calculations
                        $price = $vehicle->list_price ?? 0;
                        $msrp = $vehicle->prices?->msrp ?? 0;
                        $internetPrice = $vehicle->prices?->internet_price ?? 0;

                        $sellingPrice = $vehicle->prices?->special_price > 0
                            ? $vehicle->prices->special_price
                            : ($vehicle->prices?->asking_price > 0
                                ? $vehicle->prices->asking_price
                                : ($vehicle->prices?->internet_price > 0
                                    ? $vehicle->prices->internet_price
                                    : ($vehicle->list_price ?? 0)));

                        $retailPrice = $vehicle->prices?->msrp > 0
                            ? $vehicle->prices->msrp
                            : ($vehicle->list_price ?? 0);

                        $invoicePrice = $vehicle->prices?->dealer_cost ?? 0;

                        // Resolve location (vehicle specific first, then dealer fallback)
                        $vehicleLocation = $vehicle->location ?? $location;

                        $xml = "    <listing>\n";
                        $xml .= "        <record_id>" . htmlspecialchars($vehicle->ulid ?? $vehicle->id) . "</record_id>\n";
                        $xml .= "        <vin>" . htmlspecialchars($vehicle->vin ?? '') . "</vin>\n";
                        $xml .= "        <stock_number>" . htmlspecialchars($vehicle->stock_number ?? '') . "</stock_number>\n";
                        $xml .= "        <title>" . htmlspecialchars($vehicle->display_title ?? '') . "</title>\n";
                        $xml .= "        <url>" . htmlspecialchars($url) . "</url>\n";
                        $xml .= "        <category>" . htmlspecialchars($vehicle->bodyType?->name ?? 'car') . "</category>\n";

                        // Output live images dynamically (guarantee at least 4 tags, then dynamic additional tags)
                        for ($i = 0; $i < max(4, count($livePhotos)); $i++) {
                            $tagName = $i === 0 ? 'image' : 'image' . ($i + 1);
                            $photoUrl = $livePhotos[$i] ?? '';
                            $xml .= "        <{$tagName}>" . htmlspecialchars($photoUrl) . "</{$tagName}>\n";
                        }

                        $xml .= "        <address>" . htmlspecialchars($vehicleLocation?->street1 ?? '') . "</address>\n";
                        $xml .= "        <city>" . htmlspecialchars($vehicleLocation?->city ?? '') . "</city>\n";
                        $xml .= "        <state>" . htmlspecialchars($vehicleLocation?->state ?? '') . "</state>\n";
                        $xml .= "        <zip>" . htmlspecialchars($vehicleLocation?->postalcode ?? '') . "</zip>\n";
                        $xml .= "        <country>" . htmlspecialchars($vehicleLocation?->country ?? 'United States') . "</country>\n";
                        $xml .= "        <seller_type>Dealer</seller_type>\n";
                        $xml .= "        <dealer_name>" . htmlspecialchars($dealer->company_name ?? $dealer->name ?? '') . "</dealer_name>\n";
                        $xml .= "        <dealer_ID>" . htmlspecialchars($dealer->internal_id ?? $dealer->id) . "</dealer_ID>\n";
                        $xml .= "        <dealer_email>" . htmlspecialchars($dealer->email ?? $dealer->support_email ?? '') . "</dealer_email>\n";
                        $xml .= "        <dealer_phone>" . htmlspecialchars($dealer->phone ?? '') . "</dealer_phone>\n";
                        $xml .= "        <dealer_website>" . htmlspecialchars($dealerWebsite) . "</dealer_website>\n";
                        $xml .= "        <dealer_fee>" . htmlspecialchars($dealerFeeVal) . "</dealer_fee>\n";
                        $xml .= "        <make>" . htmlspecialchars($vehicle->make?->name ?? '') . "</make>\n";
                        $xml .= "        <model>" . htmlspecialchars($vehicle->makeModel?->name ?? '') . "</model>\n";
                        $xml .= "        <trim>" . htmlspecialchars($vehicle->trim ?? '') . "</trim>\n";
                        $xml .= "        <body>" . htmlspecialchars($vehicle->bodyStyle?->name ?? $vehicle->bodyType?->name ?? '') . "</body>\n";
                        $xml .= "        <mileage>" . htmlspecialchars($vehicle->mileage ?? '') . "</mileage>\n";
                        $xml .= "        <year>" . htmlspecialchars($vehicle->year ?? '') . "</year>\n";
                        $xml .= "        <currency>USD</currency>\n";
                        $xml .= "        <price>" . htmlspecialchars($formatPrice($price)) . "</price>\n";
                        $xml .= "        <MSRP>" . htmlspecialchars($formatPrice($msrp)) . "</MSRP>\n";
                        $xml .= "        <internet_price>" . htmlspecialchars($formatPrice($internetPrice)) . "</internet_price>\n";
                        $xml .= "        <selling_price>" . htmlspecialchars($formatPrice($sellingPrice)) . "</selling_price>\n";
                        $xml .= "        <retail_price>" . htmlspecialchars($formatPrice($retailPrice)) . "</retail_price>\n";
                        $xml .= "        <invoice_price>" . htmlspecialchars($formatPrice($invoicePrice)) . "</invoice_price>\n";
                        $xml .= "        <exterior_color>" . htmlspecialchars($vehicle->exteriorColor?->name ?? '') . "</exterior_color>\n";
                        $xml .= "        <interior_color>" . htmlspecialchars($vehicle->interiorColor?->name ?? '') . "</interior_color>\n";
                        $xml .= "        <interior_material>" . htmlspecialchars($vehicle->specs?->interior_material ?? '') . "</interior_material>\n";
                        $xml .= "        <doors>" . htmlspecialchars($vehicle->doors ?? '') . "</doors>\n";
                        $xml .= "        <cylinders>" . htmlspecialchars($vehicle->specs?->cylinders ?? '') . "</cylinders>\n";

                        $engineSize = $vehicle->specs?->displacement ? number_format($vehicle->specs->displacement, 1) . ' L' : '';
                        $xml .= "        <engine_size>" . htmlspecialchars($engineSize) . "</engine_size>\n";
                        $xml .= "        <drive_type>" . htmlspecialchars($vehicle->drivetrainType?->name ?? $vehicle->specs?->drivetrain_standard ?? '') . "</drive_type>\n";
                        $xml .= "        <transmission>" . htmlspecialchars($vehicle->transmissionType?->name ?? $vehicle->specs?->transmission_standard ?? '') . "</transmission>\n";
                        $xml .= "        <vehicle_condition>" . htmlspecialchars($vehicle->vehicle_condition ?? '') . "</vehicle_condition>\n";
                        $xml .= "        <cpo>" . ($vehicle->is_certified ? 'YES' : 'NO') . "</cpo>\n";

                        $descriptionText = strip_tags($vehicle->notes?->dealer_notes ?? $vehicle->notes?->ai_description ?? '');
                        $xml .= "        <description><![CDATA[" . $descriptionText . "]]></description>\n";
                        $xml .= "        <standard_features><![CDATA[" . $standardFeatures . "]]></standard_features>\n";
                        $xml .= "        <optional_features><![CDATA[" . $optionalFeatures . "]]></optional_features>\n";

                        $sellerComments = strip_tags($vehicle->notes?->dealer_notes ?? '');
                        $xml .= "        <seller_comments><![CDATA[" . $sellerComments . "]]></seller_comments>\n";
                        $xml .= "        <listing_time>" . htmlspecialchars($listingTime) . "</listing_time>\n";
                        $xml .= "        <expire_time>" . htmlspecialchars($expireTime) . "</expire_time>\n";
                        $xml .= "    </listing>\n";

                        fwrite($output, $xml);
                    }
                });

            fwrite($output, "</listings>\n");
            fclose($output);
        };

        return response()->stream($callback, 200, $headers);
    }
}
