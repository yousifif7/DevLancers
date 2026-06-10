@php
    $colors = [
        'draft' => 'secondary',
        'active' => 'success',
        'in_review' => 'info',
        'completed' => 'primary',
        'cancelled' => 'danger',
        'disputed' => 'warning',
    ];
    $color = $colors[$status] ?? 'secondary';
@endphp
<span class="badge bg-{{ $color }}">{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
