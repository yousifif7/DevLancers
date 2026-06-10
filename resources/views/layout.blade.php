<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('partials.seo-head')
    <link rel="icon" type="image/x-icon" href="/images/favicon.ico">
    <link rel="apple-touch-icon" href="{{ asset(config('seo.default_image')) }}">
    <script src="https://kit.fontawesome.com/7ba6153525.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/styles.css">
    @stack('head')
</head>

<body>
    <nav class="navbar dl-navbar sticky-top" aria-label="Main navigation">
        <div class="container-fluid px-4">
            <a class="navbar-brand d-flex align-items-center gap-2" href="/" title="{{ config('seo.site_name') }} – {{ config('seo.tagline') }}">
                <img src="{{ asset('images/devlancer-logo.png') }}" alt="{{ config('seo.site_name') }} logo – developer freelance marketplace" width="48" height="36" class="logo">
                <span class="fw-bold d-none d-sm-inline" style="color:var(--dl-primary)">{{ $siteName ?? 'DevLancer' }}</span>
            </a>
            <ul class="nav align-items-center gap-2">
                <li class="nav-item"><a class="nav-link text-muted" href="/"><i class="fa-solid fa-magnifying-glass"></i> Find Work</a></li>
                <li class="nav-item"><a class="nav-link text-muted" href="/guide"><i class="fa-solid fa-circle-info"></i> How It Works</a></li>
                <li class="nav-item"><a class="nav-link text-muted" href="/support"><i class="fa-solid fa-life-ring"></i> Support</a></li>
                @auth
                    <li class="nav-item"><a class="regbtn" href="/gigs/create"><i class="fa-solid fa-plus"></i> Post</a></li>
                    <li class="nav-item">
                        <form method="post" action="/logout">@csrf
                            <button type="submit" class="dl-btn dl-btn-outline btn-sm">Log out</button>
                        </form>
                    </li>
                @else
                    <li class="nav-item"><a class="regbtn" href="/signup">Sign Up</a></li>
                    <li class="nav-item"><a class="dl-btn dl-btn-outline" href="/login">Login</a></li>
                @endauth
            </ul>
        </div>
    </nav>

    @auth
        <div class="dl-nav-links d-flex justify-content-center flex-wrap">
            <a class="nav-link" href="/gigs/profile"><i class="fa-solid fa-user"></i> {{ Auth::user()->name }}</a>
            <a class="nav-link" href="/user/chats/{{ Auth::user()->id }}">
                <i class="fa-solid fa-comments"></i> Chat
                <span class="dl-nav-badge" data-count="messages">{{ $msgCount ?? 0 }}</span>
            </a>
            <a class="nav-link" href="/user/alerts/{{ Auth::user()->id }}">
                <i class="fa-solid fa-bell"></i> Alerts
                <span class="dl-nav-badge" data-count="alerts">{{ $alertCount ?? 0 }}</span>
            </a>
            <a class="nav-link" href="/user/proposals/{{ Auth::user()->id }}">
                <i class="fa-solid fa-file-lines"></i> Proposals
                <span class="dl-nav-badge" data-count="proposals">{{ $proposalCount ?? 0 }}</span>
            </a>
            <a class="nav-link" href="/user/tasks/{{ Auth::user()->id }}">
                <i class="fa-solid fa-briefcase"></i> Contracts
            </a>
            <a class="nav-link" href="/user/reviews/pending/{{ Auth::user()->id }}">
                <i class="fa-solid fa-star"></i> Reviews
                <span class="dl-nav-badge" data-count="reviews">{{ $pendingReviewCount ?? 0 }}</span>
            </a>
            @if (Auth::user()->is_admin)
                <a class="nav-link" href="/admin"><i class="fa-solid fa-shield"></i> Admin</a>
            @endif
        </div>
    @endauth

    <main class="container-fluid px-4 py-3">
        @yield('createGig')
        @yield('content')
    </main>

    <footer class="dl-footer text-center" role="contentinfo">
        <div class="container py-4">
            <nav class="dl-footer-links mb-3" aria-label="Footer">
                <a href="/">Find Work</a>
                <a href="/?scope=talent">Find Developers</a>
                <a href="/guide">How It Works</a>
                <a href="/support">Support</a>
            </nav>
            <p class="small text-muted mb-1">{{ config('seo.tagline') }}</p>
            <small class="text-light">&copy; {{ date('Y') }} {{ $siteName ?? config('seo.site_name') }}. All rights reserved.</small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @auth
    <script src="/js/devlancer.js?v=2"></script>
    @endauth
    @yield('scripts')
</body>

</html>
