@php
    $colors = [
        'pending' => 'warning',
        'shortlisted' => 'info',
        'accepted' => 'success',
        'rejected' => 'danger',
        'withdrawn' => 'secondary',
    ];
    $color = $colors[$status] ?? 'secondary';
@endphp
<span class="badge bg-{{ $color }}">{{ ucfirst($status) }}</span>
