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
            'title'             => 'required|string|max:255',
            'promo_category_id' => 'nullable|integer|exists:promo_categories,id',
            'status'            => 'required|string',
            'start_date'        => 'nullable|date',
            'end_date'          => 'nullable|date',
            'link_url'          => 'nullable|string|max:255',
            'content'           => 'nullable|string',
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
            'title'             => 'required|string|max:255',
            'promo_category_id' => 'nullable|integer|exists:promo_categories,id',
            'status'            => 'required|string',
            'start_date'        => 'nullable|date',
            'end_date'          => 'nullable|date',
            'link_url'          => 'nullable|string|max:255',
            'content'           => 'nullable|string',
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
                    'title'             => $item['title'] ?? '',
                    'promo_category_id' => $item['promo_category_id'] ?? null,
                    'status'            => $item['status'] ?? 'Active',
                    'start_date'        => $item['start_date'] ?? null,
                    'end_date'          => $item['end_date'] ?? null,
                    'link_url'          => $item['link_url'] ?? null,
                    'content'           => $item['content'] ?? null,
                    'sort_order'        => $index,
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
