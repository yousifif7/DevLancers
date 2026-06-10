@extends('layout')

@section('title')
    | Admin Support
@endsection

@section('content')
<div class="container py-4">
    @include('admin.partials.nav')
    <h4 class="fw-semibold mb-4">Support Tickets</h4>

    @if (session()->has('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    @foreach ($tickets as $ticket)
        <div class="dl-card p-4 mb-3">
            <div class="d-flex justify-content-between mb-2">
                <strong>{{ $ticket->subject }}</strong>
                <span class="badge bg-{{ $ticket->status === 'open' ? 'warning' : 'success' }}">{{ ucfirst($ticket->status) }}</span>
            </div>
            <p class="text-muted mb-1">{{ $ticket->name }} &lt;{{ $ticket->email }}&gt; · {{ $ticket->created_at->diffForHumans() }}</p>
            <p>{{ $ticket->message }}</p>
            @if ($ticket->admin_reply)
                <div class="alert alert-info py-2"><strong>Your reply:</strong> {{ $ticket->admin_reply }}</div>
            @endif
            @if ($ticket->status === 'open')
                <form method="POST" action="/admin/support/{{ $ticket->id }}/reply" class="mt-2">
                    @csrf
                    <textarea class="form-control mb-2" name="admin_reply" rows="3" placeholder="Reply to user..." required></textarea>
                    <select class="form-select mb-2" name="status">
                        <option value="replied">Mark as Replied</option>
                        <option value="closed">Close Ticket</option>
                    </select>
                    <button class="btn btn-sm regbtn">Send Reply</button>
                </form>
            @endif
        </div>
    @endforeach
    {{ $tickets->links() }}
</div>
@endsection
