<?php

namespace App\Actions\Website;

use App\Http\Requests\Website\StoreScheduleTestDriveRequest;
use App\Models\Website\FormEntry;
use App\Services\Website\DealerResolverService;
use App\Services\Website\VisitorDataService;

class StoreScheduleTestDriveAction
{
    public function __construct(
        private readonly DealerResolverService $dealerResolver,
        private readonly VisitorDataService    $visitorData,
    ) {}

    public function __invoke(StoreScheduleTestDriveRequest $request): FormEntry
    {
        return FormEntry::create([
            'dealer_id'    => $this->dealerResolver->resolve(),
            'form_type'    => 'schedule_test_drive',
            'status'       => 'complete',
            'vehicle_id'   => $request->vehicle_id,
            'referrer'     => $request->headers->get('referer'),
            'first_name'   => $request->first_name,
            'last_name'    => $request->last_name,
            'email'        => $request->email,
            'phone'        => $request->phone,
            'visitor_data' => $this->visitorData->collect($request),
            'data'         => [
                'preferred_date'      => $request->preferred_date,
                'preferred_day_label' => $request->preferred_day_label,
                'preferred_time'      => $request->preferred_time,
                'commpref'            => $request->commpref,
                'comment'             => $request->comment,
            ],
        ]);
    }
}