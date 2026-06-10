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
        @php
            $galleryImages = $gig->media->where('type', 'image');
            $thumbUrl = $gig->thumbnailUrl();
        @endphp
        @if ($galleryImages->count() > 1)
            <div id="gigGallery" class="carousel slide mx-auto mb-3" style="max-width: 500px;" data-bs-ride="carousel">
                <div class="carousel-inner rounded">
                    @foreach ($galleryImages as $index => $media)
                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                            <img src="{{ $media->url() }}" class="d-block w-100 image" style="max-height: 320px; object-fit: cover;" alt="{{ $gig->title }}">
                        </div>
                    @endforeach
                </div>
                @if ($galleryImages->count() > 1)
                    <button class="carousel-control-prev" type="button" data-bs-target="#gigGallery" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#gigGallery" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                @endif
            </div>
        @elseif ($thumbUrl)
            <img src="{{ $thumbUrl }}" class="image" width="300px" alt="{{ $gig->title }}">
        @else
            @if ($gig->gig_type == 'gig')
                <img src={{ asset('images/gig-logo_default_1024x1024.png') }} class="image" width="300px">
            @else
                <img src={{ asset('images/job1.png') }} class="image" width="300px">
            @endif
        @endif
        <br>
        {{ $gig->created_at->setTimezone('Asia/Gaza')->format('Y-m-d') }}
        <p class="text-primary">{{ $gig->created_at->diffForHumans() }}</p>
    </div>
    <div class="container">
        <hr>
        <h1 class="text-success h3">{{ $gig->title }}</h1>

        <span class="badge bg-{{ $gig->status === 'open' ? 'success' : ($gig->status === 'filled' ? 'primary' : 'dark') }}">
            {{ ucfirst($gig->status) }}
        </span>
        <span class="badge bg-secondary">{{ ucfirst($gig->gig_type) }}</span>
        @if ($proposalCount > 0)
            <span class="badge bg-info">{{ $proposalCount }} {{ Str::plural('proposal', $proposalCount) }}</span>
        @endif

        @include('stats.listing-stats', ['stats' => $listingStats])

        <div class="card mb-3">
            <div class="card-body py-3">
                <h6 class="fw-semibold mb-2">
                    {{ $gig->user->isWorker() ? 'Freelancer' : 'Client' }}:
                    <a href="/users/{{ $gig->user_id }}">{{ $gig->user->name }}</a>
                </h6>
                @include('stats.user-mini', ['stats' => $ownerStats])
            </div>
        </div>

        <hr>
        <p>Based salary: <b class="text-danger">{{ $gig->salary }}$</b></p>
        <p>{{ $gig->description }}</p>
        <p>Contact Email: <b>{{ $gig->email }}</b></p>

        @php $attachments = $gig->media->where('type', 'attachment'); @endphp
        @if ($attachments->isNotEmpty())
            <div class="mb-3">
                <h6 class="fw-semibold">Attachments</h6>
                <ul class="list-unstyled">
                    @foreach ($attachments as $file)
                        <li class="mb-1">
                            <a href="{{ $file->url() }}" target="_blank" rel="noopener">
                                <i class="fa-solid fa-paperclip"></i> {{ $file->original_name ?? basename($file->path) }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        <ul class="nav">
            @foreach ($tags as $tag)
                @if (trim($tag))
                    <li class="nav-item tags">
                        <a class="nav-link"><b>{{ $tag }}</b></a>
                    </li>
                @endif
            @endforeach
        </ul>
        <hr>

        @auth
            @if ($gig->user_id == Auth::user()->id)
                <a href="/gigs/{{ $gig->id }}/edit" class="btn btn-outline-success w-100">
                    <i class="fa-solid fa-pencil"></i> Edit
                </a>
                <br><br>
                @if ($proposalCount > 0)
                    <a href="/gigs/{{ $gig->id }}/proposals" class="btn btn-primary w-100">
                        <i class="fa-solid fa-file-lines"></i> View Proposals ({{ $proposalCount }})
                    </a>
                    <br><br>
                @endif
                @if ($gig->isOpen())
                    <form method="POST" action="/gigs/{{ $gig->id }}/close">
                        @csrf
                        <button class="btn btn-warning w-100">
                            <i class="fa-solid fa-lock"></i> Close Listing
                        </button>
                    </form>
                    <br><br>
                @endif
                <form method="POST" action="/gigs/{{ $gig->id }}">
                    @csrf
                    <button class="btn btn-danger w-100">
                        <i class="fa-solid fa-trash"></i> Delete
                    </button>
                </form>
            @else
                @if ($gig->isOpen())
                    @if (!Auth::user()->canProposeOn($gig))
                        <div class="alert alert-secondary">
                            @if ($gig->gig_type === 'gig' && Auth::user()->isWorker())
                                <strong>This is a worker service (gig).</strong> Workers cannot apply here — only clients can send a hire request. Browse <a href="/?type=job">jobs</a> posted by clients to submit proposals.
                            @elseif ($gig->gig_type === 'job' && Auth::user()->isClient())
                                <strong>This is a client job posting.</strong> Only workers can apply. Browse <a href="/?type=gig">gigs</a> to hire freelancers directly.
                            @endif
                        </div>
                    @elseif ($userProposal)
                        <div class="alert alert-info">
                            <strong>Your proposal:</strong>
                            @include('proposals.partials.status-badge', ['status' => $userProposal->status])
                            <p class="mb-1 mt-2">Bid: <b>${{ $userProposal->bid_amount }}</b> &middot; {{ $userProposal->delivery_days }} days</p>
                            <p class="mb-0">{{ Str::limit($userProposal->cover_letter, 150) }}</p>
                            @if ($userProposal->isActionable())
                                <form method="POST" action="/proposals/{{ $userProposal->id }}/withdraw" class="mt-2">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-danger">Withdraw Proposal</button>
                                </form>
                            @endif
                        </div>
                    @else
                        <h5 class="text-primary">
                            @if ($gig->gig_type == 'job')
                                Submit your proposal for this job
                            @else
                                Place an order for this service
                            @endif
                        </h5>
                        <form class="form" method="POST" action="/gigs/{{ $gig->id }}/proposals">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Cover letter</label>
                                <textarea class="form-control" rows="4" name="cover_letter" required>{{ old('cover_letter') }}</textarea>
                                @error('cover_letter')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                                @error('proposal')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Your bid ($)</label>
                                    <input type="number" class="form-control" name="bid_amount" min="5" max="10000"
                                        step="0.01" value="{{ old('bid_amount', $gig->salary) }}" required>
                                    @error('bid_amount')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Delivery (days)</label>
                                    <input type="number" class="form-control" name="delivery_days" min="1" max="365"
                                        value="{{ old('delivery_days', 7) }}" required>
                                    @error('delivery_days')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <button class="btn btn-success w-100" type="submit">
                                {{ $gig->gig_type == 'job' ? 'Submit Proposal' : 'Place Order' }}
                            </button>
                        </form>
                    @endif
                    <hr>
                    <h6 class="text-muted">Or send a quick message</h6>
                    <form class="form" method="POST" action="/requests">
                        @csrf
                        <input type="hidden" value="{{ Auth::user()->id }}" name="user_id">
                        <input type="hidden" value="{{ $gig->id }}" name="gig_id">
                        <input type="hidden" value="{{ $gig->user_id }}" name="reciever">
                        <input type="hidden" value="{{ Auth::user()->name }}" name="sender">
                        <div class="mb-3">
                            <textarea class="form-control" rows="2" name="message" placeholder="Quick message...">{{ old('message') }}</textarea>
                            @error('message')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <button class="btn btn-outline-primary w-100" type="submit">Send Message</button>
                    </form>
                @else
                    <div class="alert alert-secondary">
                        This listing is <strong>{{ $gig->status }}</strong> and no longer accepts proposals.
                    </div>
                @endif
            @endif
            <hr>
        @else
            <p class="text-danger" style="font-size:20px;">You must be logged in to submit a proposal.</p>
        @endauth

        @auth
            @if ($messages->isNotEmpty())
                <h5>Messages on this listing</h5>
                <table class="table">
                    <tbody>
                        @foreach ($messages as $message)
                            <tr>
                                <td>
                                    <a href="/users/{{ $message->user_id }}" style="text-decoration: none;">
                                        {{ $message->sender }}
                                    </a>
                                    <p>{{ $message->message }}</p>
                                </td>
                                <td>{{ $message->created_at->diffForHumans() }}</td>
                                <td>
                                    <form method="POST" action="/request/{{ $message->id }}">
                                        @csrf
                                        <button class="btn btn-danger btn-sm">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        @endauth
    </div>
@endsection
