@extends('layout')

@section('title')
    | Profile - {{ $user->name }}
@endsection

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
@section('content')
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
                        @if ($user->isWorker() && $user->headline)
                            <p class="text-primary fw-semibold mb-2">{{ $user->headline }}</p>
                        @endif
                        @if ($user->bio)
                            <p class="text-muted mb-1w">{{ $user->bio }}</p>
                        @endif
                        @if ($user->isWorker() && $user->skills)
                            @php $skillTags = array_filter(array_map('trim', explode(',', $user->skills))); @endphp
                            @if (count($skillTags))
                                <div class="d-flex flex-wrap gap-1 justify-content-center mb-2">
                                    @foreach (array_slice($skillTags, 0, 8) as $skill)
                                        <span class="badge bg-light text-dark border">{{ $skill }}</span>
                                    @endforeach
                                </div>
                            @endif
                        @endif
                        @if ($user->isWorker() && $user->experience_years !== null)
                            <p class="text-muted small mb-1"><b>{{ $user->experience_years }}+</b> years experience</p>
                        @endif
                        @if ($user->isWorker() && $user->hourly_rate)
                            <p class="text-muted small mb-1">Hourly rate: <b>${{ number_format((float) $user->hourly_rate, 0) }}/hr</b></p>
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
                        @if ($stats['average_rating'])
                            <p class="mb-2">
                                <span class="text-warning">{{ str_repeat('★', (int) round($stats['average_rating'])) }}</span>
                                <b>{{ $stats['average_rating'] }}</b> / 5
                                <small class="text-muted">({{ $stats['reviews_count'] }} reviews)</small>
                            </p>
                        @endif
                        @if ($user->isWorker())
                            @if ($user->portfolio_url)
                                <p class="mb-1"><a href="{{ $user->portfolio_url }}" target="_blank" rel="noopener"><i class="fa-solid fa-globe"></i> Portfolio</a></p>
                            @endif
                            @if ($user->github_url)
                                <p class="mb-1"><a href="{{ $user->github_url }}" target="_blank" rel="noopener"><i class="fa-brands fa-github"></i> GitHub</a></p>
                            @endif
                        @endif
                        <p class="text-muted small mb-4">
                            Member since {{ $stats['member_since']->format('M Y') }}
                        </p>
                        <!-- Button to Open the Modal -->
                        @if (Auth::user()->id == $user->id)
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#GFG">
                                Edit bio
                            </button>
                        @else
                            <a href="/user/chats/{{ Auth::user()->id }}/with/{{ $user->id }}" class="btn btn-success btn-sm">
                                <i class="fa-solid fa-comments"></i> Chat
                            </a>
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
            <div class="col-lg-9">
                <h5 class="fw-semibold mb-3">{{ $user->isWorker() ? 'Freelancer stats' : 'Client stats' }}</h5>
                @include('stats.user-dashboard', ['stats' => $stats])

                @if ($user->isWorker() && ($user->education || $user->certifications))
                    <div class="card mb-3">
                        <div class="card-body">
                            @if ($user->education)
                                <h6 class="fw-semibold">Education</h6>
                                <p class="mb-2">{{ $user->education }}</p>
                            @endif
                            @if ($user->certifications)
                                <h6 class="fw-semibold">Certifications</h6>
                                <p class="mb-0">{{ $user->certifications }}</p>
                            @endif
                        </div>
                    </div>
                @endif

            @if (Auth::user()->id == $user->id)
                    <hr class="dropdown-divider">
                    <h5 class="fw-semibold mb-3">{{ $user->isWorker() ? 'My gigs' : 'My jobs' }}</h5>
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
            @else
                    <hr class="dropdown-divider">
                    <h5 class="fw-semibold mb-3">{{ $user->isWorker() ? 'Listed gigs' : 'Posted jobs' }}</h5>
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
            @endif
            </div>
        </div>

        @if (isset($reviews) && $reviews->isNotEmpty())
            <div class="row mt-4">
                <div class="col-12">
                    <h5>Reviews</h5>
                    @foreach ($reviews as $review)
                        <div class="card mb-2">
                            <div class="card-body py-2">
                                <strong>{{ $review->reviewer->name }}</strong>
                                <span class="text-warning">{{ str_repeat('★', $review->rating) }}</span>
                                <small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                                @if ($review->comment)<p class="mb-0">{{ $review->comment }}</p>@endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

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
