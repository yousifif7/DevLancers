@extends('layout')

@section('title')
    | Profile - {{ $user->name }}
@endsection

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
@section('content')
    <?php
    $gigs = App\Models\Gigs::where('user_id', '=', $user->id)->get();
    ?>

    @if (session()->has('message'))
        <div class="alert alert-warning alert-dismissible fade show container" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close btn-danger" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="container">
        <div class="row">
            <div class="col-lg-3">
                <div class="card mb-4">
                    <div class="card-body text-center">
                        {{-- <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-chat/ava3.webp" alt="avatar"
                    class="rounded-circle img-fluid" style="width: 150px;"> --}}
                        <h5 class="my-3">{{ $user->name }}</h5>
                        @if ($user->bio)
                            <p class="text-muted mb-1w">{{ $user->bio }}</p>
                        @endif
                        @if ($user->address)
                            <p class="text-muted mb-4">Location:<b> {{ $user->address }} </b></p>
                        @endif

                        <p class="text-muted mb-4">Gender: <b>{{ $user->gender }}</b> </p>
                        <hr>

                        @if ($user->acc_type == 1)
                            <p class="text-muted mb-4">Account type: <b class="text-success">Worker</b></p>
                        @else
                            <p class="text-muted mb-4">Account type: <b class="text-warning">Client</b></p>
                        @endif
                        <!-- Button to Open the Modal -->
                        @if (Auth::user()->id == $user->id)
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#GFG">
                                Edit bio
                            </button>
                        @else
                            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal"
                                data-bs-target="#exampleModal">
                                <i class="fa-solid fa-message"></i>Send message
                            </button>
                        @endif
                        {{-- MY lovely modal --}}
                        <div class="modal fade" id="GFG">
                            <div class="modal-dialog  modal-lg  
                            modal-dialog-scrollable ">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="GFGLabel">
                                            Edit your bio
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        @include('registeration.editinfo')
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @if (Auth::user()->id == $user->id)
                <div class="col-lg-9">
                    <hr class="dropdown-divider">
                    @unless ($gigs->isEmpty())
                        <br>
                        <div class="nav-item">
                            @if ($user->acc_type == 1)
                                <a class="btn btn-success w-100" href="/gigs/create" style="text-decoration: none;">Create
                                    GIG</a>
                            @else
                                <a class="btn btn-success w-100" href="/gigs/create" style="text-decoration: none;">Post Job</a>
                            @endif
                        </div><br>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">Gig title</th>
                                    <th scope="col">Edit</th>
                                    <th scope="col">Delete</th>
                                </tr>
                            </thead>
                            @foreach ($gigs as $gig)
                                <tbody>
                                    <td>
                                        <a class="card-body" href="/gigs/{{ $gig->id }}">
                                            <h5>
                                                <a class="" href="/gigs/{{ $gig->id }}"
                                                    style="text-decoration: none;">{{ $gig->title }}</a>
                                            </h5>
                                        </a>
                                    </td>
                                    <td>
                                        <a href="/gigs/{{ $gig->id }}/edit" class="btn btn-sm btn-success">
                                            <i class="fa-solid fa-pencil"></i>
                                        </a>
                                    </td>
                                    <td>
                                        <form method="POST" action="/gigs/{{ $gig->id }}">
                                            @csrf
                                            <button class="btn btn-danger btn-sm">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tbody>
                            @endforeach
                        </table>
                    @else
                        <div class="container">
                            <h5>You don't have any gigs to show!</h5><br>
                            <div class="nav-item">
                                <p>List a new gig.
                                    <a class="" href="/gigs/create" style="text-decoration: none;">Create GIG</a>
                                </p>
                            </div>
                        </div>
                    @endunless
                </div>
            @else
                <div class="col-lg-9">
                    <hr class="dropdown-divider">
                    @unless ($gigs->isEmpty())
                        <br>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">Gig title</th>
                                </tr>
                            </thead>
                            @foreach ($gigs as $gig)
                                <tbody>
                                    <td>
                                        <a class="card-body" href="/gigs/{{ $gig->id }}">
                                            <h5>
                                                <a class="" href="/gigs/{{ $gig->id }}"
                                                    style="text-decoration: none;">{{ $gig->title }}</a>
                                            </h5>
                                        </a>
                                    </td>
                                </tbody>
                            @endforeach
                        </table>
                    @else
                        <div class="container">
                            <h5>{{ $user->name }} don't have any gigs to show!</h5><br>
                        </div>
                    @endunless
                </div>
            @endif
        </div>

        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Reply to a message</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <h5 class="text-primary">You're about to send a message, Careful!</h5>
                        <form class="form" method="POST" action="/requests" enctype="multipart/form-data">
                            @csrf
                            <input hidden value="{{ Auth::user()->id }}" name="user_id">

                            {{-- <input hidden value="{{$message->gig_id}}" name="gig_id"> --}}

                            <input hidden value="{{ $user->id }}" name="reciever">

                            <input hidden value="{{ Auth::user()->name }}" name="sender">

                            <div class="mb-3">
                                <label for="exampleFormControlInput1" class="form-label">Sending to</label>
                                <p><b>{{ $user->name }}</b></p>
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
                </div>
            </div>
        </div>
    @endsection
