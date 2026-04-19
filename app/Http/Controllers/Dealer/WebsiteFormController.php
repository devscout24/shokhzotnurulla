<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Website\BulkFormEntryIdsRequest;
use App\Models\Website\FormEntry;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class WebsiteFormController extends Controller
{
    // ── Index ─────────────────────────────────────────────────────────────────
    public function index(Request $request): View
    {
        $dealerId = $request->user()->current_dealer_id;

        $query = FormEntry::where('dealer_id', $dealerId)
            ->with('vehicle')
            ->latest('submitted_at');

        // ── Tab filter ────────────────────────────────────────────────────────
        match ($request->query('tab', 'all')) {
            'unread'   => $query->unread(),
            'complete' => $query->completed(),
            'abandoned'=> $query->abandoned(),
            'archived' => $query->read(),
            default    => null,
        };

        // ── Search by name ────────────────────────────────────────────────────
        if ($search = $request->query('search')) {
            $query->searchByName($search);
        }

        // ── Date range ────────────────────────────────────────────────────────
        if ($request->filled(['from', 'to'])) {
            $query->dateRange($request->query('from'), $request->query('to'));
        }

        // ── Filter by form type ───────────────────────────────────────────────
        if ($formType = $request->query('form_type')) {
            $query->ofType($formType);
        }

        $entries = $query->paginate(100)->withQueryString();

        return view('dealer.pages.website.form-entries.index', [
            'entries'   => $entries,
            'counts'    => $this->getCounts($dealerId),
            'formTypes' => FormEntry::FORM_TYPES,
        ]);
    }

    // ── Show ──────────────────────────────────────────────────────────────────
    public function show(Request $request, FormEntry $formEntry): JsonResponse
    {
        $this->authorizeEntry($request, $formEntry);

        $formEntry->markAsRead();
        $formEntry->load('vehicle', 'photos');

        if (isset($formEntry->data['borrower']['ssn_encrypted'])) {
            $data = $formEntry->data;
            $data['borrower']['ssn'] = decrypt($data['borrower']['ssn_encrypted']);
            unset($data['borrower']['ssn_encrypted']);

            if (isset($data['coborrower']['ssn_encrypted'])) {
                $data['coborrower']['ssn'] = decrypt($data['coborrower']['ssn_encrypted']);
                unset($data['coborrower']['ssn_encrypted']);
            }

            $formEntry->data = $data;
        }

        if ($formEntry->vehicle) {
            $formEntry->vehicle->append(['display_title']);
        }

        AuditLogger::info($request, 'Form entry viewed', [
            'form_entry_id' => $formEntry->id,
            'form_type'     => $formEntry->form_type,
        ]);

        return response()->json([
            'success' => true,
            'entry'   => $formEntry->append([
                'form_type_label',
                'borrower_type_label',
                'full_name',
            ]),
        ]);
    }

    // ── Mark as Read ──────────────────────────────────────────────────────────
    public function markAsRead(Request $request, FormEntry $formEntry): JsonResponse
    {
        $this->authorizeEntry($request, $formEntry);

        $formEntry->markAsRead();

        return response()->json(['success' => true]);
    }

    // ── Mark as Unread ────────────────────────────────────────────────────────
    public function markAsUnread(Request $request, FormEntry $formEntry): JsonResponse
    {
        $this->authorizeEntry($request, $formEntry);

        $formEntry->markAsUnread();

        return response()->json(['success' => true]);
    }

    // ── Bulk Mark as Read ─────────────────────────────────────────────────────
    public function bulkMarkAsRead(BulkFormEntryIdsRequest $request): JsonResponse
    {
        FormEntry::where('dealer_id', $request->user()->current_dealer_id)
            ->whereIn('id', $request->ids)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return response()->json(['success' => true]);
    }

    // ── Destroy ───────────────────────────────────────────────────────────────
    public function destroy(Request $request, FormEntry $formEntry): JsonResponse
    {
        $this->authorizeEntry($request, $formEntry);

        $formEntry->delete();

        AuditLogger::warning($request, 'Form entry deleted', [
            'form_entry_id' => $formEntry->id,
            'form_type'     => $formEntry->form_type,
        ]);

        return response()->json(['success' => true]);
    }

    // ── Bulk Destroy ──────────────────────────────────────────────────────────
    public function bulkDestroy(BulkFormEntryIdsRequest $request): JsonResponse
    {
        $deleted = FormEntry::where('dealer_id', $request->user()->current_dealer_id)
            ->whereIn('id', $request->ids)
            ->delete();

        AuditLogger::warning($request, 'Form entries bulk deleted', [
            'count' => $deleted,
            'ids'   => $request->ids,
        ]);

        return response()->json(['success' => true]);
    }

    // ── Export ────────────────────────────────────────────────────────────────
    public function export(Request $request): StreamedResponse
    {
        $dealerId = $request->user()->current_dealer_id;
        $filename = 'form-entries-' . now()->format('Y-m-d') . '.csv';

        $entries = FormEntry::where('dealer_id', $dealerId)
            ->with('vehicle')
            ->latest('submitted_at')
            ->get();

        AuditLogger::info($request, 'Form entries exported', [
            'count' => $entries->count(),
        ]);

        return response()->streamDownload(function () use ($entries) {

            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'ID', 'Name', 'Email', 'Phone',
                'Type', 'Status', 'Vehicle',
                'Referrer', 'Submitted At',
            ]);

            foreach ($entries as $entry) {
                fputcsv($handle, [
                    $entry->id,
                    $entry->full_name,
                    $entry->email,
                    $entry->phone,
                    $entry->form_type_label,
                    $entry->status,
                    $entry->vehicle?->display_title ?? $entry->referrer,
                    $entry->referrer,
                    $entry->submitted_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);

        }, $filename, ['Content-Type' => 'text/csv']);
    }

    // ── Private Helpers ───────────────────────────────────────────────────────

    private function authorizeEntry(Request $request, FormEntry $formEntry): void
    {
        abort_if($formEntry->dealer_id !== $request->user()->current_dealer_id, 403);
    }

    private function getCounts(int $dealerId): array
    {
        $base = FormEntry::where('dealer_id', $dealerId);

        return [
            'all'      => (clone $base)->count(),
            'unread'   => (clone $base)->unread()->count(),
            'complete' => (clone $base)->completed()->count(),
            'abandoned'=> (clone $base)->abandoned()->count(),
            'archived' => (clone $base)->read()->count(),
        ];
    }
}