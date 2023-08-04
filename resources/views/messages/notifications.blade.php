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

    <div class="container">
        <h4 class="text-primary text-center">This is the messages you've reached on your gigs or jobs</h4>
        <?php
        $messages = App\Models\Requests::where('reciever', '=', Auth::user()->id)->get();
        ?>
        <h6 class="text-center">You've recieved {{ count($messages) }} messages</h6>
        <br>
        @unless (count($messages) == 0)
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Sender</th>
                        <th>Post</th>
                        <th>Time</th>
                        <th>Action</th>
                    </tr>
                </thead>
                @foreach ($messages as $message)
                    <tbody>
                        <?php
                        $messageTime = $message->created_at->setTimezone('Asia/Gaza');
                        $gig = App\Models\Gigs::find($message->gig_id);
                        $sender = App\Models\User::find($message->user_id);
                        ?>
                        <td>
                            <h5>
                                <a class="" href="/users/{{ $sender->id }}" id="{{$sender->id}}"
                                    style="text-decoration: none;">{{ $message->sender }}
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
                            <div class="row w-50">
                                <div class="col-4">
                                    <form method="POST" action="/request/{{ $message->id }}">
                                        @csrf
                                        <button class="btn btn-danger btn-sm">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                                <div class="col">
                                    <a href="/reply/{{$message->user_id}}" class="btn btn-success btn-sm" >
                                        <i class="fa-solid fa-reply"></i>
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tbody>
                @endforeach
            </table>
            <div class="text-center">
                <form method="POST" action="/request/recieved/{{ $user->id }}">
                    @csrf
                    <button class="btn btn-danger btn-sm">
                        <i class="fa-solid fa-trash"></i>Delete all
                    </button>
                </form>
            </div>
        @else
            <h5 class="bg-light text-danger p-1">You have no messages yet!</h5>
        </div>
    @endunless

@section('scripts')
@endsection
@endsection
</div>
