<?php

namespace App\Actions\Website;

use App\Http\Requests\Website\StoreTradeInRequest;
use App\Models\Website\FormEntry;
use App\Services\Website\DealerResolverService;
use App\Services\Website\VisitorDataService;

class StoreTradeInAction
{
    public function __construct(
        private readonly DealerResolverService $dealerResolver,
        private readonly VisitorDataService    $visitorData,
    ) {}

    public function __invoke(StoreTradeInRequest $request): FormEntry
    {
        return FormEntry::create([
            'dealer_id'    => $this->dealerResolver->resolve(),
            'form_type'    => 'trade_in',
            'status'       => 'complete',
            'vehicle_id'   => $request->vehicle_id,
            'referrer'     => $request->headers->get('referer'),
            'first_name'   => $request->first_name,
            'last_name'    => $request->last_name,
            'email'        => $request->email,
            'phone'        => $request->phone,
            'visitor_data' => $this->visitorData->collect($request),
            'data'         => [
                'vehicle' => [
                    'input_method' => $request->input_method,
                    'year'         => $request->year,
                    'make'         => $request->make,
                    'model'        => $request->model,
                    'trim'         => $request->trim,
                    'body'         => $request->body,
                    'engine'       => $request->engine,
                    'drivetrain'   => $request->drivetrain,
                    'vin'          => $request->vin,
                ],
                'condition' => [
                    'mileage'           => $request->mileage,
                    'postal_code'       => $request->postal_code,
                    'exterior_color'    => $request->exterior_color,
                    'interior_color'    => $request->interior_color,
                    'keys'              => $request->keys,
                    'ownership'         => $request->ownership,
                    'lienholder'        => $request->lienholder,
                    'remaining_balance' => $request->remaining_balance,
                    'overall_condition' => $request->condition,
                    'clean_title'       => $request->clean_title,
                    'run_drive'         => $request->run_drive,
                    'accident'          => $request->accident,
                    'warning_lights'    => $request->warning_lights,
                    'smoked_in'         => $request->smoked_in,
                    'damage'            => $request->damage ?? [],
                    'tire_condition'    => $request->tire_condition,
                ],
                'contact' => [
                    'commpref' => $request->commpref,
                    'comment'  => $request->comment,
                ],
            ],
        ]);
    }
}
