@extends('proposals.mainprop')

@section('title')
    | My Proposals
@endsection

@section('style2')
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
        <h4 class="text-primary text-center">Proposals you have submitted</h4>
        @include('stats.user-dashboard', ['stats' => $stats])
        <br>

        @unless ($proposals->isEmpty())
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Listing</th>
                        <th>Type</th>
                        <th>Your Bid</th>
                        <th>Delivery</th>
                        <th>Status</th>
                        <th>Submitted</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($proposals as $proposal)
                        <tr>
                            <td>
                                <a href="/gigs/{{ $proposal->gig_id }}">{{ $proposal->gig->title }}</a>
                            </td>
                            <td>{{ ucfirst($proposal->gig->gig_type) }}</td>
                            <td>${{ $proposal->bid_amount }}</td>
                            <td>{{ $proposal->delivery_days }} days</td>
                            <td>
                                @include('proposals.partials.status-badge', ['status' => $proposal->status])
                            </td>
                            <td>{{ $proposal->created_at->diffForHumans() }}</td>
                            <td>
                                @if ($proposal->isActionable())
                                    <form method="POST" action="/proposals/{{ $proposal->id }}/withdraw">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-danger">Withdraw</button>
                                    </form>
                                @elseif ($proposal->status === 'accepted')
                                    <span class="badge bg-success">Hired</span>
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <h5 class="bg-light text-danger p-3 text-center">You haven't submitted any proposals yet.</h5>
        @endunless
    </div>
@endsection
