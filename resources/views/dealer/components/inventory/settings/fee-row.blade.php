<tr class="if-row" data-id="{{ $fee->id }}"
    data-name="{{ $fee->name }}"
    data-description="{{ $fee->description ?? '' }}"
    data-type="{{ $fee->type }}"
    data-value="{{ $fee->value }}"
    data-tax="{{ $fee->tax }}"
    data-is-optional="{{ $fee->is_optional ? '1' : '0' }}"
    data-condition="{{ $fee->condition }}"
    data-update-url="{{ route('dealer.inventory.settings.fees.update', $fee) }}">

    {{-- Drag Handle --}}
    <td class="if-drag-handle" style="width:30px; cursor:move;">
        <i class="bi bi-grip-vertical text-muted"></i>
    </td>

    {{-- Dealer + Actions --}}
    <td style="width:25%;">
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

    {{-- Name --}}
    <td style="width:20%;">{{ $fee->name }}</td>

    {{-- Type --}}
    <td style="width:10%;">{{ $fee->type_label }}</td>

    {{-- Value --}}
    <td style="width:10%;">{{ $fee->formatted_value }}</td>

    {{-- Tax --}}
    <td style="width:10%;">{{ $fee->tax_label }}</td>

    {{-- Optional --}}
    <td style="width:10%;">{{ $fee->optional_label }}</td>

    {{-- Condition --}}
    <td style="width:15%;">{{ $fee->condition_label }}</td>
</tr>