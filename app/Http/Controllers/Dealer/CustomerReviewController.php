<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use App\Models\Website\CustomerReview;
use App\Models\Website\CustomerReviewCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CustomerReviewController extends Controller
{
    public function index(): View
    {
        $categories = CustomerReviewCategory::orderBy('sort_order')->withCount('reviews')->get();
        $reviews = CustomerReview::with('category')->orderBy('sort_order', 'desc')->get();

        return view('dealer.pages.website.customer-reviews.index', compact('reviews', 'categories'));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'reviewer_name'               => 'required|string|max:255',
            'review_headline'             => 'nullable|string|max:255',
            'review_date'                 => 'nullable|date',
            'review_source'               => 'nullable|string|max:255',
            'star_count'                  => 'nullable|integer|min:1|max:5',
            'customer_review_category_id' => 'required|integer|exists:customer_review_categories,id',
            'photo_url'                   => 'nullable|string|max:255',
            'content'                     => 'required|string',
            'status'                      => 'required|string',
        ]);

        $validated['author'] = Auth::user()->name;
        $validated['sort_order'] = CustomerReview::max('sort_order') + 1;

        $review = CustomerReview::create($validated);
        $review->load('category');

        return response()->json($review);
    }

    public function update(Request $request, CustomerReview $customerReview): JsonResponse
    {
        $validated = $request->validate([
            'reviewer_name'               => 'required|string|max:255',
            'review_headline'             => 'nullable|string|max:255',
            'review_date'                 => 'nullable|date',
            'review_source'               => 'nullable|string|max:255',
            'star_count'                  => 'nullable|integer|min:1|max:5',
            'customer_review_category_id' => 'required|integer|exists:customer_review_categories,id',
            'photo_url'                   => 'nullable|string|max:255',
            'content'                     => 'required|string',
            'status'                      => 'required|string',
        ]);

        $customerReview->update($validated);
        $customerReview->load('category');

        return response()->json($customerReview);
    }

    public function destroy(CustomerReview $customerReview): JsonResponse
    {
        $customerReview->delete();
        return response()->json(['success' => true]);
    }

    public function bulkUpdate(Request $request): JsonResponse
    {
        $data = $request->input('reviews', []);
        $author = Auth::user()->name;

        foreach ($data as $index => $item) {
            $id = $item['id'] ?? null;

            if (!empty($item['is_deleted']) && $id) {
                CustomerReview::where('id', $id)->delete();
                continue;
            }

            $payload = [
                'reviewer_name'               => $item['reviewer_name'] ?? '',
                'review_headline'             => $item['review_headline'] ?? null,
                'review_date'                 => $item['review_date'] ?? null,
                'review_source'               => $item['review_source'] ?? null,
                'star_count'                  => $item['star_count'] ?? 5,
                'customer_review_category_id' => $item['customer_review_category_id'] ?? null,
                'photo_url'                   => $item['photo_url'] ?? null,
                'content'                     => $item['content'] ?? '',
                'status'                      => $item['status'] ?? 'Active',
                'sort_order'                  => $index,
            ];

            if ($id) {
                CustomerReview::where('id', $id)->update($payload);
            } else {
                $payload['author'] = $author;
                CustomerReview::create($payload);
            }
        }

        $all = CustomerReview::with('category')->orderBy('sort_order')->get();
        return response()->json($all);
    }

    public function storeCategory(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category = CustomerReviewCategory::create([
            'name'       => $validated['name'],
            'sort_order' => CustomerReviewCategory::max('sort_order') + 1,
        ]);

        $category->loadCount('reviews');
        return response()->json($category);
    }

    public function updateCategory(Request $request, CustomerReviewCategory $customerReviewCategory): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $customerReviewCategory->update($validated);
        $customerReviewCategory->loadCount('reviews');
        return response()->json($customerReviewCategory);
    }

    public function destroyCategory(CustomerReviewCategory $customerReviewCategory): JsonResponse
    {
        CustomerReview::where('customer_review_category_id', $customerReviewCategory->id)
            ->update(['customer_review_category_id' => null]);

        $customerReviewCategory->delete();
        return response()->json(['success' => true]);
    }
}
