<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use App\Models\Website\PromoBanner;
use App\Models\Website\PromoCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class WebsitePromoBannerController extends Controller
{
    public function index(): View
    {
        $categories = PromoCategory::orderBy('sort_order')->withCount('banners')->get();
        $banners = PromoBanner::with('category')->orderBy('sort_order')->get();

        return view('dealer.pages.website.promo-banner.index', compact('banners', 'categories'));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title'                  => 'required|string|max:255',
            'disclaimer'             => 'nullable|string',
            'promo_category_id'      => 'nullable|integer|exists:promo_categories,id',
            'condition'              => 'nullable|string|in:New,Pre-owned',
            'certified'              => 'nullable|string|in:CPO,VPO',
            'status'                 => 'required|string',
            'start_date'             => 'nullable|date',
            'end_date'               => 'nullable|date',
            'link_url'               => 'nullable|string|max:255',
            'desktop_image_url'      => 'nullable|string|max:255',
            'mobile_image_url'       => 'nullable|string|max:255',
            'srp_desktop_banner_url' => 'nullable|string|max:255',
            'srp_mobile_banner_url'  => 'nullable|string|max:255',
            'primary_color'          => 'nullable|string|max:20',
            'secondary_color'        => 'nullable|string|max:20',
            'content'                => 'nullable|string',
        ]);

        $validated['author'] = Auth::user()->name;
        $validated['sort_order'] = PromoBanner::max('sort_order') + 1;

        $banner = PromoBanner::create($validated);
        $banner->load('category');

        return response()->json($banner);
    }

    public function update(Request $request, PromoBanner $promoBanner): JsonResponse
    {
        $validated = $request->validate([
            'title'                  => 'required|string|max:255',
            'disclaimer'             => 'nullable|string',
            'promo_category_id'      => 'nullable|integer|exists:promo_categories,id',
            'condition'              => 'nullable|string|in:New,Pre-owned',
            'certified'              => 'nullable|string|in:CPO,VPO',
            'status'                 => 'required|string',
            'start_date'             => 'nullable|date',
            'end_date'               => 'nullable|date',
            'link_url'               => 'nullable|string|max:255',
            'desktop_image_url'      => 'nullable|string|max:255',
            'mobile_image_url'       => 'nullable|string|max:255',
            'srp_desktop_banner_url' => 'nullable|string|max:255',
            'srp_mobile_banner_url'  => 'nullable|string|max:255',
            'primary_color'          => 'nullable|string|max:20',
            'secondary_color'        => 'nullable|string|max:20',
            'content'                => 'nullable|string',
        ]);

        $promoBanner->update($validated);
        $promoBanner->load('category');

        return response()->json($promoBanner);
    }

    public function destroy(PromoBanner $promoBanner): JsonResponse
    {
        $promoBanner->delete();
        return response()->json(['success' => true]);
    }

    // ── CSV Template & Upload ───────────────────────────────────────────────

    public function downloadTemplate()
    {
        $headers = [
            'Promo Title / Image Alt Text',
            'Disclaimer',
            'Category',
            'Start Date',
            'End Date',
            'Link URL',
            'Desktop Image (Carousel / List)',
            'Mobile Version (Carousel / List)',
            'Condition',
            'Certified',
            'SRP: Top Banner (Desktop)',
            'SRP: Top Banner (Mobile)',
            'SRP: Primary background color',
            'SRP: Secondary background color'
        ];

        $callback = function() use ($headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            fclose($file);
        };

        return response()->stream($callback, 200, [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=promo_banner_template.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ]);
    }

    public function uploadCsv(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt'
        ]);

        $file = $request->file('file');
        $handle = fopen($file->getPathname(), 'r');
        $header = fgetcsv($handle);

        $data = [];
        while (($row = fgetcsv($handle)) !== false) {
            $item = array_combine($header, $row);
            
            // Map the friendly names back to field keys
            $mapped = [
                'title'                  => $item['Promo Title / Image Alt Text'] ?? '',
                'disclaimer'             => $item['Disclaimer'] ?? '',
                'category_name'          => $item['Category'] ?? '',
                'start_date'             => $item['Start Date'] ?? '',
                'end_date'               => $item['End Date'] ?? '',
                'link_url'               => $item['Link URL'] ?? '',
                'desktop_image_url'      => $item['Desktop Image (Carousel / List)'] ?? '',
                'mobile_image_url'       => $item['Mobile Version (Carousel / List)'] ?? '',
                'condition'              => $item['Condition'] ?? '',
                'certified'              => $item['Certified'] ?? '',
                'srp_desktop_banner_url' => $item['SRP: Top Banner (Desktop)'] ?? '',
                'srp_mobile_banner_url'  => $item['SRP: Top Banner (Mobile)'] ?? '',
                'primary_color'          => $item['SRP: Primary background color'] ?? '',
                'secondary_color'        => $item['SRP: Secondary background color'] ?? '',
            ];
            $data[] = $mapped;
        }
        fclose($handle);

        return response()->json($data);
    }

    // ── Category CRUD ────────────────────────────────────────────────────────

    public function storeCategory(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category = PromoCategory::create([
            'name'       => $validated['name'],
            'sort_order' => PromoCategory::max('sort_order') + 1,
        ]);

        $category->loadCount('banners');
        return response()->json($category);
    }

    public function updateCategory(Request $request, PromoCategory $promoCategory): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $promoCategory->update($validated);
        $promoCategory->loadCount('banners');
        return response()->json($promoCategory);
    }

    public function destroyCategory(PromoCategory $promoCategory): JsonResponse
    {
        PromoBanner::where('promo_category_id', $promoCategory->id)
            ->update(['promo_category_id' => null]);

        $promoCategory->delete();
        return response()->json(['success' => true]);
    }

    // ── Bulk Update ──────────────────────────────────────────────────────────

    public function bulkUpdate(Request $request): JsonResponse
    {
        $data   = $request->input('banners', []);
        $author = Auth::user()->name;

        DB::transaction(function () use ($data, $author) {
            foreach ($data as $index => $item) {
                $id = $item['id'] ?? null;

                if (! empty($item['is_deleted']) && $id) {
                    PromoBanner::where('id', $id)->delete();
                    continue;
                }

                $payload = [
                    'title'                  => $item['title'] ?? '',
                    'disclaimer'             => $item['disclaimer'] ?? null,
                    'promo_category_id'      => $item['promo_category_id'] ?? null,
                    'condition'              => $item['condition'] ?? null,
                    'certified'              => $item['certified'] ?? null,
                    'status'                 => $item['status'] ?? 'Active',
                    'start_date'             => $item['start_date'] ?? null,
                    'end_date'               => $item['end_date'] ?? null,
                    'link_url'               => $item['link_url'] ?? null,
                    'desktop_image_url'      => $item['desktop_image_url'] ?? null,
                    'mobile_image_url'       => $item['mobile_image_url'] ?? null,
                    'srp_desktop_banner_url' => $item['srp_desktop_banner_url'] ?? null,
                    'srp_mobile_banner_url'  => $item['srp_mobile_banner_url'] ?? null,
                    'primary_color'          => $item['primary_color'] ?? null,
                    'secondary_color'        => $item['secondary_color'] ?? null,
                    'content'                => $item['content'] ?? null,
                    'sort_order'             => $index,
                ];

                if ($id) {
                    PromoBanner::where('id', $id)->update($payload);
                } else {
                    $payload['author'] = $author;
                    PromoBanner::create($payload);
                }
            }
        });

        $all = PromoBanner::with('category')->orderBy('sort_order')->get();
        return response()->json($all);
    }
}
