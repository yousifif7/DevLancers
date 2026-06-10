@extends('layout')

@section('title')
    | Messages
@endsection

@section('content')
<div class="container py-4">
    <h4 class="mb-4 fw-semibold">Messages</h4>
    <p class="text-muted">All conversations are saved permanently.</p>

    @forelse ($conversations as $convo)
        <a href="/user/chats/{{ Auth::user()->id }}/with/{{ $convo['user']->id }}" class="dl-convo-item d-block mb-2 rounded">
            <div class="d-flex justify-content-between">
                <strong>{{ $convo['user']->name }}</strong>
                <small>{{ $convo['last_at']->diffForHumans() }}</small>
            </div>
            <small>{{ Str::limit($convo['last_message'], 60) }}</small>
        </a>
    @empty
        <div class="dl-card p-4 text-center text-muted">
            <i class="fa-solid fa-comments fa-2x mb-3"></i>
            <p>No conversations yet. Visit a gig or profile to start chatting.</p>
        </div>
    @endforelse
</div>
@endsection
