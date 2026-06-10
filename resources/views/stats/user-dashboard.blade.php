@php
    $isWorker = ($stats['role'] ?? '') === 'worker';
@endphp

<div class="row g-3 mb-4">
    @if ($isWorker)
        <div class="col-6 col-md-3">
            <div class="dl-stat-card">
                <h3>${{ number_format($stats['total_earned'], 0) }}</h3>
                <small>Total earned</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="dl-stat-card">
                <h3>{{ $stats['contracts_completed'] }}</h3>
                <small>Jobs completed</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="dl-stat-card">
                <h3>{{ $stats['contracts_active'] }}</h3>
                <small>Active contracts</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="dl-stat-card">
                <h3>{{ $stats['proposals_submitted'] }}</h3>
                <small>Proposals sent</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="dl-stat-card">
                <h3>{{ $stats['proposals_accepted'] }}</h3>
                <small>Proposals won</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="dl-stat-card">
                <h3>{{ $stats['proposal_success_rate'] !== null ? $stats['proposal_success_rate'] . '%' : '—' }}</h3>
                <small>Win rate</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="dl-stat-card">
                <h3>{{ $stats['listings_posted'] }}</h3>
                <small>Gigs listed</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="dl-stat-card">
                <h3>
                    @if ($stats['average_rating'])
                        {{ $stats['average_rating'] }}★
                    @else
                        —
                    @endif
                </h3>
                <small>{{ $stats['reviews_count'] }} reviews</small>
            </div>
        </div>
    @else
        <div class="col-6 col-md-3">
            <div class="dl-stat-card">
                <h3>${{ number_format($stats['total_spent'], 0) }}</h3>
                <small>Total spent</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="dl-stat-card">
                <h3>{{ $stats['hires_made'] }}</h3>
                <small>Freelancers hired</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="dl-stat-card">
                <h3>{{ $stats['contracts_active'] }}</h3>
                <small>Active contracts</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="dl-stat-card">
                <h3>{{ $stats['contracts_completed'] }}</h3>
                <small>Jobs completed</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="dl-stat-card">
                <h3>{{ $stats['jobs_posted'] }}</h3>
                <small>Jobs posted</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="dl-stat-card">
                <h3>{{ $stats['proposals_received'] }}</h3>
                <small>Proposals received</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="dl-stat-card">
                <h3>{{ $stats['open_listings'] }}</h3>
                <small>Open listings</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="dl-stat-card">
                <h3>
                    @if ($stats['average_rating'])
                        {{ $stats['average_rating'] }}★
                    @else
                        —
                    @endif
                </h3>
                <small>{{ $stats['reviews_count'] }} reviews</small>
            </div>
        </div>
    @endif
</div>
