@php
    $isSelected = in_array($option->id, $selectedOptionIds);
    $isStarred  = in_array($option->id, $starredOptionIds);
    $iconClass  = $isSelected
        ? ($isStarred ? 'bi-star-fill' : 'bi-check-square-fill')
        : 'bi-square';
@endphp
<div class="vd-feat-item {{ $indented ? 'vd-feat-item-indented' : '' }}"
     data-option-id="{{ $option->id }}">
    <label class="vd-feat-check-label">
        <input type="checkbox"
               class="vd-factory-option-cb"
               data-option-id="{{ $option->id }}"
               {{ $isSelected ? 'checked' : '' }}>
        <span class="vd-feat-checkbox-icon {{ $isStarred ? 'is-starred' : '' }}">
            <i class="bi {{ $iconClass }}"></i>
        </span>
        <span class="vd-feat-label">{{ $option->label }}</span>
    </label>
    @if($isSelected)
        <button type="button"
                class="vd-feat-star-btn {{ $isStarred ? 'is-starred' : '' }}"
                data-option-id="{{ $option->id }}"
                title="{{ $isStarred ? 'Remove from key features' : 'Mark as key feature' }}"
                aria-label="{{ $isStarred ? 'Remove from key features' : 'Mark as key feature' }}">
            <i class="bi {{ $isStarred ? 'bi-star-fill' : 'bi-star' }}"></i>
        </button>
    @endif
</div>
