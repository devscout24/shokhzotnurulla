<tr class="if-row" data-id="{{ $fee->id }}"
    data-name="{{ $fee->name }}"
    data-description="{{ $fee->description ?? '' }}"
    data-type="{{ $fee->type }}"
    data-value="{{ $fee->value }}"
    data-tax="{{ $fee->tax }}"
    data-is-optional="{{ $fee->is_optional ? '1' : '0' }}"
    data-condition="{{ $fee->condition }}"
    data-location-id="{{ $fee->location_id ?? '' }}"
    data-update-url="{{ route('dealer.inventory.settings.fees.update', $fee) }}">

    {{-- Drag Handle --}}
    <td class="if-drag-handle" style="width:30px; cursor:move;">
        <i class="bi bi-grip-vertical text-muted"></i>
    </td>

    {{-- Dealer + Actions --}}
    <td style="width:20%;">
        <div class="if-dealer-name">{{ $dealer->name }}</div>
        <div class="if-row-actions">
            <button class="btn-edit-fee" type="button">
                <i class="bi bi-pencil-square me-1"></i>Edit
            </button>
            <button class="btn-delete-fee" type="button"
                    data-url="{{ route('dealer.inventory.settings.fees.destroy', $fee) }}">
                <i class="bi bi-trash me-1 text-danger"></i>Trash
            </button>
        </div>
    </td>

    {{-- Location --}}
    <td style="width:15%;">
        @if($fee->location_id && $fee->location)
            <span class="badge" style="background:#e8f4f8;color:#166B87;font-weight:500;font-size:11.5px;">
                {{ $fee->location->name }}
            </span>
        @else
            <span class="text-muted" style="font-size:12px;">All locations</span>
        @endif
    </td>

    {{-- Name --}}
    <td style="width:18%;">{{ $fee->name }}</td>

    {{-- Type --}}
    <td style="width:9%;">{{ $fee->type_label }}</td>

    {{-- Value --}}
    <td style="width:9%;">{{ $fee->formatted_value }}</td>

    {{-- Tax --}}
    <td style="width:9%;">{{ $fee->tax_label }}</td>

    {{-- Optional --}}
    <td style="width:10%;">
        @if($fee->is_optional)
            <span class="badge" style="background:#fff3cd;color:#856404;font-size:11px;">Optional</span>
        @else
            <span class="badge" style="background:#d1e7dd;color:#0f5132;font-size:11px;">Guaranteed</span>
        @endif
    </td>

    {{-- Condition --}}
    <td style="width:10%;">{{ $fee->condition_label }}</td>

</tr>