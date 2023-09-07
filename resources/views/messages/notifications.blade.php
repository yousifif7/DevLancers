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
                        <th>Message</th>
                        <th>Time</th>
                        <th>Delete</th>
                        <th>Chat </th>
                        <th>Accept</th>
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
                                <a href="/reply/{{ $message->user_id }}" class="text-dark" style="text-decoration: none;">
                                    {{ $message->sender }}
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
                            <div class="col-3">
                                <form method="POST" action="/request/{{ $message->id }}">
                                    @csrf
                                    <button class="btn btn-danger btn-sm">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                        <td>
                            <div class="col-3">
                                <a href="/reply/{{ $message->user_id }}" class="btn btn-primary btn-sm">
                                    <i class="fa-solid fa-reply"></i>
                                </a>
                            </div>
                        </td>
                        @if (Auth::user()->acc_type == 2 && $message->gig_id)
                            <td>
                                <div class="col-3">
                                    <form method="POST" action="/tasks/create" enctype="multipart/form-data">
                                        @csrf
                                        <input hidden name="user_id" value="{{ $message->user_id }}">
                                        @if ($message->gig_id)
                                            <input hidden name="gig_id" value="{{ $gig->id }}">
                                        @endif
                                        <input hidden name="owner" value="{{ Auth::user()->id }}">

                                        <input hidden name="status" value="Pending">

                                        @if ($message->gig_id)
                                            <input hidden name="price" value="{{ $gig->salary }}">
                                        @else
                                        @endif

                                        <input hidden name="content" value="{{ $message->message }}">

                                        <button type="submit" class="btn btn-success btn-sm">
                                            <i class="fa-solid fa-check"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        @elseif (Auth::user()->acc_type == 1 && $message->gig_id)
                            <td>
                                <div class="col-3">
                                    <form method="POST" action="/tasks/create" enctype="multipart/form-data">
                                        @csrf
                                        <input hidden name="user_id" value="{{ Auth::user()->id }}">
                                        @if ($message->gig_id)
                                            <input hidden name="gig_id" value="{{ $gig->id }}">
                                        @endif
                                        <input hidden name="owner" value="{{ $message->user_id }}">

                                        <input hidden name="status" value="Pending">

                                        @if ($message->gig_id)
                                            <input hidden name="price" value="{{ $gig->salary }}">
                                        @else
                                        @endif

                                        <input hidden name="content" value="{{ $message->message }}">

                                        <input hidden name="payment_flag" value="0">

                                        <button type="submit" class="btn btn-success btn-sm">
                                            <i class="fa-solid fa-check"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        @else
                        <td></td>                                
                        @endif
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
