<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use App\Models\Website\Faq;
use App\Models\Website\FaqCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WebsiteFaqController extends Controller
{
    // ── Index (main page) ────────────────────────────────────────────────────

    public function index(Request $request): View
    {
        $dealerId = $request->user()->current_dealer_id;

        $categories = FaqCategory::forDealer($dealerId)
            ->ordered()
            ->withCount('faqs')
            ->get();

        $faqs = Faq::forDealer($dealerId)
            ->with('category')
            ->ordered()
            ->get();

        return view('dealer.pages.website.faqs.index', compact('faqs', 'categories'));
    }

    // ── FAQ CRUD (JSON) ──────────────────────────────────────────────────────

    public function storeFaq(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'question'        => ['required', 'string', 'max:1000'],
            'answer'          => ['nullable', 'string'],
            'faq_category_id' => ['nullable', 'integer', 'exists:faq_categories,id'],
            'status'          => ['nullable', 'in:Published,Draft'],
        ]);

        $dealerId = $request->user()->current_dealer_id;

        $faq = Faq::create([
            'dealer_id'       => $dealerId,
            'faq_category_id' => $validated['faq_category_id'] ?? null,
            'question'        => $validated['question'],
            'answer'          => $validated['answer'] ?? null,
            'author'          => $request->user()->name,
            'status'          => $validated['status'] ?? 'Published',
            'sort_order'      => Faq::forDealer($dealerId)->max('sort_order') + 1,
        ]);

        $faq->load('category');

        return response()->json($faq, 201);
    }

    public function updateFaq(Request $request, Faq $faq): JsonResponse
    {
        $dealerId = $request->user()->current_dealer_id;
        if ($faq->dealer_id !== $dealerId) {
            abort(403);
        }

        $validated = $request->validate([
            'question'        => ['required', 'string', 'max:1000'],
            'answer'          => ['nullable', 'string'],
            'faq_category_id' => ['nullable', 'integer', 'exists:faq_categories,id'],
            'status'          => ['nullable', 'in:Published,Draft'],
        ]);

        $faq->update($validated);
        $faq->load('category');

        return response()->json($faq);
    }

    public function destroyFaq(Request $request, Faq $faq): JsonResponse
    {
        $dealerId = $request->user()->current_dealer_id;
        if ($faq->dealer_id !== $dealerId) {
            abort(403);
        }

        $faq->delete();

        return response()->json(['message' => 'FAQ deleted.']);
    }

    // ── Category CRUD (JSON) ─────────────────────────────────────────────────

    public function storeCategory(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $dealerId = $request->user()->current_dealer_id;

        $exists = FaqCategory::forDealer($dealerId)
            ->where('name', $validated['name'])
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Category already exists.'], 422);
        }

        $category = FaqCategory::create([
            'dealer_id'  => $dealerId,
            'name'       => $validated['name'],
            'sort_order' => FaqCategory::forDealer($dealerId)->max('sort_order') + 1,
        ]);

        $category->loadCount('faqs');

        return response()->json($category, 201);
    }

    public function updateCategory(Request $request, FaqCategory $faqCategory): JsonResponse
    {
        $dealerId = $request->user()->current_dealer_id;
        if ($faqCategory->dealer_id !== $dealerId) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $exists = FaqCategory::forDealer($dealerId)
            ->where('name', $validated['name'])
            ->where('id', '!=', $faqCategory->id)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Category already exists.'], 422);
        }

        $faqCategory->update($validated);
        $faqCategory->loadCount('faqs');

        return response()->json($faqCategory);
    }

    public function destroyCategory(Request $request, FaqCategory $faqCategory): JsonResponse
    {
        $dealerId = $request->user()->current_dealer_id;
        if ($faqCategory->dealer_id !== $dealerId) {
            abort(403);
        }

        // Move orphaned FAQs to uncategorized (null)
        Faq::forDealer($dealerId)
            ->where('faq_category_id', $faqCategory->id)
            ->update(['faq_category_id' => null]);

        $faqCategory->delete();

        return response()->json(['message' => 'Category deleted.']);
    }

    // ── Bulk Update ──────────────────────────────────────────────────────────

    public function bulkUpdate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'faqs' => ['required', 'array'],
            'faqs.*.id' => ['nullable', 'integer'],
            'faqs.*.question' => ['required', 'string', 'max:1000'],
            'faqs.*.answer' => ['required', 'string'],
            'faqs.*.faq_category_id' => ['nullable', 'integer', 'exists:faq_categories,id'],
            'faqs.*.is_deleted' => ['nullable', 'boolean'],
        ]);

        $dealerId = $request->user()->current_dealer_id;
        $updatedIds = [];

        foreach ($validated['faqs'] as $index => $faqData) {
            if (!empty($faqData['is_deleted']) && !empty($faqData['id'])) {
                Faq::forDealer($dealerId)->where('id', $faqData['id'])->delete();
                continue;
            }

            $data = [
                'dealer_id' => $dealerId,
                'question' => $faqData['question'],
                'answer' => $faqData['answer'],
                'faq_category_id' => $faqData['faq_category_id'],
                'sort_order' => $index,
            ];

            if (!empty($faqData['id'])) {
                $faq = Faq::forDealer($dealerId)->find($faqData['id']);
                if ($faq) {
                    $faq->update($data);
                    $updatedIds[] = $faq->id;
                }
            } else {
                $faq = Faq::create(array_merge($data, ['status' => 'Published', 'author' => $request->user()->name]));
                $updatedIds[] = $faq->id;
            }
        }

        $allFaqs = Faq::forDealer($dealerId)->with('category')->ordered()->get();
        return response()->json($allFaqs);
    }
}
