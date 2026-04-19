<?php

namespace App\Http\Controllers\Frontend;

use App\Actions\Website\StoreGetApprovedAction;
use App\Actions\Website\StoreScheduleTestDriveAction;
use App\Actions\Website\StoreSimpleFormEntryAction;
use App\Actions\Website\StoreTradeInAction;
use App\Actions\Website\UploadFormEntryPhotosAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Website\StoreGetApprovedRequest;
use App\Http\Requests\Website\StoreScheduleTestDriveRequest;
use App\Http\Requests\Website\StoreSimpleFormRequest;
use App\Http\Requests\Website\StoreTradeInRequest;
use App\Http\Requests\Website\UpdateNpsRequest;
use App\Http\Requests\Website\UploadFormEntryPhotosRequest;
use App\Models\Website\FormEntry;
use Illuminate\Http\JsonResponse;

class FormEntryController extends Controller
{
    public function __construct(
        private readonly StoreTradeInAction           $storeTradeIn,
        private readonly StoreGetApprovedAction       $storeGetApproved,
        private readonly StoreSimpleFormEntryAction   $storeSimple,
        private readonly StoreScheduleTestDriveAction $storeTestDrive,
        private readonly UploadFormEntryPhotosAction  $uploadPhotos,
    ) {}

    public function tradeIn(StoreTradeInRequest $request): JsonResponse
    {
        $entry = ($this->storeTradeIn)($request);

        return response()->json([
            'success'       => true,
            'form_entry_id' => $entry->id,
        ]);
    }

    public function uploadTradeInPhotos(UploadFormEntryPhotosRequest $request): JsonResponse
    {
        $entry  = FormEntry::findOrFail($request->form_entry_id);
        $photos = ($this->uploadPhotos)($request, $entry);

        return response()->json([
            'success' => true,
            'photos'  => $photos,
        ]);
    }

    public function getApproved(StoreGetApprovedRequest $request): JsonResponse
    {
        $entry = ($this->storeGetApproved)($request);

        return response()->json(['success' => true, 'form_entry_id' => $entry->id]);
    }

    public function managersSpecial(StoreSimpleFormRequest $request): JsonResponse
    {
        $entry = ($this->storeSimple)($request, 'managers_special', [
            'comment' => $request->comment,
        ]);
        return response()->json(['success' => true, 'form_entry_id' => $entry->id]);
    }

    public function askQuestion(StoreSimpleFormRequest $request): JsonResponse
    {
        $entry = ($this->storeSimple)($request, 'ask_question', [
            'comment' => $request->comment,
        ]);
        return response()->json(['success' => true, 'form_entry_id' => $entry->id]);
    }

    public function scheduleTestDrive(StoreScheduleTestDriveRequest $request): JsonResponse
    {
        $entry = ($this->storeTestDrive)($request);
        return response()->json(['success' => true, 'form_entry_id' => $entry->id]);
    }

    public function unlockPrice(StoreSimpleFormRequest $request): JsonResponse
    {
        $entry = ($this->storeSimple)($request, 'unlock_eprice');
        return response()->json(['success' => true, 'form_entry_id' => $entry->id]);
    }

    public function contactUs(StoreSimpleFormRequest $request): JsonResponse
    {
        $entry = ($this->storeSimple)($request, 'contact_us', [
            'comment' => $request->comment,
        ]);
        return response()->json(['success' => true, 'form_entry_id' => $entry->id]);
    }

    public function updateNps(UpdateNpsRequest $request, FormEntry $formEntry): JsonResponse
    {
        $formEntry->update(['nps_rating' => $request->rating]);

        return response()->json(['success' => true]);
    }
}