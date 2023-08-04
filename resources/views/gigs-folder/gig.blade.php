@extends('layout')

@section('title')
    | {{ $gig->title }}
@endsection

@php
    $tags = explode(',', $gig->tag);
@endphp



@section('content')
    @if (session()->has('message'))
        <div class="alert alert-warning alert-dismissible fade show container" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close btn-danger" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <div>
        <button onclick="history.back()" class="back-btn fa-sharp fa-solid fa-left-long fa-xl"></button>
    </div>
    <br>
    <div class="container text-center">
        @if ($gig->image)
            <img src={{ asset('storage/' . $gig->image) }} class="image" width="300px">
        @else
            @if ($gig->gig_type == 'gig')
                <img src={{ asset('images/gig-logo_default_1024x1024.png') }} class="image" width="300px">
            @else
                <img src={{ asset('images/job1.png') }} class="image" width="300px">
            @endif
        @endif
        <br>
        <?php
        $time = $gig->created_at->setTimezone('Asia/Gaza');
        ?>
        
        {{ $time->format('Y-m-d') }}
        <p class="text-primary">{{ $time->diffForHumans() }}</p>
    </div>
    <div class="container">
        <hr>
        <h3 class="text-success">
            {{ $gig['title'] }}
        </h3>
        <?php
        $user = App\Models\User::find($gig->user_id);
        $messages = App\Models\Requests::all();
        ?>
        @auth
            @if ($user->acc_type == 1)
                <h4>Worker: <b><a href="/users/{{ $gig->user_id }}">{{ $user->name }}</a></b></h4>
            @else
                <h4>Client: <b><a href="/users/{{ $gig->user_id }}">{{ $user->name }}</a></b></h4>
            @endif
        @else
            @if ($user->acc_type == 1)
                <h4>Worker: <b>{{ $user->name }}</b></h4>
            @else
                <h4>Client: <b>{{ $user->name }}</b></h4>
            @endif
        @endauth
        <hr>
        <p>Based salary: <b class="text-danger">{{ $gig['salary'] }}$</b></p>
        <p>{{ $gig['description'] }}</p>
        <p>Contact Email: <b>{{ $gig['email'] }}</b></p>
        <ul class="nav">
            @foreach ($tags as $tag)
                <li class="nav-item tags">
                    <a class="nav-link"><b>{{ $tag }}</b></a>
                </li>
            @endforeach
        </ul>
        <hr>
        @auth
            @if ($gig->user_id == Auth::user()->id)
                <a href="/gigs/{{ $gig->id }}/edit" class="btn btn-outline-success w-100">
                    <i class="fa-solid fa-pencil"></i> Edit
                </a>
                <br><br>
                <form method="POST" action="/gigs/{{ $gig->id }}">
                    @csrf
                    <button class="btn btn-danger w-100">
                        <i class="fa-solid fa-trash"></i> Delete
                    </button>
                </form>
            @else
                {{-- <a a href="mailto:{{$gig->email}}" class="btn btn-outline-success w-100">Contact</a>
                <p class="text-danger" style="font-size:14px;">Contact is via email.</p> --}}
                <form class="form" method="POST" action="/requests" enctype="multipart/form-data">
                    @csrf
                    <input hidden value="{{Auth::user()->id}}" name="user_id">

                    <input hidden value="{{$gig->id}}" name="gig_id">

                    <input hidden value="{{$gig->user_id}}" name="reciever">

                    <input hidden value="{{Auth::user()->name}}" name="sender">

                    <div class="mb-3">
                        <label for="exampleFormControlInput1" class="form-label">Sender name</label>
                        <p><b>{{Auth::user()->name}}</b></p>
                    </div>
                    <div class="mb-3">
                        <label for="exampleFormControlTextarea1" class="form-label">Message</label>
                        <textarea class="form-control" id="exampleFormControlTextarea1" rows="3" name="message">{{ old('message') }}</textarea>
                        @error('message')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                    <button class="btn btn-success w-100" type="submit">Send</button>
                </form>
            @endif
            <hr>
        @else
            <form>
                @csrf
                <div class="mb-3">
                    <label for="exampleFormControlInput1" class="form-label">Sender name</label>
                    <input type="text" class="form-control" id="exampleFormControlInput1" disabled>
                </div>
                <div class="mb-3">
                    <label for="exampleFormControlTextarea1" class="form-label">Message</label>
                    <textarea class="form-control" id="exampleFormControlTextarea1" rows="3" disabled></textarea>
                </div>
                <button class="gigbtn" type="submit">Send</button>
            </form>
            <p class="text-danger" style="font-size:20px;">You must be logged in to contact with Owner.</p>
        @endauth
        
        @auth
        <table class="table">
            @foreach ($messages as $message)
            @if (Auth::user()->id==$message->user_id || Auth::user()->id==$gig->user_id || $message->reciever==Auth::user()->id)
                <tbody>
                    @if ($message->gig_id == $gig->id)
                        <?php
                        $messageTime = $message->created_at->setTimezone('Asia/Gaza');
                        ?>
                        <td>
                                <h5>
                                    <a class="" href="/users/{{ $message->user_id }}"
                                        style="text-decoration: none;">{{ $message->sender }}
                                    </a>
                                </h5>
                                <p>{{ $message->message }}</p>
                            </a>
                        </td>
                        <td>
                            {{ $messageTime->diffForHumans() }}
                        </td>
                        <td>
                            <form method="POST" action="/request/{{ $message->id }}">
                                @csrf
                                <button class="btn btn-danger btn-sm">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    @endif
                </tbody>
            @endif               
            @endforeach
        </table>
        @endauth
    </div>
@endsection

<script></script>
