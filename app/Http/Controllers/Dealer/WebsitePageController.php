<?php
namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use App\Models\Website\Page;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WebsitePageController extends Controller
{
    // ── List Pages ───────────────────────────────────────────────────────────

    public function index(Request $request): View
    {
        $dealerId = $request->user()->current_dealer_id;

        $pages = Page::forDealer($dealerId)
            ->orderByPublished()
            ->paginate(20);

        $routes = [
            'create'  => route('dealer.website.pages.create'),
            'store'   => route('dealer.website.pages.store'),
            'edit'    => route('dealer.website.pages.edit', ['page' => '__ID__']),
            'update'  => route('dealer.website.pages.update', ['page' => '__ID__']),
            'destroy' => route('dealer.website.pages.destroy', ['page' => '__ID__']),
        ];

        return view('dealer.pages.website.pages.index', compact('pages', 'routes'));
    }

    // ── Create Page Form ─────────────────────────────────────────────────────

    public function create(): View
    {
        $routes = [
            'store' => route('dealer.website.pages.store'),
        ];

        return view('dealer.pages.website.pages.create', compact('routes'));
    }

    // ── Store Page ───────────────────────────────────────────────────────────

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title'            => ['required', 'string', 'max:255'],
            'slug'             => ['required', 'string', 'max:255', 'regex:/^[a-z0-9\-]+$/'],
            'content'          => ['nullable', 'string'],
            'meta_title'       => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:255'],
            'meta_keywords'    => ['nullable', 'string'],
            'tags'             => ['nullable'],
            'is_active'        => ['nullable'],
            'is_featured'      => ['nullable'],
            'published_at'     => ['nullable', 'date'],
        ]);

        $dealerId = $request->user()->current_dealer_id;

        // Check slug uniqueness for this dealer
        $exists = Page::forDealer($dealerId)
            ->where('slug', $validated['slug'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['slug' => 'Slug already exists for this dealer.']);
        }

        // Parse tags from JSON string if needed
        $tags = $request->input('tags', '[]');
        if (is_string($tags)) {
            $tags = json_decode($tags, true) ?? [];
        }

        Page::create([
            'dealer_id'        => $dealerId,
            'title'            => $validated['title'],
            'slug'             => $validated['slug'],
            'content'          => $validated['content'] ?? '[]',
            'meta_title'       => $validated['meta_title'] ?? $validated['title'],
            'meta_description' => $validated['meta_description'] ?? null,
            'meta_keywords'    => $validated['meta_keywords'] ?? null,
            'tags'             => $tags,
            'is_active'        => $request->input('is_active') === '1',
            'is_featured'      => $request->boolean('is_featured'),
            'published_at'     => $validated['published_at'] ?? now(),
        ]);

        return redirect()->route('dealer.website.pages.index')
            ->with('success', 'Page created successfully.');
    }

    // ── Edit Page Form ───────────────────────────────────────────────────────

    public function edit(Request $request, Page $page): View | RedirectResponse
    {
        $dealerId = $request->user()->current_dealer_id;

        if ($page->dealer_id !== $dealerId) {
            abort(403);
        }

        $routes = [
            'update' => route('dealer.website.pages.update', ['page' => $page->id]),
        ];

        return view('dealer.pages.website.pages.edit', compact('page', 'routes'));
    }

    // ── Update Page ──────────────────────────────────────────────────────────

    public function update(Request $request, Page $page): RedirectResponse
    {
        $dealerId = $request->user()->current_dealer_id;

        if ($page->dealer_id !== $dealerId) {
            abort(403);
        }

        $validated = $request->validate([
            'title'            => ['required', 'string', 'max:255'],
            'slug'             => ['required', 'string', 'max:255', 'regex:/^[a-z0-9\-]+$/'],
            'content'          => ['nullable', 'string'],
            'meta_title'       => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:255'],
            'meta_keywords'    => ['nullable', 'string'],
            'tags'             => ['nullable'],
            'is_active'        => ['boolean'],
            'is_featured'      => ['boolean'],
            'published_at'     => ['nullable', 'date'],
        ]);

        // Check slug uniqueness (excluding current page)
        $exists = Page::forDealer($dealerId)
            ->where('slug', $validated['slug'])
            ->where('id', '!=', $page->id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['slug' => 'Slug already exists for this dealer.']);
        }

        // Parse tags from JSON string if needed
        $tags = $request->input('tags', '[]');
        if (is_string($tags)) {
            $tags = json_decode($tags, true) ?? [];
        }

        $page->update([
            'title'            => $validated['title'],
            'slug'             => $validated['slug'],
            'content'          => $validated['content'] ?? '[]',
            'meta_title'       => $validated['meta_title'] ?? $validated['title'],
            'meta_description' => $validated['meta_description'] ?? null,
            'meta_keywords'    => $validated['meta_keywords'] ?? null,
            'tags'             => $tags,
            'is_active'        => $request->input('is_active') === '1',
            'is_featured'      => $request->boolean('is_featured'),
            'published_at'     => $validated['published_at'],
        ]);

        return redirect()->route('dealer.website.pages.index')
            ->with('success', 'Page updated successfully.');
    }

    // ── Duplicate Page ───────────────────────────────────────────────────────
    public function duplicate(Request $request, Page $page): RedirectResponse
    {
        $dealerId = $request->user()->current_dealer_id;
        if ($page->dealer_id !== $dealerId) abort(403);

        $newPage = $page->replicate();
        $newPage->title = $page->title . ' (Copy)';
        $newPage->slug = $page->slug . '-copy-' . time();
        $newPage->is_active = false;
        $newPage->save();

        return redirect()->route('dealer.website.pages.index')
            ->with('success', 'Page duplicated successfully.');
    }

    // ── Delete Page ──────────────────────────────────────────────────────────

    public function destroy(Request $request, Page $page): RedirectResponse
    {
        $dealerId = $request->user()->current_dealer_id;

        if ($page->dealer_id !== $dealerId) {
            abort(403);
        }

        $page->delete();

        return redirect()->route('dealer.website.pages.index')
            ->with('success', 'Page deleted successfully.');
    }

    // ── AJAX: Get Pages by Tag ───────────────────────────────────────────────

    public function getByTag(Request $request, string $tag): JsonResponse
    {
        $dealerId = $request->user()->current_dealer_id;

        $pages = Page::forDealer($dealerId)
            ->active()
            ->published()
            ->byTag($tag)
            ->get(['id', 'title', 'slug', 'content', 'published_at']);

        return response()->json($pages);
    }
}
