@extends('messages.mainmess')

@section('title')
    | Sent messages
@endsection

<style>
    .message {
        text-decoration: none;
        color: #000000;
        cursor: auto;
    }
</style>

@section('style2')
    active text-danger
@endsection
@section('contenttype')
    <div class="container">
        <h4 class="text-primary text-center">This is the messages you've sent or replied </h4>
        <?php
        $messages = App\Models\Requests::where('user_id', '=', Auth::user()->id)->get();
        ?>
        <h6 class="text-center">You've sent {{ count($messages) }} messages</h6>
        <br>
        @unless (count($messages) == 0)
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Sent to</th>
                        <th>Message</th>
                        <th>Time</th>
                        <th>Delete</th>
                        <th>Chat</th>
                    </tr>
                </thead>
                @foreach ($messages as $message)
                    <tbody>
                        <?php
                        $messageTime = $message->created_at->setTimezone('Asia/Gaza');
                        $gig = App\Models\Gigs::find($message->gig_id);
                        $reciever = App\Models\User::find($message->reciever);
                        ?>
                        <td>
                            <h5>
                                <a href="/reply/{{ $reciever->id }}" class="text-dark" style="text-decoration: none;">
                                    {{ $reciever->name }}
                                </a>
                            </h5>
                        </td>
                        <td>
                            @if ($message->gig_id)
                                <a href="/gigs/{{ $gig->id }}" class="message">
                                    <p>{{ $message->message }}</p>
                                </a>
                            @else
                                <p>{{ $message->message }}</p>
                            @endif
                        </td>
                        <td>
                            {{ $messageTime->diffForHumans() }}
                        </td>
                        <td>
                            <div class="col">
                                <form method="POST" action="/request/{{ $message->id }}">
                                    @csrf
                                    <button class="btn btn-danger btn-sm">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                        <td>
                            <div class="col">
                                <a href="/reply/{{ $message->reciever }}" class="btn btn-primary btn-sm">
                                    <i class="fa-solid fa-reply"></i>
                                </a>
                            </div>
                        </td>
                    </tbody>
                @endforeach
            </table>
            <div class="text-center">
                <form method="POST" action="/request/sent/{{ $user->id }}">
                    @csrf
                    <button class="btn btn-danger btn-sm">
                        <i class="fa-solid fa-trash"></i>Delete all
                    </button>
                </form>
            </div>
        @else
            <h5 class="bg-light text-danger p-1">You didn't send any messages yet!</h5>
        </div>
    @endunless

@section('scripts')
@endsection
@endsection
</div>
