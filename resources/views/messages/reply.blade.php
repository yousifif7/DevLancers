@extends('messages.mainmess')

@section('title')
    | Reply - {{ $reciever->name }}
@endsection

<?php
// $m=  App\Models\Requests::where('reciever', '=', $reciever->id);
// $messages = App\Models\Requests::where('user_id', '=', Auth::user()->id)
//     ->where('reciever', '=', $reciever->id)
//     ->get();
$messages = App\Models\Requests::all();

// $count = $messages->count();

?>

@section('content')
    <div class="container">
        <h5 class="text-primary">You're about to send a message, Careful!</h5>
        <form class="form" method="POST" action="/requests" enctype="multipart/form-data">
            @csrf
            <input hidden value="{{ Auth::user()->id }}" name="user_id">

            {{-- <input hidden value="{{ $message->gig_id }}" name="gig_id"> --}}

            <input hidden value="{{ $reciever->id }}" name="reciever">

            <input hidden value="{{ Auth::user()->name }}" name="sender">

            <div class="mb-3">
                <label for="exampleFormControlInput1" class="form-label">Sending to</label>
                <p><b>{{ $reciever->name }}</b></p>
            </div>
            <div class="mb-3">
                <label for="exampleFormControlTextarea1" class="form-label">Message</label>
                <textarea class="form-control" id="exampleFormControlTextarea1" rows="3" name="message">{{ old('message') }}</textarea>
                @error('message')
                    <p class="text-danger">{{ $message }}</p>
                @enderror
            </div>
            <button class="btn btn-success" type="submit">Send</button>
        </form>
    </div>
    <hr>
    <div class="container">

        <section style="background-color: #eee;">
            <div class="container py-5">

                <div class="row d-flex justify-content-center">
                    <div class="col-md-8 col-lg-6 col-xl-4">

                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center p-3"
                                style="border-top: 4px solid #ebd09b;">
                                <h5 class="mb-0"><a class="" href="/users/{{ $reciever->id }}" id="{{ $reciever->id }}"
                                    style="text-decoration: none;">{{ $reciever->name }}
                                </a></h5>
                            </div>
                            <div class="card-body" data-mdb-perfect-scrollbar="true"
                                style="position: relative; height: 400px; overflow:auto">
                                @foreach ($messages as $message)
                                    <?php
                                    $messageTime = $message->created_at->setTimezone('Asia/Gaza');
                                    ?>
                                    @if (
                                        ($message->user_id == Auth::user()->id && $message->reciever == $reciever->id) ||
                                            ($message->user_id == $reciever->id && $message->reciever == Auth::user()->id))

                                        @if ($message->user_id !== Auth::user()->id)
                                            <div class="d-flex justify-content-between">
                                                <p class="small mb-1">{{ $message->sender }}</p>
                                                <p class="small mb-1 text-muted">{{ $messageTime }}</p>
                                            </div>
                                            <div class="d-flex flex-row justify-content-start">
                                                <div>
                                                    <p class="small p-2 ms-3 mb-3 rounded-3"
                                                        style="background-color: #f5f6f7;">
                                                        {{ $message->message }}</p>
                                                </div>
                                            </div>
                                        @else
                                        <div class="d-flex justify-content-between">
                                            <p class="small mb-1 text-muted">{{ $messageTime }}</p>
                                            <p class="small mb-1">{{ $message->sender }}</p>
                                          </div>
                                          <div class="d-flex flex-row justify-content-end mb-4 pt-1">
                                            <div>
                                              <p class="small p-2 me-3 mb-3 text-white rounded-3 bg-dark">{{ $message->message }}</p>
                                            </div>
                                          </div>
                                        @endif
                                    @endif
                                @endforeach
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </section>
    </div>
    <br>
@endsection
