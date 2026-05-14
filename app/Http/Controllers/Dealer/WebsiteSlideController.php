<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use App\Models\Website\Slide;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WebsiteSlideController extends Controller
{
    // ── List Slides ───────────────────────────────────────────────────────────

    public function index(Request $request): View
    {
        $dealerId = $request->user()->current_dealer_id;

        $slides = Slide::forDealer($dealerId)
            ->orderByPublished()
            ->paginate(20);

        $routes = [
            'create'  => route('dealer.website.slides.create'),
            'store'   => route('dealer.website.slides.store'),
            'edit'    => route('dealer.website.slides.edit', ['slide' => '__ID__']),
            'update'  => route('dealer.website.slides.update', ['slide' => '__ID__']),
            'destroy' => route('dealer.website.slides.destroy', ['slide' => '__ID__']),
        ];

        return view('dealer.pages.website.slides.index', compact('slides', 'routes'));
    }

    // ── Create Slide Form ─────────────────────────────────────────────────────

    public function create(): View
    {
        $routes = [
            'store' => route('dealer.website.slides.store'),
        ];

        return view('dealer.pages.website.slides.create', compact('routes'));
    }

    // ── Store Slide ───────────────────────────────────────────────────────────

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
        $exists = Slide::forDealer($dealerId)
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

        Slide::create([
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

        return redirect()->route('dealer.website.slides.index')
            ->with('success', 'Slide created successfully.');
    }

    // ── Edit Slide Form ───────────────────────────────────────────────────────

    public function edit(Request $request, Slide $slide): View | RedirectResponse
    {
        $dealerId = $request->user()->current_dealer_id;

        if ($slide->dealer_id !== $dealerId) {
            abort(403);
        }

        $routes = [
            'update' => route('dealer.website.slides.update', ['slide' => $slide->id]),
        ];

        return view('dealer.pages.website.slides.edit', compact('slide', 'routes'));
    }

    // ── Update Slide ──────────────────────────────────────────────────────────

    public function update(Request $request, Slide $slide): RedirectResponse
    {
        $dealerId = $request->user()->current_dealer_id;

        if ($slide->dealer_id !== $dealerId) {
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

        // Check slug uniqueness (excluding current slide)
        $exists = Slide::forDealer($dealerId)
            ->where('slug', $validated['slug'])
            ->where('id', '!=', $slide->id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['slug' => 'Slug already exists for this dealer.']);
        }

        // Parse tags from JSON string if needed
        $tags = $request->input('tags', '[]');
        if (is_string($tags)) {
            $tags = json_decode($tags, true) ?? [];
        }

        $slide->update([
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

        return redirect()->route('dealer.website.slides.index')
            ->with('success', 'Slide updated successfully.');
    }

    // ── Duplicate Slide ───────────────────────────────────────────────────────
    public function duplicate(Request $request, Slide $slide): RedirectResponse
    {
        $dealerId = $request->user()->current_dealer_id;
        if ($slide->dealer_id !== $dealerId) abort(403);

        $newSlide = $slide->replicate();
        $newSlide->title = $slide->title . ' (Copy)';
        $newSlide->slug = $slide->slug . '-copy-' . time();
        $newSlide->is_active = false;
        $newSlide->save();

        return redirect()->route('dealer.website.slides.index')
            ->with('success', 'Slide duplicated successfully.');
    }

    // ── Delete Slide ──────────────────────────────────────────────────────────

    public function destroy(Request $request, Slide $slide): RedirectResponse
    {
        $dealerId = $request->user()->current_dealer_id;

        if ($slide->dealer_id !== $dealerId) {
            abort(403);
        }

        $slide->delete();

        return redirect()->route('dealer.website.slides.index')
            ->with('success', 'Slide deleted successfully.');
    }

    // ── AJAX: Get Slides by Tag ───────────────────────────────────────────────

    public function getByTag(Request $request, string $tag): JsonResponse
    {
        $dealerId = $request->user()->current_dealer_id;

        $slides = Slide::forDealer($dealerId)
            ->active()
            ->published()
            ->byTag($tag)
            ->get(['id', 'title', 'slug', 'content', 'published_at']);

        return response()->json($slides);
    }
}
