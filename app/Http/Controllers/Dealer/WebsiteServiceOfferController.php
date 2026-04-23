<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use App\Models\Website\ServiceOffer;
use App\Models\Website\ServiceOfferCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class WebsiteServiceOfferController extends Controller
{
    public function index(): View
    {
        $categories = ServiceOfferCategory::orderBy('sort_order')->withCount('serviceOffers')->get();
        $offers = ServiceOffer::with('category')->orderBy('sort_order', 'desc')->get();

        return view('dealer.pages.website.service-offers.index', compact('offers', 'categories'));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title'                     => 'required|string|max:255',
            'subtitle'                  => 'nullable|string|max:255',
            'description'               => 'required|string',
            'service_offer_category_id' => 'required|integer|exists:service_offer_categories,id',
            'photo_url'                 => 'nullable|string|max:255',
            'link_offer_to'             => 'required|string|max:255',
            'link_text'                 => 'required|string|max:255',
            'disclaimer'                => 'required|string',
            'status'                    => 'required|string',
        ]);

        $validated['author'] = Auth::user()->name;
        $validated['sort_order'] = ServiceOffer::max('sort_order') + 1;

        $offer = ServiceOffer::create($validated);
        $offer->load('category');

        return response()->json($offer);
    }

    public function update(Request $request, ServiceOffer $serviceOffer): JsonResponse
    {
        $validated = $request->validate([
            'title'                     => 'required|string|max:255',
            'subtitle'                  => 'nullable|string|max:255',
            'description'               => 'required|string',
            'service_offer_category_id' => 'required|integer|exists:service_offer_categories,id',
            'photo_url'                 => 'nullable|string|max:255',
            'link_offer_to'             => 'required|string|max:255',
            'link_text'                 => 'required|string|max:255',
            'disclaimer'                => 'required|string',
            'status'                    => 'required|string',
        ]);

        $serviceOffer->update($validated);
        $serviceOffer->load('category');

        return response()->json($serviceOffer);
    }

    public function destroy(ServiceOffer $serviceOffer): JsonResponse
    {
        $serviceOffer->delete();
        return response()->json(['success' => true]);
    }

    public function bulkUpdate(Request $request): JsonResponse
    {
        $data = $request->input('offers', []);
        $author = Auth::user()->name;

        foreach ($data as $index => $item) {
            $id = $item['id'] ?? null;

            if (!empty($item['is_deleted']) && $id) {
                ServiceOffer::where('id', $id)->delete();
                continue;
            }

            $payload = [
                'title'                     => $item['title'] ?? '',
                'subtitle'                  => $item['subtitle'] ?? null,
                'description'               => $item['description'] ?? '',
                'service_offer_category_id' => $item['service_offer_category_id'] ?? null,
                'photo_url'                 => $item['photo_url'] ?? null,
                'link_offer_to'             => $item['link_offer_to'] ?? null,
                'link_text'                 => $item['link_text'] ?? null,
                'disclaimer'                => $item['disclaimer'] ?? '',
                'status'                    => $item['status'] ?? 'Published',
                'sort_order'                => $index,
            ];

            if ($id) {
                ServiceOffer::where('id', $id)->update($payload);
            } else {
                $payload['author'] = $author;
                ServiceOffer::create($payload);
            }
        }

        $all = ServiceOffer::with('category')->orderBy('sort_order')->get();
        return response()->json($all);
    }

    public function storeCategory(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category = ServiceOfferCategory::create([
            'name'       => $validated['name'],
            'sort_order' => ServiceOfferCategory::max('sort_order') + 1,
        ]);

        $category->loadCount('serviceOffers');
        return response()->json($category);
    }

    public function updateCategory(Request $request, ServiceOfferCategory $serviceOfferCategory): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $serviceOfferCategory->update($validated);
        $serviceOfferCategory->loadCount('serviceOffers');
        return response()->json($serviceOfferCategory);
    }

    public function destroyCategory(ServiceOfferCategory $serviceOfferCategory): JsonResponse
    {
        ServiceOffer::where('service_offer_category_id', $serviceOfferCategory->id)
            ->update(['service_offer_category_id' => null]);

        $serviceOfferCategory->delete();
        return response()->json(['success' => true]);
    }
}
