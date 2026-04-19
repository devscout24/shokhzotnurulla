<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use App\Models\Website\Media;
use App\Models\Inventory\VehiclePhoto;
use App\Actions\Website\UploadMediaAction;
use App\Actions\Website\DeleteMediaAction;
use App\Http\Requests\Website\UploadMediaRequest;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WebsiteMediaController extends Controller
{
    public function __construct(
        private readonly UploadMediaAction $uploadMedia,
        private readonly DeleteMediaAction $deleteMedia,
    ) {}

    // ── Page ─────────────────────────────────────────────────────────────────

    public function index(): View
    {
        return view('dealer.pages.website.media');
    }

    // ── AJAX: List ───────────────────────────────────────────────────────────

    public function list(Request $request): JsonResponse
    {
        $dealerId = $request->user()->current_dealer_id;

        $query = Media::forDealer($dealerId)->latest();

        if ($request->filled('search')) {
            $query->where('original_name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('type') && in_array($request->type, ['image', 'video', 'file'])) {
            $query->where('type', $request->type);
        }

        $media = $query->paginate(50)->through(fn ($m) => [
            'id'             => $m->id,
            'original_name'  => $m->original_name,
            'url'            => $m->url,
            'type'           => $m->type,
            'mime_type'      => $m->mime_type,
            'size'           => $m->formatted_size,
            'dimensions'     => $m->dimensions,
            'width'          => $m->width,
            'height'         => $m->height,
            'title'          => $m->title,
            'alt_text'       => $m->alt_text,
            'uploaded_by'    => $m->uploader?->full_name ?? '—',
            'created_at'     => $m->created_at->format('M d, Y'),
        ]);

        return response()->json($media);
    }

    // ── AJAX: Upload ─────────────────────────────────────────────────────────

    public function upload(UploadMediaRequest $request): JsonResponse
    {
        $dealerId = $request->user()->current_dealer_id;

        $uploaded = $this->uploadMedia->execute($dealerId, $request->file('files'));

        AuditLogger::info($request, 'Media uploaded', [
            'dealer_id' => $dealerId,
            'count'     => count($uploaded),
        ]);

        return response()->json([
            'success' => true,
            'message' => count($uploaded) . ' file(s) uploaded successfully.',
            'media'   => collect($uploaded)->map(fn ($m) => [
                'id'            => $m->id,
                'original_name' => $m->original_name,
                'url'           => $m->url,
                'type'          => $m->type,
                'size'          => $m->formatted_size,
                'dimensions'    => $m->dimensions,
                'created_at'    => $m->created_at->format('M d, Y'),
            ]),
        ]);
    }

    // Update (title + alt_text)
    public function update(Request $request, Media $media): JsonResponse
    {
        $request->validate([
            'title'    => ['nullable', 'string', 'max:255'],
            'alt_text' => ['nullable', 'string', 'max:255'],
        ]);

        $media->update([
            'title'    => $request->input('title'),
            'alt_text' => $request->input('alt_text'),
        ]);

        return response()->json(['success' => true, 'message' => 'Media updated.']);
    }

    // ── AJAX: Delete ─────────────────────────────────────────────────────────

    public function destroy(Request $request, Media $media): JsonResponse
    {
        abort_if($media->dealer_id !== $request->user()->current_dealer_id, 403);

        $this->deleteMedia->execute($media);

        AuditLogger::warning($request, 'Media deleted', [
            'dealer_id' => $request->user()->current_dealer_id,
            'media_id'  => $media->id,
            'name'      => $media->original_name,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'File deleted successfully.',
        ]);
    }
}