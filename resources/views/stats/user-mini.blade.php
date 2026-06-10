@php
    $isWorker = ($stats['role'] ?? '') === 'worker';
@endphp

<div class="dl-user-mini-stats">
    @if ($stats['average_rating'])
        <span class="dl-mini-stat text-warning" title="Average rating">
            <i class="fa-solid fa-star"></i> {{ $stats['average_rating'] }}
            <small>({{ $stats['reviews_count'] }})</small>
        </span>
    @endif

    @if ($isWorker)
        <span class="dl-mini-stat" title="Jobs completed">
            <i class="fa-solid fa-briefcase"></i> {{ $stats['contracts_completed'] }} completed
        </span>
        <span class="dl-mini-stat" title="Total earned">
            <i class="fa-solid fa-dollar-sign"></i> ${{ number_format($stats['total_earned'], 0) }} earned
        </span>
        @if ($stats['proposal_success_rate'] !== null)
            <span class="dl-mini-stat" title="Proposal win rate">
                <i class="fa-solid fa-chart-line"></i> {{ $stats['proposal_success_rate'] }}% win rate
            </span>
        @endif
        <span class="dl-mini-stat" title="Proposals submitted">
            <i class="fa-solid fa-file-lines"></i> {{ $stats['proposals_submitted'] }} proposals
        </span>
    @else
        <span class="dl-mini-stat" title="Total spent">
            <i class="fa-solid fa-dollar-sign"></i> ${{ number_format($stats['total_spent'], 0) }} spent
        </span>
        <span class="dl-mini-stat" title="Freelancers hired">
            <i class="fa-solid fa-user-check"></i> {{ $stats['hires_made'] }} hires
        </span>
        <span class="dl-mini-stat" title="Jobs posted">
            <i class="fa-solid fa-clipboard-list"></i> {{ $stats['jobs_posted'] }} jobs
        </span>
        <span class="dl-mini-stat" title="Contracts completed">
            <i class="fa-solid fa-circle-check"></i> {{ $stats['contracts_completed'] }} completed
        </span>
    @endif

    <span class="dl-mini-stat text-muted" title="Member since">
        <i class="fa-solid fa-calendar"></i> {{ $stats['member_since']->format('M Y') }}
    </span>
</div>
