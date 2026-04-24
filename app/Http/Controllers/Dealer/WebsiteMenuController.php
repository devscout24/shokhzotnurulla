<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use App\Models\Website\Menu;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WebsiteMenuController extends Controller
{
    // ── Page ─────────────────────────────────────────────────────────────────

    public function menus(Request $request): View
    {
        $dealerId = $request->user()->current_dealer_id;

        $routes = [
            'data'    => route('dealer.website.menus.data'),
            'store'   => route('dealer.website.menus.store'),
            'update'  => route('dealer.website.menus.update',  ['menu' => '__ID__']),
            'destroy' => route('dealer.website.menus.destroy', ['menu' => '__ID__']),
            'reorder' => route('dealer.website.menus.reorder'),
        ];

        return view('dealer.pages.website.menus', compact('routes'));
    }

    // ── AJAX: Data ────────────────────────────────────────────────────────────

    public function data(Request $request): JsonResponse
    {
        $dealerId = $request->user()->current_dealer_id;

        $build = function (string $location) use ($dealerId) {
            return Menu::forDealer($dealerId)
                ->forLocation($location)
                ->topLevel()
                ->with('children')
                ->orderBy('sort_order')
                ->get()
                ->map(fn ($item) => $this->formatItem($item));
        };

        return response()->json([
            'main'   => $build('main'),
            'footer' => $build('footer'),
        ]);
    }

    // ── AJAX: Store ───────────────────────────────────────────────────────────

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'location'  => ['required', 'in:main,footer'],
            'label'     => ['required', 'string', 'max:255'],
            'url'       => ['required', 'string', 'max:255'],
            'target'    => ['nullable', 'in:_self,_blank'],
            'parent_id' => ['nullable', 'integer', 'exists:menus,id'],
        ]);

        $dealerId = $request->user()->current_dealer_id;

        // Sort order — last position
        $sortOrder = Menu::forDealer($dealerId)
            ->forLocation($request->location)
            ->where('parent_id', $request->parent_id)
            ->max('sort_order') + 1;

        $menu = Menu::create([
            'dealer_id'  => $dealerId,
            'location'   => $request->location,
            'label'      => $request->label,
            'url'        => $request->url,
            'target'     => $request->target ?? '_self',
            'parent_id'  => $request->parent_id,
            'sort_order' => $sortOrder,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Menu item added.',
            'item'    => $this->formatItem($menu),
        ]);
    }

    // ── AJAX: Update ──────────────────────────────────────────────────────────

    public function update(Request $request, Menu $menu): JsonResponse
    {
        abort_if($menu->dealer_id !== $request->user()->current_dealer_id, 403);

        $request->validate([
            'label'     => ['required', 'string', 'max:255'],
            'url'       => ['required', 'string', 'max:255'],
            'target'    => ['nullable', 'in:_self,_blank'],
            'parent_id' => ['nullable', 'exists:menus,id'],
        ]);

        $menu->update([
            'label'     => $request->label,
            'url'       => $request->url,
            'target'    => $request->target ?? '_self',
            'parent_id' => $request->parent_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Menu item updated.',
        ]);
    }

    // ── AJAX: Destroy ─────────────────────────────────────────────────────────

    public function destroy(Request $request, Menu $menu): JsonResponse
    {
        abort_if($menu->dealer_id !== $request->user()->current_dealer_id, 403);

        // Children bhi delete ho jayenge (nullOnDelete se parent_id null hoga)
        // Lekin hum children bhi properly delete karna chahte hain
        Menu::where('parent_id', $menu->id)->delete();
        $menu->delete();

        return response()->json([
            'success' => true,
            'message' => 'Menu item removed.',
        ]);
    }

    // ── AJAX: Reorder ─────────────────────────────────────────────────────────

    public function reorder(Request $request): JsonResponse
    {
        $request->validate([
            'items'          => ['required', 'array'],
            'items.*.id'     => ['required', 'integer'],
            'items.*.order'  => ['required', 'integer'],
            'items.*.children'          => ['nullable', 'array'],
            'items.*.children.*.id'     => ['nullable', 'integer'],
            'items.*.children.*.order'  => ['nullable', 'integer'],
        ]);

        $dealerId = $request->user()->current_dealer_id;

        foreach ($request->items as $item) {
            Menu::where('id', $item['id'])
                ->where('dealer_id', $dealerId)
                ->update(['sort_order' => $item['order'], 'parent_id' => null]);

            if (!empty($item['children'])) {
                foreach ($item['children'] as $child) {
                    Menu::where('id', $child['id'])
                        ->where('dealer_id', $dealerId)
                        ->update([
                            'sort_order' => $child['order'],
                            'parent_id'  => $item['id'],
                        ]);
                }
            }
        }

        // Clear cache
        \Illuminate\Support\Facades\Cache::forget("dealer_{$dealerId}_main_menu");
        \Illuminate\Support\Facades\Cache::forget("dealer_{$dealerId}_footer_menu");

        return response()->json(['success' => true, 'message' => 'Order saved.']);
    }

    // ── Private: Format Item ──────────────────────────────────────────────────

    private function formatItem(Menu $item): array
    {
        $data = [
            'id'       => $item->id,
            'label'    => $item->label,
            'url'      => $item->url,
            'target'   => $item->target,
            'parent_id'=> $item->parent_id,
            'children' => [],
        ];

        if ($item->relationLoaded('children')) {
            $data['children'] = $item->children
                ->map(fn ($child) => $this->formatItem($child))
                ->values()
                ->toArray();
        }

        return $data;
    }
}