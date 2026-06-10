@extends('layout')

@section('title')
    | Admin Dashboard
@endsection

@section('content')
<div class="container py-4">
    @include('admin.partials.nav')

    <h3 class="fw-semibold mb-4">Dashboard</h3>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3"><div class="dl-stat-card"><h3>{{ $userCount }}</h3><small>Total Users</small></div></div>
        <div class="col-6 col-md-3"><div class="dl-stat-card"><h3>{{ $workerCount }}</h3><small>Workers</small></div></div>
        <div class="col-6 col-md-3"><div class="dl-stat-card"><h3>{{ $clientCount }}</h3><small>Clients</small></div></div>
        <div class="col-6 col-md-3"><div class="dl-stat-card"><h3>{{ $gigCount }}</h3><small>Listings</small></div></div>
        <div class="col-6 col-md-3"><div class="dl-stat-card"><h3>{{ $jobCount }}</h3><small>Jobs</small></div></div>
        <div class="col-6 col-md-3"><div class="dl-stat-card"><h3>{{ $contractCount }}</h3><small>Contracts</small></div></div>
        <div class="col-6 col-md-3"><div class="dl-stat-card"><h3>{{ $activeContracts }}</h3><small>Active</small></div></div>
        <div class="col-6 col-md-3"><div class="dl-stat-card"><h3>${{ number_format($paymentTotal, 0) }}</h3><small>Revenue</small></div></div>
        <div class="col-6 col-md-3"><div class="dl-stat-card"><h3 class="text-danger">{{ $openDisputes }}</h3><small>Open Disputes</small></div></div>
        <div class="col-6 col-md-3"><div class="dl-stat-card"><h3 class="text-warning">{{ $openTickets }}</h3><small>Support Tickets</small></div></div>
        <div class="col-6 col-md-3"><div class="dl-stat-card"><h3>{{ $proposalCount }}</h3><small>Proposals</small></div></div>
        <div class="col-6 col-md-3"><div class="dl-stat-card"><h3>{{ $reviewCount }}</h3><small>Reviews</small></div></div>
        <div class="col-6 col-md-3"><div class="dl-stat-card"><h3 class="text-warning">{{ $pendingReviews }}</h3><small>Pending Reviews</small></div></div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="dl-card p-3">
                <h6 class="fw-semibold mb-3">Recent Users</h6>
                @foreach ($recentUsers as $u)
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <a href="/users/{{ $u->id }}">{{ $u->name }}</a>
                        <small class="text-muted">{{ $u->created_at->diffForHumans() }}</small>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="col-md-6">
            <div class="dl-card p-3">
                <h6 class="fw-semibold mb-3">Recent Contracts</h6>
                @foreach ($recentContracts as $c)
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <a href="/tasks/{{ $c->id }}">#{{ $c->id }} {{ $c->gig?->title ?? 'Contract' }}</a>
                        <span class="badge bg-secondary">{{ $c->status }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
