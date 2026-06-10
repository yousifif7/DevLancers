@extends('layout')

@section('title')
    | Browse
@endsection

@section('content')
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="dl-hero mb-4">
        <h1>{{ $heroTitle }}</h1>
        <p>{{ $heroSubtitle }}</p>
    </div>

    <div class="mb-4">
        <form class="d-flex gap-2" action="/">
            <input class="form-control search flex-grow-1" type="search" placeholder="Search gigs, jobs, tags, worker names..." name="search" value="{{ request('search') }}">
            <button class="search-btn" type="submit"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
        </form>
        <div class="mt-2 d-flex gap-2 flex-wrap">
            <a href="/?type=gig" class="dl-tag">Gigs</a>
            <a href="/?type=job" class="dl-tag">Jobs</a>
        </div>
    </div>

    @unless (count($gigs) == 0)
        <div class="row g-3">
            @foreach ($gigs as $gig)
                @php $tags = explode(',', $gig->tag); @endphp
                <div class="col-lg-6">
                    <div class="dl-listing-card p-3">
                        <div class="row g-3 align-items-center">
                            <div class="col-3">
                                <a href="/gigs/{{ $gig->id }}">
                                    @if ($gig->thumbnailUrl())
                                        <img src="{{ $gig->thumbnailUrl() }}" class="w-100 rounded" style="height:80px;object-fit:cover">
                                    @else
                                        <img src="{{ asset($gig->gig_type == 'gig' ? 'images/gig-logo_default_1024x1024.png' : 'images/job.png') }}" class="w-100 rounded" style="height:80px;object-fit:cover">
                                    @endif
                                </a>
                            </div>
                            <div class="col-9">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <a href="/gigs/{{ $gig->id }}" class="dl-listing-title">{{ $gig->title }}</a>
                                    <div class="text-end">
                                        @if ($gig->gig_type == 'gig')
                                            <span class="list-gig">Gig</span>
                                        @else
                                            <span class="list-job">Job</span>
                                        @endif
                                        <span class="list-price ms-1">{{ $gig->salary }}$</span>
                                    </div>
                                </div>
                                <ul class="nav flex-wrap">
                                    @foreach ($tags as $tag)
                                        @if (trim($tag))
                                            <li class="nav-item"><a href="/?tag={{ trim($tag) }}" class="dl-tag">{{ trim($tag) }}</a></li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-4">{{ $gigs->links() }}</div>
    @else
        <div class="dl-card p-5 text-center text-muted">
            <i class="fa-solid fa-search fa-2x mb-3"></i>
            <p>No listings found. Try a different search.</p>
        </div>
    @endunless
@endsection
