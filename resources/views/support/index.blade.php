@extends('layout')

@section('title')
    | Support
@endsection

@section('content')
<div class="container py-4" style="max-width:700px">
    @if (session()->has('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    <h4 class="fw-semibold mb-2"><i class="fa-solid fa-life-ring text-primary"></i> Contact Support</h4>
    <p class="text-muted mb-4">Have a question or issue? We're here to help.</p>

    <div class="dl-form-panel mb-4">
        <form method="POST" action="/support">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" class="form-control" name="name" value="{{ Auth::user()->name ?? old('name') }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" value="{{ Auth::user()->email ?? old('email') }}" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Subject</label>
                <input type="text" class="form-control" name="subject" value="{{ old('subject') }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Message</label>
                <textarea class="form-control" name="message" rows="5" required>{{ old('message') }}</textarea>
            </div>
            <button class="regbtn w-100">Submit Support Request</button>
        </form>
    </div>

    @auth
        @if ($tickets->isNotEmpty())
            <h5 class="fw-semibold mb-3">Your tickets</h5>
            @foreach ($tickets as $ticket)
                <div class="dl-card p-3 mb-2">
                    <div class="d-flex justify-content-between">
                        <strong>{{ $ticket->subject }}</strong>
                        <span class="badge bg-{{ $ticket->status === 'open' ? 'warning' : ($ticket->status === 'closed' ? 'secondary' : 'success') }}">
                            {{ ucfirst($ticket->status) }}
                        </span>
                    </div>
                    <p class="mb-1 mt-2 text-muted">{{ Str::limit($ticket->message, 120) }}</p>
                    @if ($ticket->admin_reply)
                        <div class="alert alert-info mt-2 mb-0 py-2">
                            <strong>Support reply:</strong> {{ $ticket->admin_reply }}
                        </div>
                    @endif
                    <small class="text-muted">{{ $ticket->created_at->diffForHumans() }}</small>
                </div>
            @endforeach
        @endif
    @else
        <p class="text-muted"><a href="/login">Log in</a> to track your support tickets.</p>
    @endauth
</div>
@endsection
