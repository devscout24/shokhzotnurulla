<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use App\Models\Website\StaticPageContent;
use App\Models\Website\StaticPageCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class WebsiteStaticPageContentController extends Controller
{
    public function index(): View
    {
        $categories = StaticPageCategory::orderBy('sort_order')->withCount('contents')->get();
        $contents = StaticPageContent::with('category')->orderBy('sort_order')->get();

        return view('dealer.pages.website.static-page-content.index', compact('contents', 'categories'));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nickname'                => 'nullable|string|max:255',
            'slug'                    => 'required|string|max:255',
            'static_page_category_id' => 'nullable|integer|exists:static_page_categories,id',
            'h1_override'             => 'nullable|string|max:255',
            'meta_title'              => 'nullable|string|max:255',
            'meta_description'        => 'nullable|string',
            'placement'               => 'required|string|max:50',
            'content'                 => 'nullable|string',
            'status'                  => 'required|in:Published,Draft',
        ]);

        $validated['author'] = Auth::user()->name;
        $validated['sort_order'] = StaticPageContent::max('sort_order') + 1;

        $content = StaticPageContent::create($validated);
        $content->load('category');

        return response()->json($content);
    }

    public function update(Request $request, StaticPageContent $staticPageContent): JsonResponse
    {
        $validated = $request->validate([
            'nickname'                => 'nullable|string|max:255',
            'slug'                    => 'required|string|max:255',
            'static_page_category_id' => 'nullable|integer|exists:static_page_categories,id',
            'h1_override'             => 'nullable|string|max:255',
            'meta_title'              => 'nullable|string|max:255',
            'meta_description'        => 'nullable|string',
            'placement'               => 'required|string|max:50',
            'content'                 => 'nullable|string',
            'status'                  => 'required|in:Published,Draft',
        ]);

        $staticPageContent->update($validated);
        $staticPageContent->load('category');

        return response()->json($staticPageContent);
    }

    public function destroy(StaticPageContent $staticPageContent): JsonResponse
    {
        $staticPageContent->delete();
        return response()->json(['success' => true]);
    }

    // ── Category CRUD ────────────────────────────────────────────────────────

    public function storeCategory(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category = StaticPageCategory::create([
            'name'       => $validated['name'],
            'sort_order' => StaticPageCategory::max('sort_order') + 1,
        ]);

        $category->loadCount('contents');
        return response()->json($category);
    }

    public function updateCategory(Request $request, StaticPageCategory $staticPageCategory): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $staticPageCategory->update($validated);
        $staticPageCategory->loadCount('contents');
        return response()->json($staticPageCategory);
    }

    public function destroyCategory(StaticPageCategory $staticPageCategory): JsonResponse
    {
        // Move contents to uncategorized
        StaticPageContent::where('static_page_category_id', $staticPageCategory->id)
            ->update(['static_page_category_id' => null]);

        $staticPageCategory->delete();
        return response()->json(['success' => true]);
    }

    // ── Bulk Update ──────────────────────────────────────────────────────────

    public function bulkUpdate(Request $request): JsonResponse
    {
        $data   = $request->input('contents', []);
        $author = Auth::user()->name;

        DB::transaction(function () use ($data, $author) {
            foreach ($data as $index => $item) {
                $id = $item['id'] ?? null;

                if (! empty($item['is_deleted']) && $id) {
                    StaticPageContent::where('id', $id)->delete();
                    continue;
                }

                $payload = [
                    'nickname'                => $item['nickname'] ?? null,
                    'slug'                    => $item['slug'],
                    'static_page_category_id' => $item['static_page_category_id'] ?? null,
                    'h1_override'             => $item['h1_override'] ?? null,
                    'meta_title'              => $item['meta_title'] ?? null,
                    'meta_description'        => $item['meta_description'] ?? null,
                    'placement'               => $item['placement'] ?? 'top',
                    'content'                 => $item['content'] ?? null,
                    'status'                  => $item['status'] ?? 'Published',
                    'sort_order'              => $index,
                ];

                if ($id) {
                    StaticPageContent::where('id', $id)->update($payload);
                } else {
                    $payload['author'] = $author;
                    StaticPageContent::create($payload);
                }
            }
        });

        $all = StaticPageContent::with('category')->orderBy('sort_order')->get();
        return response()->json($all);
    }
}
