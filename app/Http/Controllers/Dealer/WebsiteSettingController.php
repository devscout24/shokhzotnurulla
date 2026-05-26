<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use App\Helpers\TimeHelper;
use App\Models\Website\Location;
use App\Models\Website\DigitalRetailSetting;
use App\Models\Website\Redirect;
use App\Models\Dealership\DealerIp;
use App\Actions\Website\CreateLocationAction;
use App\Actions\Website\UpdateLocationAction;
use App\Actions\Website\DeleteLocationAction;
use App\Actions\Website\ReorderLocationsAction;
use App\Actions\Website\UploadMediaAction;
use App\Actions\Website\DeleteMediaAction;
use App\Actions\Website\CreateRedirectAction;
use App\Actions\Website\UpdateRedirectAction;
use App\Actions\Website\DeleteRedirectAction;
use App\Actions\Website\ImportRedirectsAction;
use App\Actions\Website\CreateDealerIpAction;
use App\Actions\Website\UpdateDealerIpAction;
use App\Actions\Website\DeleteDealerIpAction;
use App\Models\Website\HomeAboutCtaSection;
use App\Http\Requests\Website\UpdateGeneralSettingsRequest;
use App\Http\Requests\Website\UpdateDisclaimersRequest;
use App\Http\Requests\Website\UpdateSocialLinksRequest;
use App\Http\Requests\Website\StoreLocationRequest;
use App\Http\Requests\Website\UpdateLocationRequest;
use App\Http\Requests\Website\UpdateBannerSettingsRequest;
use App\Http\Requests\Website\UpdateDigitalRetailRequest;
use App\Http\Requests\Website\UpdateVideoRequest;
use App\Http\Requests\Website\StoreRedirectRequest;
use App\Http\Requests\Website\UpdateRedirectRequest;
use App\Http\Requests\Website\StoreDealerIpRequest;
use App\Http\Requests\Website\UpdateDealerIpRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use App\Support\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\View\View;



class WebsiteSettingController extends Controller
{
    public function __construct(private readonly AuditLogger $auditLogger) {}

    public function general(Request $request): View
    {
        $dealer = $request->user()->currentDealer;
        return view('dealer.pages.website.settings.general', compact('dealer'));
    }

    public function updateGeneral(UpdateGeneralSettingsRequest $request): JsonResponse
    {
        $dealer = $request->user()->currentDealer;
        $validated = $request->validated();

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = time() . '_' . $file->getClientOriginalName();
            
            // Ensure directory exists
            if (!file_exists(public_path('assets/frontend/img/logos'))) {
                mkdir(public_path('assets/frontend/img/logos'), 0755, true);
            }

            // Delete old logo if exists
            if ($dealer->logo && file_exists(public_path('assets/frontend/img/logos/' . $dealer->logo))) {
                @unlink(public_path('assets/frontend/img/logos/' . $dealer->logo));
            }

            $file->move(public_path('assets/frontend/img/logos'), $filename);
            $validated['logo'] = $filename;
            $path = $filename;
        }

        $dealer->update($validated);
        \Illuminate\Support\Facades\Cache::forget("dealer_{$dealer->id}_frontend_settings");
        $this->auditLogger->info($request, 'General settings updated');
        
        $responseData = ['success' => true, 'message' => 'General settings saved.'];
        if (isset($path)) {
            $responseData['logo_url'] = asset('assets/frontend/img/logos/' . $path);
        }
        
