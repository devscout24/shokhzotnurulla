<?php

namespace App\Http\Requests\Website;

use Illuminate\Foundation\Http\FormRequest;

class StoreGetApprovedRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isJoint = $this->input('borrower_type') === 'joint';

        return [
            'borrower_type'  => ['required', 'in:single,joint'],

            // ── Primary borrower contact ──────────────────────────────────────
            'first_name'     => ['required', 'string', 'min:2', 'max:100'],
            'last_name'      => ['required', 'string', 'min:2', 'max:100'],
            'suffix'         => ['nullable', 'string', 'max:10'],
            'email'          => ['required', 'email', 'max:255'],
            'phone'          => ['required', 'string', 'max:20'],
            'commpref'       => ['required', 'in:email,text,phone'],

            // ── Primary borrower address ──────────────────────────────────────
            'address'               => ['required', 'string', 'max:255'],
            'city'                  => ['required', 'string', 'max:100'],
            'state'                 => ['required', 'string', 'size:2'],
            'postalcode'            => ['required', 'digits:5'],
            'housing'               => ['required', 'in:Rent,Own,Own_Freeandclear,Other'],
            'currentaddressperiod'  => ['required', 'integer', 'min:0'],
            'housingpay'            => ['required', 'numeric', 'min:0'],

            // ── Primary borrower employment ───────────────────────────────────
            'employer'       => ['required', 'string', 'max:255'],
            'position'       => ['required', 'string', 'max:255'],
            'wphone'         => ['required', 'string', 'max:20'],
            'mincome'        => ['required', 'numeric', 'min:0'],
            'years'          => ['required', 'integer', 'min:0'],
            'months'         => ['required', 'integer', 'min:0', 'max:11'],
            'other'          => ['required', 'in:Y,N'],
            'otherincomeexpln' => ['required_if:other,Y', 'nullable', 'string', 'max:255'],
            'otherincome'    => ['required_if:other,Y', 'nullable', 'numeric', 'min:0'],

            // ── Consent (primary — required for all) ──────────────────────────
            'ssn'               => ['required', 'string', 'regex:/^\d{3}-\d{2}-\d{4}$/'],
            'month'             => ['required', 'integer', 'min:1', 'max:12'],
            'day'               => ['required', 'integer', 'min:1', 'max:31'],
            'year'              => ['required', 'integer', 'min:1930', 'max:2010'],
            'singlesignature'   => ['required', 'string', 'max:255'],
            'singleconsent'     => ['required', 'accepted'],
            'regulationbprimary' => ['required', 'accepted'],  // required for all borrower types

            // ── Co-borrower fields (only if joint) ────────────────────────────
            'spousefirstname'      => [$isJoint ? 'required' : 'nullable', 'string', 'max:100'],
            'spouselastname'       => [$isJoint ? 'required' : 'nullable', 'string', 'max:100'],
            'spousesuffix'         => ['nullable', 'string', 'max:10'],
            'spousephone'          => [$isJoint ? 'required' : 'nullable', 'string', 'max:20'],
            'spouseaddress'        => [$isJoint ? 'required' : 'nullable', 'string', 'max:255'],
            'spousecity'           => [$isJoint ? 'required' : 'nullable', 'string', 'max:100'],
            'spousestate'          => [$isJoint ? 'required' : 'nullable', 'string', 'size:2'],
            'spousepostalcode'     => [$isJoint ? 'required' : 'nullable', 'digits:5'],
            'spousehousing'        => [$isJoint ? 'required' : 'nullable', 'in:Rent,Own,Own_Freeandclear,Other'],
            'spouseaddressperiod'  => [$isJoint ? 'required' : 'nullable', 'integer', 'min:0'],
            'spousehousingpay'     => [$isJoint ? 'required' : 'nullable', 'numeric', 'min:0'],
            'spouseemployer'       => [$isJoint ? 'required' : 'nullable', 'string', 'max:255'],
            'spouseposition'       => [$isJoint ? 'required' : 'nullable', 'string', 'max:255'],
            'spouseworkphone'      => [$isJoint ? 'required' : 'nullable', 'string', 'max:20'],
            'spouseincome'         => [$isJoint ? 'required' : 'nullable', 'numeric', 'min:0'],
            'spouseyears'          => [$isJoint ? 'required' : 'nullable', 'integer', 'min:0'],
            'spouseother'          => [$isJoint ? 'required' : 'nullable', 'in:Y,N'],
            'spousessn'            => [$isJoint ? 'required' : 'nullable', 'string', 'regex:/^\d{3}-\d{2}-\d{4}$/'],
            'spousemonth'          => [$isJoint ? 'required' : 'nullable', 'integer', 'min:1', 'max:12'],
            'spouseday'            => [$isJoint ? 'required' : 'nullable', 'integer', 'min:1', 'max:31'],
            'spouseyear'           => [$isJoint ? 'required' : 'nullable', 'integer', 'min:1930', 'max:2010'],
            'jointsignature'       => [$isJoint ? 'required' : 'nullable', 'string', 'max:255'],
            'jointconsent'         => $isJoint ? ['required', 'accepted'] : ['nullable'],
            'regulationbjoint'     => $isJoint ? ['required', 'accepted'] : ['nullable'],
        ];
    }
}
