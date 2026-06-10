@extends('messages.mainmess')

@section('title')
    | Notifications
@endsection

<style>
    .message {
        text-decoration: none;
        color: #000000;
        cursor: auto;
    }
</style>
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
        <h4 class="text-center fw-semibold">Inbox</h4>
        <p class="text-center text-muted">
            <a href="/user/chats/{{ Auth::user()->id }}" class="regbtn btn-sm">Open Live Chat</a>
            · Hire via <a href="/user/proposals/{{ Auth::user()->id }}">Proposals</a>
        </p>
        <br>
        @unless (count($messages) == 0)
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Sender</th>
                        <th>Message</th>
                        <th>Time</th>
                        <th>Delete</th>
                        <th>Chat</th>
                    </tr>
                </thead>
                @foreach ($messages as $message)
                    <tbody>
                        <tr>
                        <td>
                            <h5>
                                <a href="/reply/{{ $message->user_id }}" class="text-dark" style="text-decoration: none;">
                                    {{ $message->sender }}
                                </a>
                            </h5>
                        </td>
                        <td>
                            @if ($message->gig_id && $message->gig)
                                <a href="/gigs/{{ $message->gig->id }}" class="message">
                                    <p>{{ $message->message }}</p>
                                </a>
                            @else
                                <p>{{ $message->message }}</p>
                            @endif
                        </td>
                        <td>
                            {{ $message->created_at->setTimezone('Asia/Gaza')->diffForHumans() }}
                        </td>
                        <td>
                            <form method="POST" action="/request/{{ $message->id }}">
                                @csrf
                                <button class="btn btn-danger btn-sm">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </td>
                        <td>
                            <a href="/user/chats/{{ Auth::user()->id }}/with/{{ $message->user_id }}" class="btn btn-primary btn-sm">
                                <i class="fa-solid fa-comments"></i> Chat
                            </a>
                        </td>
                        </tr>
                    </tbody>
                @endforeach
            </table>
            <div class="text-center">
                <form method="POST" action="/request/recieved/{{ $user->id }}">
                    @csrf
                    <button class="btn btn-danger btn-sm">
                        <i class="fa-solid fa-trash"></i> Delete all
                    </button>
                </form>
            </div>
        @else
            <h5 class="bg-light text-danger p-1">You have no messages yet!</h5>
        @endunless
    </div>

@section('scripts')
@endsection
@endsection
