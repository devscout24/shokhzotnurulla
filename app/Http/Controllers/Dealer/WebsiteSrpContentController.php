<?php
namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use App\Models\Website\SrpContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WebsiteSrpContentController extends Controller
{
    public function index()
    {
        $contents = SrpContent::orderBy('sort_order')->get();
        return view('dealer.pages.website.srp-content.index', compact('contents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nickname'         => 'required|string|max:255',
            'slug'             => 'required|string|max:255',
            'h1_override'      => 'nullable|string|max:255',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'placement'        => 'required|string|max:50',
            'content'          => 'nullable|string',
            'author'           => 'nullable|string|max:255',
            'status'           => 'required|in:Published,Draft',
        ]);

        if (empty($validated['author'])) {
            $validated['author'] = Auth::user()->name ?? 'System';
        }

        $content = SrpContent::create($validated);
        return response()->json($content);
    }

    public function update(Request $request, SrpContent $srpContent)
    {
        $validated = $request->validate([
            'nickname'         => 'required|string|max:255',
            'slug'             => 'required|string|max:255',
            'h1_override'      => 'nullable|string|max:255',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'placement'        => 'required|string|max:50',
            'content'          => 'nullable|string',
            'author'           => 'nullable|string|max:255',
            'status'           => 'required|in:Published,Draft',
        ]);

        $srpContent->update($validated);
        return response()->json($srpContent);
    }

    public function destroy(SrpContent $srpContent)
    {
        $srpContent->delete();
        return response()->json(['success' => true]);
    }

    public function bulkUpdate(Request $request)
    {
        $data   = $request->input('contents', []);
        $author = Auth::user()->name ?? 'System';

        DB::transaction(function () use ($data, $author) {
            foreach ($data as $item) {
                $id = $item['id'] ?? null;

                if (! empty($item['is_deleted']) && $id) {
                    SrpContent::where('id', $id)->delete();
                    continue;
                }

                $payload = [
                    'nickname'         => $item['nickname'],
                    'slug'             => $item['slug'],
                    'h1_override'      => $item['h1_override'] ?? null,
                    'meta_title'       => $item['meta_title'] ?? null,
                    'meta_description' => $item['meta_description'] ?? null,
                    'placement'        => $item['placement'] ?? 'bottom',
                    'content'          => $item['content'] ?? null,
                    'status'           => $item['status'] ?? 'Published',
                    'author'           => $item['author'] ?? $author,
                ];

                if ($id) {
                    SrpContent::where('id', $id)->update($payload);
                } else {
                    SrpContent::create($payload);
                }
            }
        });

        return response()->json(SrpContent::orderBy('sort_order')->get());
    }
}
