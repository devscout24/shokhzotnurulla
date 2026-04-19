<tr class="data-row" data-id="{{ $rate->id }}">

    <td style="min-width:90px;">
        <select class="fs-inline-select" data-field="make">
            <option value="" @selected(! $rate->make)>Any</option>
            @foreach($makes as $make)
                <option value="{{ $make->name }}" @selected($rate->make === $make->name)>
                    {{ $make->name }}
                </option>
            @endforeach
        </select>
    </td>

    <td style="width:90px;">
        <input type="number" class="fs-inline-input" data-field="min_model_year"
               value="{{ $rate->min_model_year }}" min="2000" max="2099">
    </td>

    <td style="width:90px;">
        <input type="number" class="fs-inline-input" data-field="max_model_year"
               value="{{ $rate->max_model_year }}" min="2000" max="2099">
    </td>

    <td style="width:70px;">
        <input type="number" class="fs-inline-input" data-field="min_term"
               value="{{ $rate->min_term }}" min="0" max="200">
    </td>

    <td style="width:70px;">
        <input type="number" class="fs-inline-input" data-field="max_term"
               value="{{ $rate->max_term }}" min="0" max="200">
    </td>

    <td style="width:80px;">
        <input type="number" class="fs-inline-input" data-field="min_credit_score"
               value="{{ $rate->min_credit_score }}" min="300" max="850">
    </td>

    <td style="width:80px;">
        <input type="number" class="fs-inline-input" data-field="max_credit_score"
               value="{{ $rate->max_credit_score }}" min="300" max="850">
    </td>

    <td style="min-width:90px;">
        <select class="fs-inline-select" data-field="condition">
            <option value="any"  @selected($rate->condition === 'any') >Any</option>
            <option value="new"  @selected($rate->condition === 'new') >New</option>
            <option value="used" @selected($rate->condition === 'used')>Used</option>
            <option value="cpo"  @selected($rate->condition === 'cpo') >CPO</option>
            <option value="vpo"  @selected($rate->condition === 'vpo') >VPO</option>
        </select>
    </td>

    <td style="width:100px;">
        <div class="fs-rate-wrap">
            <input type="number" step="0.01" class="fs-inline-input" data-field="rate"
                   value="{{ $rate->rate }}" min="0" max="99.99">
            <span class="fs-rate-pct">%</span>
        </div>
    </td>

    <td>
        <div class="fs-actions">
            <button class="btn-clone-rate" title="Clone" type="button">
                <i class="bi bi-copy"></i>
            </button>
            <button class="btn-delete-rate" title="Delete" type="button">
                <i class="bi bi-trash text-danger"></i>
            </button>
        </div>
    </td>
</tr>