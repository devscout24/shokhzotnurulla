<?php

namespace App\Actions\Website;

use App\Http\Requests\Website\StoreGetApprovedRequest;
use App\Models\Website\FormEntry;
use App\Services\Website\DealerResolverService;
use App\Services\Website\VisitorDataService;

class StoreGetApprovedAction
{
    public function __construct(
        private readonly DealerResolverService $dealerResolver,
        private readonly VisitorDataService    $visitorData,
    ) {}

    public function __invoke(StoreGetApprovedRequest $request): FormEntry
    {
        return FormEntry::create([
            'dealer_id'     => $this->dealerResolver->resolve(),
            'form_type'     => 'get_approved',
            'borrower_type' => $request->borrower_type,
            'status'        => 'complete',
            'vehicle_id'    => $request->vehicle_id,
            'referrer'      => $request->headers->get('referer'),
            'first_name'    => $request->first_name,
            'last_name'     => $request->last_name,
            'email'         => $request->email,
            'phone'         => $request->phone,
            'visitor_data'  => $this->visitorData->collect($request),
            'data'          => [
                'borrower'   => $this->buildBorrowerData($request),
                'coborrower' => $request->borrower_type === 'joint'
                    ? $this->buildCoborrowerData($request)
                    : null,
            ],
        ]);
    }

    private function buildBorrowerData(StoreGetApprovedRequest $request): array
    {
        return [
            'suffix'               => $request->suffix,
            'commpref'             => $request->commpref,
            'address'              => $request->address,
            'city'                 => $request->city,
            'state'                => $request->state,
            'postalcode'           => $request->postalcode,
            'housing'              => $request->housing,
            'currentaddressperiod' => $request->currentaddressperiod,
            'housingpay'           => $request->housingpay,
            'employer'             => $request->employer,
            'position'             => $request->position,
            'wphone'               => $request->wphone,
            'mincome'              => $request->mincome,
            'years'                => $request->years,
            'months'               => $request->months,
            'other_income'         => $request->other,
            'other_income_type'    => $request->otherincomeexpln,
            'other_income_amount'  => $request->otherincome,
            'dob'                  => sprintf(
                '%04d-%02d-%02d',
                $request->year,
                $request->month,
                $request->day
            ),
            'ssn_encrypted'        => encrypt($request->ssn),
            'signature'            => $request->singlesignature,
        ];
    }

    private function buildCoborrowerData(StoreGetApprovedRequest $request): array
    {
        return [
            'first_name'    => $request->spousefirstname,
            'last_name'     => $request->spouselastname,
            'suffix'        => $request->spousesuffix,
            'phone'         => $request->spousephone,
            'address'       => $request->spouseaddress,
            'city'          => $request->spousecity,
            'state'         => $request->spousestate,
            'postalcode'    => $request->spousepostalcode,
            'housing'       => $request->spousehousing,
            'addressperiod' => $request->spouseaddressperiod,
            'housingpay'    => $request->spousehousingpay,
            'employer'      => $request->spouseemployer,
            'position'      => $request->spouseposition,
            'wphone'        => $request->spouseworkphone,
            'mincome'       => $request->spouseincome,
            'years'         => $request->spouseyears,
            'other_income'  => $request->spouseother,
            'dob'           => sprintf(
                '%04d-%02d-%02d',
                $request->spouseyear,
                $request->spousemonth,
                $request->spouseday
            ),
            'ssn_encrypted' => encrypt($request->spousessn),
            'signature'     => $request->jointsignature,
        ];
    }
}
