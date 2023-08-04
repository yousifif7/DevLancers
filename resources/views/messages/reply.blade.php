@extends('messages.mainmess')

@section('title')
    | Reply - {{$reciever->name}}
@endsection

<?php
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
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </form>
    </div>
@endsection
