@extends('layout')

@section('title')
    | Profile - {{ Auth::user()->name }}
@endsection

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
@section('content')
    <?php
    $gigs = App\Models\Gigs::where('user_id', '=', Auth::user()->id)->get();
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
                        <h5 class="my-3">{{ Auth::user()->name }}</h5>
                        @if (Auth::user()->bio)
                            <p class="text-muted mb-1w">{{ Auth::user()->bio }}</p>
                        @endif
                        @if (Auth::user()->address)
                            <p class="text-muted mb-4">Location:<b> {{ Auth::user()->address }} </b></p>
                        @endif

                        <p class="text-muted mb-4">Gender: <b>{{ Auth::user()->gender }}</b> </p>
                        <hr>

                        @if (Auth::user()->acc_type == 1)
                            <p class="text-muted mb-4">Business: <b class="text-success">Worker</b></p>
                        @else
                            <p class="text-muted mb-4">Business: <b class="text-warning">Client</b></p>
                        @endif
                        <!-- Button to Open the Modal -->
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#GFG">
                            Edit bio
                        </button>
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
            <div class="col-lg-9">
                <hr class="dropdown-divider">
                @unless ($gigs->isEmpty())
                    <br>
                    <div class="nav-item">
                        @if (Auth::user()->acc_type==1)
                            <a class="btn btn-success w-100" href="/gigs/create" style="text-decoration: none;">Create GIG</a>
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
        </div>
    @endsection
