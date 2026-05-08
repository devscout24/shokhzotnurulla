<?php
namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use App\Models\Website\BlogPost;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WebsiteBlogPostController extends Controller
{
    // ── List Blog Posts ───────────────────────────────────────────────────────

    public function index(Request $request): View
    {
        $dealerId = $request->user()->current_dealer_id;

        $posts = BlogPost::forDealer($dealerId)
            ->orderByPublished()
            ->paginate(20);

        $routes = [
            'create'  => route('dealer.website.blog-posts.create'),
            'store'   => route('dealer.website.blog-posts.store'),
            'edit'    => route('dealer.website.blog-posts.edit', ['blogPost' => '__ID__']),
            'update'  => route('dealer.website.blog-posts.update', ['blogPost' => '__ID__']),
            'destroy' => route('dealer.website.blog-posts.destroy', ['blogPost' => '__ID__']),
        ];

        return view('dealer.pages.website.blog-posts.index', compact('posts', 'routes'));
    }

    // ── Create Blog Post Form ─────────────────────────────────────────────────

    public function create(): View
    {
        $routes = [
            'store' => route('dealer.website.blog-posts.store'),
        ];

        return view('dealer.pages.website.blog-posts.create', compact('routes'));
    }

    // ── Store Blog Post ───────────────────────────────────────────────────────

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
        $exists = BlogPost::forDealer($dealerId)
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

        BlogPost::create([
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

        return redirect()->route('dealer.website.blog-posts.index')
            ->with('success', 'Blog post created successfully.');
    }

    // ── Edit Blog Post Form ───────────────────────────────────────────────────

    public function edit(Request $request, BlogPost $blogPost): View | RedirectResponse
    {
        $dealerId = $request->user()->current_dealer_id;

        if ($blogPost->dealer_id !== $dealerId) {
            abort(403);
        }

        $routes = [
            'update' => route('dealer.website.blog-posts.update', ['blogPost' => $blogPost->id]),
        ];

        return view('dealer.pages.website.blog-posts.edit', compact('blogPost', 'routes'));
    }

    // ── Update Blog Post ──────────────────────────────────────────────────────

    public function update(Request $request, BlogPost $blogPost): RedirectResponse
    {
        $dealerId = $request->user()->current_dealer_id;

        if ($blogPost->dealer_id !== $dealerId) {
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

        // Check slug uniqueness (excluding current post)
        $exists = BlogPost::forDealer($dealerId)
            ->where('slug', $validated['slug'])
            ->where('id', '!=', $blogPost->id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['slug' => 'Slug already exists for this dealer.']);
        }

        // Parse tags from JSON string if needed
        $tags = $request->input('tags', '[]');
        if (is_string($tags)) {
            $tags = json_decode($tags, true) ?? [];
        }

        $blogPost->update([
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

        return redirect()->route('dealer.website.blog-posts.index')
            ->with('success', 'Blog post updated successfully.');
    }

    // ── Duplicate Blog Post ───────────────────────────────────────────────────

    public function duplicate(Request $request, BlogPost $blogPost): RedirectResponse
    {
        $dealerId = $request->user()->current_dealer_id;
        if ($blogPost->dealer_id !== $dealerId) abort(403);

        $newPost = $blogPost->replicate();
        $newPost->title = $blogPost->title . ' (Copy)';
        $newPost->slug = $blogPost->slug . '-copy-' . time();
        $newPost->is_active = false;
        $newPost->save();

        return redirect()->route('dealer.website.blog-posts.index')
            ->with('success', 'Blog post duplicated successfully.');
    }

    // ── Delete Blog Post ──────────────────────────────────────────────────────

    public function destroy(Request $request, BlogPost $blogPost): RedirectResponse
    {
        $dealerId = $request->user()->current_dealer_id;

        if ($blogPost->dealer_id !== $dealerId) {
            abort(403);
        }

        $blogPost->delete();

        return redirect()->route('dealer.website.blog-posts.index')
            ->with('success', 'Blog post deleted successfully.');
    }

    // ── AJAX: Get Posts by Tag ───────────────────────────────────────────────

    public function getByTag(Request $request, string $tag): JsonResponse
    {
        $dealerId = $request->user()->current_dealer_id;

        $posts = BlogPost::forDealer($dealerId)
            ->active()
            ->published()
            ->byTag($tag)
            ->get(['id', 'title', 'slug', 'content', 'published_at']);

        return response()->json($posts);
    }
}