        return response()->json($responseData);
    }

    public function updateDisclaimers(UpdateDisclaimersRequest $request): JsonResponse
    {
        $dealer = $request->user()->currentDealer;
        $validated = $request->validated();
        $dealer->update($validated);
        \Illuminate\Support\Facades\Cache::forget("dealer_{$dealer->id}_frontend_settings");
        $this->auditLogger->info($request, 'Disclaimer settings updated');
        return response()->json(['success' => true, 'message' => 'Disclaimers saved.']);
    }

    public function updateSocial(UpdateSocialLinksRequest $request): JsonResponse
    {
        $dealer = $request->user()->currentDealer;
        $validated = $request->validated();
        $dealer->social_links = $validated;
        $dealer->save();
        \Illuminate\Support\Facades\Cache::forget("dealer_{$dealer->id}_frontend_settings");
        $this->auditLogger->info($request, 'Social links updated');
        return response()->json(['success' => true, 'message' => 'Social links saved.']);
    }

    public function locations(Request $request): View
    {
        $dealer = $request->user()->currentDealer;
        $locations = $dealer->locations()->with(['phones', 'emails', 'hours', 'specialHours'])->get();
        return view('dealer.pages.website.settings.locations', compact('locations'));
    }

    public function storeLocation(StoreLocationRequest $request, CreateLocationAction $createLocation): JsonResponse
    {
        $dealer = $request->user()->currentDealer;
        $location = $createLocation($dealer, $request->validated());
        $this->auditLogger->info($request, 'Location created', ['location_id' => $location->id]);
        return response()->json(['success' => true, 'message' => 'Location created successfully.', 'location' => $location]);
    }

    public function editLocation(Location $location): JsonResponse
    {
        $dealer = request()->user()->currentDealer;
        abort_if($location->dealer_id !== $dealer->id, 403);

        $location->load(['phones', 'emails', 'hours', 'specialHours']);

        $data = $location->toArray();


        // Format phones as flat array
        foreach ($location->phones as $phone) {
            $data['phone_' . $phone->type] = $phone->number;
        }

        // Format emails as flat array
        foreach ($location->emails as $email) {
            $data['email_' . $email->type] = $email->email;
        }

        // Format regular hours
        $hours = [];
        foreach ($location->hours as $hour) {
            $hours[$hour->department][$hour->day_of_week - 1] = [
                'open' => TimeHelper::fromDatabase($hour->open_time),
                'close' => TimeHelper::fromDatabase($hour->close_time),
                'is_closed' => $hour->is_closed,
                'appointment_only' => $hour->appointment_only,
            ];
        }
        $data['hours'] = $hours;

        // Format special hours (no time conversion needed, as we don't use open/close for special hours in this modal)
        $data['special_hours'] = $location->specialHours->map(function ($sh) {
            return [
                'department' => $sh->department,
                'date' => $sh->date->format('Y-m-d'),
                'is_closed' => $sh->is_closed,
                'appointment_only' => $sh->appointment_only,
            ];
        })->toArray();

        return response()->json($data);
    }

    public function updateLocation(UpdateLocationRequest $request, Location $location, UpdateLocationAction $updateLocation): JsonResponse
    {
        $dealer = request()->user()->currentDealer;
        abort_if($location->dealer_id !== $dealer->id, 403);

        $location = $updateLocation($location, $request->validated());
        \Illuminate\Support\Facades\Cache::forget("dealer_{$dealer->id}_frontend_settings");
        $this->auditLogger->info($request, 'Location updated', ['location_id' => $location->id]);
        return response()->json(['success' => true, 'message' => 'Location updated successfully.', 'location' => $location]);
    }

    public function video(Request $request): View
    {
      
        $dealer = $request->user()->currentDealer;
        return view('dealer.pages.website.settings.video', compact('dealer'));
    }

    public function updateVideo(UpdateVideoRequest $request, UploadMediaAction $uploadMedia): RedirectResponse
    {
        
        $dealer = $request->user()->currentDealer;

        if ($request->hasFile('video_file')) {
            $uploaded = $uploadMedia->execute($dealer->id, [$request->file('video_file')]);
            $media = $uploaded[0];
            
            $dealer->update([
                'video_source' => 'overfuel',
            ]);

            \Illuminate\Support\Facades\Cache::forget("dealer_{$dealer->id}_frontend_settings");
            $this->auditLogger->info($request, 'Website background video updated');
        }

        return redirect()->back()->with('success', 'Video updated successfully.');
    }


    public function reorderLocations(Request $request, ReorderLocationsAction $reorderLocations): JsonResponse
    {
        $dealer = $request->user()->currentDealer;
        $order = $request->validate(['order' => 'required|array', 'order.*' => 'integer|exists:locations,id']);
        $reorderLocations($dealer, $order['order']);
        $this->auditLogger->info($request, 'Locations reordered');
        return response()->json(['success' => true, 'message' => 'Locations reordered.']);
    }

    public function banners(Request $request): View
    {
        $dealer = $request->user()->currentDealer;
        $dealer->load(['bannerDesktopMedia', 'bannerMobileMedia']);
        return view('dealer.pages.website.settings.banners', compact('dealer'));
    }

    public function homeAboutCta(Request $request): View
    {
        $dealer = $request->user()->currentDealer;
        $section = HomeAboutCtaSection::where('dealer_id', $dealer->id)->first();
        $content = array_replace_recursive(
            HomeAboutCtaSection::defaultContent(),
            $section?->content ?? []
        );

        return view('dealer.pages.website.settings.home-about-cta', compact('content'));
    }

    public function updateHomeAboutCta(Request $request): JsonResponse
    {
        $dealer = $request->user()->currentDealer;

        $validated = $request->validate([
            'about_eyebrow' => 'nullable|string|max:120',
            'about_heading' => 'nullable|string|max:255',
            'about_paragraph_1' => 'nullable|string',
            'about_paragraph_2' => 'nullable|string',
            'about_image_alt' => 'nullable|string|max:255',
            'about_image_file' => 'nullable|image|max:4096',

            'stats_1_icon' => 'nullable|string|max:120',
            'stats_1_title' => 'nullable|string|max:255',
            'stats_1_text' => 'nullable|string',
            'stats_2_icon' => 'nullable|string|max:120',
            'stats_2_title' => 'nullable|string|max:255',
            'stats_2_text' => 'nullable|string',
            'stats_3_icon' => 'nullable|string|max:120',
            'stats_3_title' => 'nullable|string|max:255',
            'stats_3_text' => 'nullable|string',
            'stats_4_icon' => 'nullable|string|max:120',
            'stats_4_title' => 'nullable|string|max:255',
            'stats_4_text' => 'nullable|string',

            'card_icon' => 'nullable|string|max:120',
            'card_title' => 'nullable|string|max:255',
            'card_text' => 'nullable|string',
            'card_image_alt' => 'nullable|string|max:255',
            'card_image_file' => 'nullable|image|max:4096',

            'cta_1_title' => 'nullable|string|max:255',
            'cta_1_text' => 'nullable|string',
            'cta_1_link' => 'nullable|string|max:255',
            'cta_2_title' => 'nullable|string|max:255',
            'cta_2_text' => 'nullable|string',
            'cta_2_link' => 'nullable|string|max:255',
        ]);

        $section = HomeAboutCtaSection::where('dealer_id', $dealer->id)->first();
        $existing = $section?->content ?? HomeAboutCtaSection::defaultContent();

        $aboutImagePath = data_get($existing, 'about.image_url', '');
        if ($request->hasFile('about_image_file')) {
            $file = $request->file('about_image_file');
            $filename = time() . '_about.' . $file->getClientOriginalExtension();
            $dir = public_path('assets/frontend/img/home-about-cta');
            if (!file_exists($dir)) {
                mkdir($dir, 0755, true);
            }
            $file->move($dir, $filename);
            $aboutImagePath = 'assets/frontend/img/home-about-cta/' . $filename;
        }

        $cardImagePath = data_get($existing, 'card.image_url', '');
        if ($request->hasFile('card_image_file')) {
            $file = $request->file('card_image_file');
            $filename = time() . '_card.' . $file->getClientOriginalExtension();
            $dir = public_path('assets/frontend/img/home-about-cta');
            if (!file_exists($dir)) {
                mkdir($dir, 0755, true);
            }
            $file->move($dir, $filename);
            $cardImagePath = 'assets/frontend/img/home-about-cta/' . $filename;
        }

        $stats = [];
        for ($i = 1; $i <= 4; $i++) {
            $stats[] = [
                'icon' => $validated["stats_{$i}_icon"] ?? '',
                'title' => $validated["stats_{$i}_title"] ?? '',
                'text' => $validated["stats_{$i}_text"] ?? '',
            ];
        }

        $ctas = [];
        for ($i = 1; $i <= 2; $i++) {
            $ctas[] = [
                'title' => $validated["cta_{$i}_title"] ?? '',
                'text' => $validated["cta_{$i}_text"] ?? '',
                'link_url' => $validated["cta_{$i}_link"] ?? '',
            ];
        }

        $content = [
            'about' => [
                'eyebrow' => $validated['about_eyebrow'] ?? '',
                'heading' => $validated['about_heading'] ?? '',
                'paragraphs' => [
                    $validated['about_paragraph_1'] ?? '',
                    $validated['about_paragraph_2'] ?? '',
                ],
                'image_url' => $aboutImagePath,
                'image_alt' => $validated['about_image_alt'] ?? '',
            ],
            'stats' => $stats,
            'card' => [
                'icon' => $validated['card_icon'] ?? '',
                'title' => $validated['card_title'] ?? '',
                'text' => $validated['card_text'] ?? '',
                'image_url' => $cardImagePath,
                'image_alt' => $validated['card_image_alt'] ?? '',
            ],
            'ctas' => $ctas,
        ];

        if ($section) {
            $section->update(['content' => $content]);
        } else {
            HomeAboutCtaSection::create([
                'dealer_id' => $dealer->id,
                'content' => $content,
            ]);
        }

        \Illuminate\Support\Facades\Cache::forget("dealer_{$dealer->id}_frontend_settings");
        $this->auditLogger->info($request, 'Home about/CTA settings updated');

        return response()->json(['success' => true, 'message' => 'Home About/CTA settings saved.']);
    }

    public function updateBanners(UpdateBannerSettingsRequest $request, UploadMediaAction $uploadMedia, DeleteMediaAction $deleteMedia): JsonResponse
    {
        $dealer = $request->user()->currentDealer;
        $data = $request->validated();

        //banner_title and banner_subtitle
        $data['banner_title'] = $data['banner_title'] ?? null;
        $data['banner_subtitle'] = $data['banner_subtitle'] ?? null;
        $data['logo'] = $data['logo'] ?? null;

        // Handle desktop image upload (if a new file was uploaded)
        if ($request->hasFile('banner_desktop_image')) {
            $uploaded = $uploadMedia->execute($dealer->id, [$request->file('banner_desktop_image')]);
            $media = $uploaded[0];
            // Delete old media if exists
            if ($dealer->banner_desktop_media_id) {
                $deleteMedia->execute($dealer->bannerDesktopMedia);
            }
            $data['banner_desktop_media_id'] = $media->id;
        }

        // Handle mobile image upload
        if ($request->hasFile('banner_mobile_image')) {
            $uploaded = $uploadMedia->execute($dealer->id, [$request->file('banner_mobile_image')]);
            $media = $uploaded[0];
            if ($dealer->banner_mobile_media_id) {
                $deleteMedia->execute($dealer->bannerMobileMedia);
            }
            $data['banner_mobile_media_id'] = $media->id;
        }

        // Remove file fields to avoid mass-assignment errors
        unset($data['banner_desktop_image'], $data['banner_mobile_image']);

        $dealer->update($data);
        \Illuminate\Support\Facades\Cache::forget("dealer_{$dealer->id}_frontend_settings");

        $this->auditLogger->info($request, 'Banner settings updated');

        return response()->json(['success' => true, 'message' => 'Banner settings saved.']);
    }

    public function finance(Request $request): View
    {
        return view('dealer.pages.website.settings.finance');
    }

    public function retail(Request $request): View
    {
        $dealer = $request->user()->currentDealer;
        $settings = $dealer->digitalRetailSettings ?? new DigitalRetailSetting();
        return view('dealer.pages.website.settings.retail', compact('settings'));
    }

    public function updateDigitalRetail(UpdateDigitalRetailRequest $request): JsonResponse
    {
        $dealer = $request->user()->currentDealer;
        $data = $request->validated();

        $settings = $dealer->digitalRetailSettings;
        if (!$settings) {
            $settings = new DigitalRetailSetting();
            $settings->dealer_id = $dealer->id;
        }

        // Map request fields to database columns
        $settings->shipping_free_miles = $data['free_shipping_miles'];
        $settings->shipping_discount_dollars = $data['shipping_discount'];
        $settings->deposit_minimum = $data['deposit_amount'];
        $settings->deposit_hold_hours = $data['deposit_hold_hours'];
        $settings->digital_retail_hold_hours = $data['retail_hold_hours'];
        $settings->trade_days_valid = $data['trade_offer_days'];

        $settings->save();

        $this->auditLogger->info($request, 'Digital Retail settings updated');

        return response()->json(['success' => true, 'message' => 'Digital Retail settings saved.']);
    }

    public function redirects(Request $request): View
    {
        $dealer = $request->user()->currentDealer;
        $redirects = $dealer->redirects()->latest()->get();
        return view('dealer.pages.website.settings.redirects', compact('redirects'));
    }

    public function storeRedirect(StoreRedirectRequest $request, CreateRedirectAction $createRedirect): JsonResponse
    {
        $dealer = $request->user()->currentDealer;
        $data = $request->validated();

        $redirect = $createRedirect->execute($dealer, $data);

        $this->auditLogger->info($request, 'Redirect created', ['redirect_id' => $redirect->id]);

        session()->flash('success', 'Redirect saved.');
        return response()->json(['success' => true, 'message' => 'Redirect saved.']);
    }

    public function updateRedirect(UpdateRedirectRequest $request, Redirect $redirect, UpdateRedirectAction $updateRedirect): JsonResponse
    {
        $dealer = $request->user()->currentDealer;
        abort_if($redirect->dealer_id !== $dealer->id, 403);

        $data = $request->validated();

        $updateRedirect->execute($redirect, $data);

        $this->auditLogger->info($request, 'Redirect updated', ['redirect_id' => $redirect->id]);

        session()->flash('success', 'Redirect updated.');
        return response()->json(['success' => true, 'message' => 'Redirect updated.']);
    }

    public function destroyRedirect(Request $request, Redirect $redirect, DeleteRedirectAction $deleteRedirect): JsonResponse
    {
        $dealer = $request->user()->currentDealer;
        abort_if($redirect->dealer_id !== $dealer->id, 403);

        $deleteRedirect->execute($redirect);

        $this->auditLogger->info($request, 'Redirect deleted', ['redirect_id' => $redirect->id]);

        return response()->json(['success' => true, 'message' => 'Redirect deleted.']);
    }

    public function importRedirects(Request $request, ImportRedirectsAction $importRedirects): JsonResponse
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $dealer = $request->user()->currentDealer;
        $result = $importRedirects->execute($dealer, $request->file('csv_file'));

        $message = "<strong>{$result['success']} Redirects imported.</strong>";
        if (!empty($result['errors'])) {
            $message .= ' <br>Errors: ' . implode('; ', array_map(function($e) {
                return "<br>Row {$e['row']}: " . implode(', ', $e['errors']);
            }, $result['errors']));
        }

        $this->auditLogger->info($request, 'Redirects imported', ['count' => $result['success'], 'errors' => count($result['errors'])]);

        // Determine flash type based on result
        if ($result['success'] > 0 && empty($result['errors'])) {
            // All successful
            session()->flash('import_success', $message);
        } elseif ($result['success'] > 0 && !empty($result['errors'])) {
            // Partial success
            session()->flash('import_warning', $message);
        } else {
            // All failed
            session()->flash('import_error', $message);
        }

        return response()->json(['success' => true, 'message' => $message]);
    }

    public function downloadSampleCsv()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="redirects_sample.csv"',
        ];

        $columns = ['source_url', 'target_url', 'is_regex', 'status_code', 'is_enabled'];
        $sampleRows = [
            ['/demo-test', '/demo-new-page', 0, 301, 1],
            ['^/demo-old-page-([0-9]+)$', '/demo-new-location/$1', 1, 302, 1],
            ['/demo-specials', '/demo-used-cars', 0, 301, 0],
            ['/demo-contact', '/demo-about-us', 0, 302, 1],
        ];

        $callback = function () use ($columns, $sampleRows) {
            $file = fopen('php://output', 'w');
            // Add BOM for UTF-8 support in Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, $columns);
            foreach ($sampleRows as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function ips(Request $request): View
    {
        $dealer = $request->user()->currentDealer;
        $dealerIps = $dealer->dealerIps()->latest()->get();
        return view('dealer.pages.website.settings.ips', compact('dealerIps'));
    }

    public function storeDealerIp(StoreDealerIpRequest $request, CreateDealerIpAction $createDealerIp): JsonResponse
    {
        $dealer = $request->user()->currentDealer;
        $dealerIp = $createDealerIp->execute($dealer, $request->validated());

        $this->auditLogger->info($request, 'Dealer IP created', ['dealer_ip_id' => $dealerIp->id]);

        session()->flash('success', 'IP address added.');

        return response()->json(['success' => true, 'message' => 'IP address added.']);
    }

    public function updateDealerIp(UpdateDealerIpRequest $request, DealerIp $dealerIp, UpdateDealerIpAction $updateDealerIp): JsonResponse
    {
        $dealer = $request->user()->currentDealer;
        abort_if($dealerIp->dealer_id !== $dealer->id, 403);

        $updateDealerIp->execute($dealerIp, $request->validated());

        $this->auditLogger->info($request, 'Dealer IP updated', ['dealer_ip_id' => $dealerIp->id]);

        session()->flash('success', 'IP address updated.');
        return response()->json(['success' => true, 'message' => 'IP address updated.']);
    }

    public function destroyDealerIp(Request $request, DealerIp $dealerIp, DeleteDealerIpAction $deleteDealerIp): JsonResponse
    {
        $dealer = $request->user()->currentDealer;
        abort_if($dealerIp->dealer_id !== $dealer->id, 403);

        $deleteDealerIp->execute($dealerIp);

        $this->auditLogger->info($request, 'Dealer IP deleted', ['dealer_ip_id' => $dealerIp->id]);

        return response()->json(['success' => true, 'message' => 'IP address deleted.']);
    }
}
