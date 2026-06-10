@extends('proposals.mainprop')

@section('title')
    | Proposal Inbox
@endsection

@section('style1')
    active text-danger
@endsection

@section('contenttype')
    @if (session()->has('message'))
        <div class="alert alert-warning alert-dismissible fade show container" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close btn-danger" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="container">
        <h4 class="text-primary text-center">Proposals received on your listings</h4>
        @include('stats.user-dashboard', ['stats' => Auth::user()->stats()])
        <br>

        @forelse ($grouped as $gigId => $gigProposals)
            @php $gig = $gigProposals->first()->gig; @endphp
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <a href="/gigs/{{ $gig->id }}" class="text-decoration-none">
                            <strong>{{ $gig->title }}</strong>
                        </a>
                        <span class="badge bg-secondary ms-2">{{ ucfirst($gig->gig_type) }}</span>
                        <span class="badge bg-{{ $gig->status === 'open' ? 'success' : ($gig->status === 'filled' ? 'primary' : 'dark') }}">
                            {{ ucfirst($gig->status) }}
                        </span>
                    </div>
                    <a href="/gigs/{{ $gig->id }}/proposals" class="btn btn-sm btn-outline-primary">View all ({{ $gigProposals->count() }})</a>
                </div>
                <div class="card-body border-bottom py-2">
                    @include('stats.listing-stats', ['stats' => $gigStats[$gigId]])
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Applicant stats</th>
                                <th>Bid</th>
                                <th>Delivery</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($gigProposals as $proposal)
                                <tr>
                                    <td style="min-width:220px">
                                        <a href="/users/{{ $proposal->user_id }}" class="fw-semibold">{{ $proposal->user->name }}</a>
                                        <small class="text-muted d-block mb-1">{{ $proposal->created_at->diffForHumans() }}</small>
                                        @include('stats.user-mini', ['stats' => $applicantStats[$proposal->user_id]])
                                    </td>
                                    <td>${{ $proposal->bid_amount }}</td>
                                    <td>{{ $proposal->delivery_days }} days</td>
                                    <td>
                                        @include('proposals.partials.status-badge', ['status' => $proposal->status])
                                    </td>
                                    <td>
                                        @include('proposals.partials.actions', ['proposal' => $proposal, 'gig' => $gig])
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <h5 class="bg-light text-danger p-3 text-center">No proposals received yet.</h5>
        @endforelse
    </div>
@endsection
