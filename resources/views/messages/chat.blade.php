@extends('layout')

@section('title')
    | Chat with {{ $partner->name }}
@endsection

@section('content')
<div class="container py-3">
    <div class="dl-chat-layout">
        <div class="dl-chat-sidebar d-none d-md-block">
            <div class="dl-chat-sidebar-header">
                <a href="/user/chats/{{ Auth::user()->id }}"><i class="fa-solid fa-arrow-left"></i> All chats</a>
            </div>
            <a href="/user/chats/{{ Auth::user()->id }}/with/{{ $partner->id }}" class="dl-convo-item active">
                <strong>{{ $partner->name }}</strong>
            </a>
        </div>

        <div class="dl-chat-main">
            <div class="dl-chat-header d-flex justify-content-between align-items-center">
                <div>
                    <a href="/users/{{ $partner->id }}" class="text-decoration-none fw-semibold">{{ $partner->name }}</a>
                    <small class="text-muted d-block">Live chat · messages are saved permanently</small>
                </div>
                <a href="/user/chats/{{ Auth::user()->id }}" class="btn btn-sm dl-btn-outline d-md-none">Back</a>
            </div>

            <div class="dl-chat-messages" id="chat-messages"
                data-partner="{{ $partner->id }}"
                data-last="{{ $messages->last()?->created_at?->toIso8601String() }}">
                @foreach ($messages as $message)
                    <div class="dl-msg {{ $message->user_id === Auth::id() ? 'dl-msg-mine' : 'dl-msg-theirs' }}" data-id="{{ $message->id }}">
                        <div class="dl-msg-meta">
                            {{ $message->user_id === Auth::id() ? 'You' : $message->sender }}
                            · {{ $message->created_at->format('M d, H:i') }}
                        </div>
                        {{ $message->message }}
                    </div>
                @endforeach
            </div>

            <form class="dl-chat-input" id="chat-form">
                <input type="text" id="chat-input" placeholder="Type a message..." autocomplete="off" maxlength="5000">
                <button type="submit" class="regbtn"><i class="fa-solid fa-paper-plane"></i></button>
            </form>
        </div>
    </div>
</div>
@endsection
