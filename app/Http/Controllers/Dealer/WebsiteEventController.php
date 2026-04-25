<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Website\Event;
use App\Models\Website\EventCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class WebsiteEventController extends Controller
{
    public function index(): View
    {
        $categories = EventCategory::orderBy('sort_order')->withCount('events')->get();
        $events = Event::with('category')->orderBy('sort_order', 'desc')->get();

        return view('dealer.pages.website.events.index', compact('events', 'categories'));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title'             => 'required|string|max:255',
            'event_category_id' => 'required|integer|exists:event_categories,id',
            'photo_url'         => 'required|string|max:255',
            'detail_link'       => 'nullable|string|max:255',
            'registration_link' => 'nullable|string|max:255',
            'event_date'        => 'required|date',
            'start_time'        => 'required',
            'end_time'          => 'required',
            'description'       => 'required|string',
            'status'            => 'required|string',
        ]);

        $validated['author'] = Auth::user()->name;
        $validated['sort_order'] = Event::max('sort_order') + 1;

        $event = Event::create($validated);
        $event->load('category');

        return response()->json($event);
    }

    public function update(Request $request, Event $event): JsonResponse
    {
        $validated = $request->validate([
            'title'             => 'required|string|max:255',
            'event_category_id' => 'required|integer|exists:event_categories,id',
            'photo_url'         => 'required|string|max:255',
            'detail_link'       => 'nullable|string|max:255',
            'registration_link' => 'nullable|string|max:255',
            'event_date'        => 'required|date',
            'start_time'        => 'required',
            'end_time'          => 'required',
            'description'       => 'required|string',
            'status'            => 'required|string',
        ]);

        $event->update($validated);
        $event->load('category');

        return response()->json($event);
    }

    public function destroy(Event $event): JsonResponse
    {
        $event->delete();
        return response()->json(['success' => true]);
    }

    public function bulkUpdate(Request $request): JsonResponse
    {
        $data = $request->input('events', []);
        $author = Auth::user()->name;

        foreach ($data as $index => $item) {
            $id = $item['id'] ?? null;

            if (!empty($item['is_deleted']) && $id) {
                Event::where('id', $id)->delete();
                continue;
            }

            $payload = [
                'title'             => $item['title'] ?? '',
                'event_category_id' => $item['event_category_id'] ?? null,
                'photo_url'         => $item['photo_url'] ?? '',
                'detail_link'       => $item['detail_link'] ?? null,
                'registration_link' => $item['registration_link'] ?? null,
                'event_date'        => $item['event_date'] ?? null,
                'start_time'        => $item['start_time'] ?? null,
                'end_time'          => $item['end_time'] ?? null,
                'description'       => $item['description'] ?? '',
                'status'            => $item['status'] ?? 'Published',
                'sort_order'        => $index,
            ];

            if ($id) {
                Event::where('id', $id)->update($payload);
            } else {
                $payload['author'] = $author;
                Event::create($payload);
            }
        }

        $all = Event::with('category')->orderBy('sort_order')->get();
        return response()->json($all);
    }

    public function storeCategory(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category = EventCategory::create([
            'name'       => $validated['name'],
            'sort_order' => EventCategory::max('sort_order') + 1,
        ]);

        $category->loadCount('events');
        return response()->json($category);
    }

    public function updateCategory(Request $request, EventCategory $eventCategory): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $eventCategory->update($validated);
        $eventCategory->loadCount('events');
        return response()->json($eventCategory);
    }

    public function destroyCategory(EventCategory $eventCategory): JsonResponse
    {
        Event::where('event_category_id', $eventCategory->id)
            ->update(['event_category_id' => null]);

        $eventCategory->delete();
        return response()->json(['success' => true]);
    }
}
