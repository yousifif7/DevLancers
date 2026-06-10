@extends('layout')

@section('title')
    | Alerts
@endsection

@section('content')
    @if (session()->has('message'))
        <div class="alert alert-warning alert-dismissible fade show container" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="text-primary mb-0">Your Alerts</h4>
            @if (Auth::user()->unreadNotifications->count() > 0)
                <form method="POST" action="/user/alerts/{{ Auth::user()->id }}/read-all">
                    @csrf
                    <button class="btn btn-sm btn-outline-secondary">Mark all read</button>
                </form>
            @endif
        </div>

        @forelse ($alerts as $alert)
            <div class="card mb-2 {{ $alert->read_at ? '' : 'border-primary' }}">
                <div class="card-body py-2">
                    <div class="d-flex justify-content-between">
                        <div>
                            <strong>{{ $alert->data['title'] ?? 'Notification' }}</strong>
                            @unless ($alert->read_at)
                                <span class="badge bg-primary">New</span>
                            @endunless
                            <p class="mb-0 text-muted">{{ $alert->data['message'] ?? '' }}</p>
                            <small>{{ $alert->created_at->diffForHumans() }}</small>
                        </div>
                        @if (!empty($alert->data['url']))
                            <form method="POST" action="/user/alerts/{{ Auth::user()->id }}/read/{{ $alert->id }}">
                                @csrf
                                <button class="btn btn-sm btn-primary">View</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <p class="text-center text-muted">No alerts yet.</p>
        @endforelse

        {{ $alerts->links() }}
    </div>
@endsection
