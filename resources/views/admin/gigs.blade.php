@extends('layout')

@section('title')
    | Admin Listings
@endsection

@section('content')
<div class="container py-4">
    @include('admin.partials.nav')
    <h4 class="fw-semibold mb-4">All Listings</h4>
    <table class="table">
        <thead><tr><th>Title</th><th>Owner</th><th>Type</th><th>Status</th><th>Salary</th></tr></thead>
        <tbody>
            @foreach ($gigs as $gig)
                <tr>
                    <td><a href="/gigs/{{ $gig->id }}">{{ $gig->title }}</a></td>
                    <td>{{ $gig->user->name ?? '—' }}</td>
                    <td>{{ ucfirst($gig->gig_type) }}</td>
                    <td><span class="badge bg-secondary">{{ $gig->status ?? 'open' }}</span></td>
                    <td>${{ $gig->salary }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $gigs->links() }}
</div>
@endsection
