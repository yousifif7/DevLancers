@extends('layout')

@section('title')
    | Proposals — {{ $gig->title }}
@endsection

@section('content')
    @if (session()->has('message'))
        <div class="alert alert-warning alert-dismissible fade show container" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close btn-danger" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="container">
        <button onclick="history.back()" class="back-btn fa-sharp fa-solid fa-left-long fa-xl"></button>
        <br>
        <h4 class="text-center text-primary">Proposals for: {{ $gig->title }}</h4>
        <p class="text-center">
            <span class="badge bg-{{ $gig->status === 'open' ? 'success' : ($gig->status === 'filled' ? 'primary' : 'dark') }}">
                {{ ucfirst($gig->status) }}
            </span>
            <span class="badge bg-secondary">{{ ucfirst($gig->gig_type) }}</span>
        </p>

        @include('stats.listing-stats', ['stats' => $listingStats])

        <div class="card mb-4">
            <div class="card-body py-3">
                <h6 class="fw-semibold mb-2">Posted by <a href="/users/{{ $gig->user_id }}">{{ $gig->user->name }}</a></h6>
                @include('stats.user-mini', ['stats' => $ownerStats])
            </div>
        </div>

        @unless ($proposals->isEmpty())
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>{{ $gig->gig_type === 'job' ? 'Freelancer' : 'Client' }}</th>
                        <th>Cover Letter</th>
                        <th>Bid</th>
                        <th>Delivery</th>
                        <th>Submitted</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($proposals as $proposal)
                        <tr>
                            <td style="min-width:220px">
                                <a href="/users/{{ $proposal->user_id }}" class="fw-semibold">{{ $proposal->user->name }}</a>
                                @include('stats.user-mini', ['stats' => $applicantStats[$proposal->user_id]])
                            </td>
                            <td style="max-width: 300px;">
                                <p class="mb-0">{{ Str::limit($proposal->cover_letter, 200) }}</p>
                            </td>
                            <td><strong>${{ $proposal->bid_amount }}</strong></td>
                            <td>{{ $proposal->delivery_days }} days</td>
                            <td>{{ $proposal->created_at->diffForHumans() }}</td>
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
        @else
            <h5 class="bg-light text-danger p-3 text-center">No proposals for this listing yet.</h5>
        @endunless
    </div>
@endsection
