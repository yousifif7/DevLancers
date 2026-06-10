<div class="dl-listing-stats row g-2 mb-3">
    <div class="col-6 col-md-3">
        <div class="dl-stat-card py-2">
            <h3 class="mb-0" style="font-size:1.5rem">{{ $stats['proposals_total'] }}</h3>
            <small>Proposals</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="dl-stat-card py-2">
            <h3 class="mb-0" style="font-size:1.5rem">{{ $stats['proposals_pending'] }}</h3>
            <small>Pending review</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="dl-stat-card py-2">
            <h3 class="mb-0" style="font-size:1.5rem">
                @if ($stats['avg_bid'] > 0)
                    ${{ number_format($stats['avg_bid'], 0) }}
                @else
                    —
                @endif
            </h3>
            <small>Avg bid</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="dl-stat-card py-2">
            <h3 class="mb-0" style="font-size:1.5rem">${{ number_format($stats['budget'], 0) }}</h3>
            <small>Budget</small>
        </div>
    </div>
</div>

@if ($stats['proposals_total'] > 0 && $stats['min_bid'] > 0)
    <p class="text-muted small mb-3">
        Bid range: <strong>${{ number_format($stats['min_bid'], 0) }}</strong>
        — <strong>${{ number_format($stats['max_bid'], 0) }}</strong>
        @if ($stats['proposals_accepted'] > 0)
            &middot; {{ $stats['proposals_accepted'] }} hired
        @endif
        @if ($stats['contracts'] > 0)
            &middot; {{ $stats['contracts'] }} contract{{ $stats['contracts'] > 1 ? 's' : '' }}
        @endif
    </p>
@endif
