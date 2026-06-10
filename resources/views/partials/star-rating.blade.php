@php
    $name = $name ?? 'rating';
    $idPrefix = $idPrefix ?? 'rating';
    $default = $default ?? 5;
    $required = $required ?? true;
@endphp

<div class="dl-star-rating" role="radiogroup" aria-label="Rating">
    @for ($i = 5; $i >= 1; $i--)
        <input type="radio"
            id="{{ $idPrefix }}-star-{{ $i }}"
            name="{{ $name }}"
            value="{{ $i }}"
            {{ (int) old($name, $default) === $i ? 'checked' : '' }}
            @if($required) required @endif>
        <label for="{{ $idPrefix }}-star-{{ $i }}" title="{{ $i }} star{{ $i > 1 ? 's' : '' }}">★</label>
    @endfor
</div>
<p class="dl-star-rating-hint small text-muted mb-0 mt-1">Click a star to rate — 1 is poor, 5 is excellent.</p>
