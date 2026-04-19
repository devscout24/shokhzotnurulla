<?php

namespace App\Actions\Website;

use App\Http\Requests\Website\StoreSimpleFormRequest;
use App\Models\Website\FormEntry;
use App\Services\Website\DealerResolverService;
use App\Services\Website\VisitorDataService;

class StoreSimpleFormEntryAction
{
    public function __construct(
        private readonly DealerResolverService $dealerResolver,
        private readonly VisitorDataService    $visitorData,
    ) {}

    public function __invoke(
        StoreSimpleFormRequest $request,
        string                 $formType,
        array                  $extraData = []
    ): FormEntry {
        return FormEntry::create([
            'dealer_id'    => $this->dealerResolver->resolve(),
            'form_type'    => $formType,
            'status'       => 'complete',
            'vehicle_id'   => $request->vehicle_id,
            'referrer'     => $request->headers->get('referer'),
            'first_name'   => $request->first_name,
            'last_name'    => $request->last_name,
            'email'        => $request->email,
            'phone'        => $request->phone,
            'visitor_data' => $this->visitorData->collect($request),
            'data'         => array_merge(
                ['commpref' => $request->commpref],
                $extraData
            ),
        ]);
    }
}