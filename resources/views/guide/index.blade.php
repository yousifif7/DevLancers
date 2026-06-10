@extends('layout')

@section('title')
    | How It Works
@endsection

@section('content')
<div class="container py-4" style="max-width:900px">

    <div class="dl-hero mb-4">
        <h1>How DevLancer Works</h1>
        <p>Your guide to finding work, hiring talent, contracts, payments, and staying safe on the platform.</p>
    </div>

    {{-- Quick nav --}}
    <div class="d-flex flex-wrap gap-2 mb-4">
        <a href="#roles" class="dl-tag">Roles</a>
        <a href="#gigs-jobs" class="dl-tag">Gigs & Jobs</a>
        <a href="#workers" class="dl-tag">For Workers</a>
        <a href="#clients" class="dl-tag">For Clients</a>
        <a href="#contracts" class="dl-tag">Contracts</a>
        <a href="#payments" class="dl-tag">Payments</a>
        <a href="#reviews" class="dl-tag">Reviews</a>
        <a href="#disputes" class="dl-tag">Disputes</a>
    </div>

    {{-- Overview --}}
    <div class="dl-card p-4 mb-4">
        <h4 class="fw-semibold mb-3"><i class="fa-solid fa-compass text-primary"></i> Platform overview</h4>
        <p class="mb-2">DevLancer connects <strong>workers</strong> (freelancers) with <strong>clients</strong> who need projects done.</p>
        <ol class="mb-0">
            <li>Browse or post listings on the homepage</li>
            <li>Submit proposals and agree on terms</li>
            <li>Work under a contract with clear scope and payment</li>
            <li>Deliver work, get paid, and leave reviews</li>
        </ol>
    </div>

    {{-- Roles --}}
    <div class="dl-card p-4 mb-4" id="roles">
        <h4 class="fw-semibold mb-3"><i class="fa-solid fa-users text-primary"></i> Two roles</h4>
        <div class="row g-3">
            <div class="col-md-6">
                <div class="border rounded p-3 h-100">
                    <h6 class="fw-semibold"><i class="fa-solid fa-laptop-code"></i> Worker (Freelancer)</h6>
                    <ul class="small mb-0 ps-3">
                        <li>Post <strong>gigs</strong> — services you offer (like Fiverr)</li>
                        <li>Apply to <strong>jobs</strong> posted by clients</li>
                        <li>Deliver work and receive payment</li>
                        <li>Build your profile with reviews and completed contracts</li>
                    </ul>
                </div>
            </div>
            <div class="col-md-6">
                <div class="border rounded p-3 h-100">
                    <h6 class="fw-semibold"><i class="fa-solid fa-briefcase"></i> Client</h6>
                    <ul class="small mb-0 ps-3">
                        <li>Post <strong>jobs</strong> — projects you need done</li>
                        <li>Order worker <strong>gigs</strong> directly</li>
                        <li>Hire from proposals and manage contracts</li>
                        <li>Pay when work is approved</li>
                    </ul>
                </div>
            </div>
        </div>
        @guest
            <p class="mt-3 mb-0 small text-muted">Choose your role when you <a href="/signup">sign up</a>. You pick Worker or Client at registration.</p>
        @endguest
    </div>

    {{-- Gigs vs Jobs --}}
    <div class="dl-card p-4 mb-4" id="gigs-jobs">
        <h4 class="fw-semibold mb-3"><i class="fa-solid fa-tags text-primary"></i> Gigs vs Jobs</h4>
        <div class="table-responsive">
            <table class="table table-bordered small mb-0">
                <thead class="table-light">
                    <tr>
                        <th></th>
                        <th>Gig <span class="list-gig">Gig</span></th>
                        <th>Job <span class="list-job">Job</span></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Posted by</strong></td>
                        <td>Worker</td>
                        <td>Client</td>
                    </tr>
                    <tr>
                        <td><strong>Who applies</strong></td>
                        <td>Clients place orders</td>
                        <td>Workers submit proposals</td>
                    </tr>
                    <tr>
                        <td><strong>Can be ordered/hired</strong></td>
                        <td>Many times (repeat orders)</td>
                        <td>Once (one hire per job)</td>
                    </tr>
                    <tr>
                        <td><strong>Think of it like</strong></td>
                        <td>Fiverr service listing</td>
                        <td>Upwork job posting</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Worker flow --}}
    <div class="dl-card p-4 mb-4" id="workers">
        <h4 class="fw-semibold mb-3"><i class="fa-solid fa-laptop-code text-primary"></i> For workers — step by step</h4>
        <div class="dl-guide-steps">
            <div class="dl-guide-step"><span>1</span><div><strong>Complete your profile</strong> — bio, skills, and portfolio help clients trust you.</div></div>
            <div class="dl-guide-step"><span>2</span><div><strong>Post gigs</strong> — list services with price, description, and tags. Gigs stay open for multiple clients.</div></div>
            <div class="dl-guide-step"><span>3</span><div><strong>Browse jobs</strong> — filter homepage by Jobs, submit proposals with bid and delivery time.</div></div>
            <div class="dl-guide-step"><span>4</span><div><strong>Get hired</strong> — when a client accepts your proposal, a contract is created. Both must accept before work starts.</div></div>
            <div class="dl-guide-step"><span>5</span><div><strong>Deliver work</strong> — submit deliverables or complete milestones on the contract page.</div></div>
            <div class="dl-guide-step"><span>6</span><div><strong>Get paid</strong> — after client approval, payment is released. Set up Stripe Connect for payouts.</div></div>
            <div class="dl-guide-step"><span>7</span><div><strong>Leave a review</strong> — after full payment, rate the client (admin approves before it goes public).</div></div>
        </div>
    </div>

    {{-- Client flow --}}
    <div class="dl-card p-4 mb-4" id="clients">
        <h4 class="fw-semibold mb-3"><i class="fa-solid fa-briefcase text-primary"></i> For clients — step by step</h4>
        <div class="dl-guide-steps">
            <div class="dl-guide-step"><span>1</span><div><strong>Post a job</strong> — describe your project, budget, and tags so workers can find it.</div></div>
            <div class="dl-guide-step"><span>2</span><div><strong>Or order a gig</strong> — browse worker gigs and place an order like on Fiverr.</div></div>
            <div class="dl-guide-step"><span>3</span><div><strong>Review proposals</strong> — check bids, delivery times, and profiles. Shortlist or hire from your proposal inbox.</div></div>
            <div class="dl-guide-step"><span>4</span><div><strong>Accept the contract</strong> — review scope, terms, and timeline. Both parties must accept.</div></div>
            <div class="dl-guide-step"><span>5</span><div><strong>Track progress</strong> — use the contract page, milestones, and chat to stay in sync.</div></div>
            <div class="dl-guide-step"><span>6</span><div><strong>Approve & pay</strong> — approve deliverables or milestones, then pay via Stripe.</div></div>
            <div class="dl-guide-step"><span>7</span><div><strong>Leave a review</strong> — share feedback after the contract is fully paid.</div></div>
        </div>
    </div>

    {{-- Contracts --}}
    <div class="dl-card p-4 mb-4" id="contracts">
        <h4 class="fw-semibold mb-3"><i class="fa-solid fa-file-contract text-primary"></i> Contracts</h4>
        <p>A contract is created when a proposal is hired. It includes:</p>
        <ul>
            <li><strong>Scope of work</strong> — what will be delivered</li>
            <li><strong>Price & timeline</strong> — agreed amount and dates</li>
            <li><strong>Both-party acceptance</strong> — worker and client must accept before work begins</li>
            <li><strong>Contract history</strong> — a timeline of everything that happens on the contract</li>
        </ul>
        <p class="mb-0 small text-muted">Find your contracts under <strong>Contracts</strong> in the navigation bar.</p>
    </div>

    {{-- Payments & milestones --}}
    <div class="dl-card p-4 mb-4" id="payments">
        <h4 class="fw-semibold mb-3"><i class="fa-solid fa-money-bill-wave text-primary"></i> Payments & milestones</h4>
        <p><strong>Single payment</strong> — worker delivers everything; client approves once and pays the full amount.</p>
        <p><strong>Milestone payments</strong> — either party can propose splitting the contract into phases. Both must agree to the plan, then:</p>
        <ol class="mb-0">
            <li>Worker completes and submits each milestone</li>
            <li>Client approves (or requests revision)</li>
            <li>Client pays that milestone amount</li>
            <li>Repeat until all milestones are paid</li>
        </ol>
    </div>

    {{-- Reviews --}}
    <div class="dl-card p-4 mb-4" id="reviews">
        <h4 class="fw-semibold mb-3"><i class="fa-solid fa-star text-primary"></i> Reviews</h4>
        <ul class="mb-0">
            <li>Reviews unlock after a contract is <strong>fully paid</strong></li>
            <li>Both parties can rate each other (1–5 stars + optional comment)</li>
            <li>Reviews are <strong>private until both submit</strong> — then you can read each other's feedback</li>
            <li>Admin <strong>moderates</strong> reviews before they appear on public profiles</li>
            <li>Find pending reviews under <strong>Reviews</strong> in the nav bar</li>
        </ul>
    </div>

    {{-- Disputes --}}
    <div class="dl-card p-4 mb-4" id="disputes">
        <h4 class="fw-semibold mb-3"><i class="fa-solid fa-gavel text-warning"></i> Disputes</h4>
        <p>If something goes wrong on an active contract — missed deadlines, quality issues, communication breakdown — either party can <strong>open a dispute</strong> from the contract page.</p>
        <ul>
            <li>Describe the issue clearly (minimum 20 characters)</li>
            <li>The contract is marked as <strong>disputed</strong> and paused</li>
            <li>The other party and platform admin are notified</li>
            <li>An admin reviews the case and resolves it with notes</li>
            <li>Outcome options: resume contract, cancel, or mark completed</li>
        </ul>
        <p class="mb-0 small text-muted">Try to resolve issues through chat first. Disputes are for when you need platform help.</p>
    </div>

    {{-- Navigation help --}}
    <div class="dl-card p-4 mb-4">
        <h4 class="fw-semibold mb-3"><i class="fa-solid fa-map text-primary"></i> Where to find things</h4>
        <div class="row small g-2">
            <div class="col-md-6"><i class="fa-solid fa-house"></i> <strong>Homepage</strong> — browse gigs & jobs</div>
            <div class="col-md-6"><i class="fa-solid fa-file-lines"></i> <strong>Proposals</strong> — inbox & your submissions</div>
            <div class="col-md-6"><i class="fa-solid fa-briefcase"></i> <strong>Contracts</strong> — active & past work</div>
            <div class="col-md-6"><i class="fa-solid fa-comments"></i> <strong>Chat</strong> — message other users</div>
            <div class="col-md-6"><i class="fa-solid fa-bell"></i> <strong>Alerts</strong> — notifications</div>
            <div class="col-md-6"><i class="fa-solid fa-star"></i> <strong>Reviews</strong> — pending feedback</div>
            <div class="col-md-6"><i class="fa-solid fa-life-ring"></i> <strong>Support</strong> — contact the team</div>
            <div class="col-md-6"><i class="fa-solid fa-plus"></i> <strong>Post</strong> — create a gig or job</div>
        </div>
    </div>

    <div class="text-center py-3">
        @guest
            <a href="/signup" class="regbtn me-2">Get Started</a>
            <a href="/login" class="dl-btn dl-btn-outline">Log In</a>
        @else
            <a href="/" class="regbtn">Browse Listings</a>
        @endguest
        <a href="/support" class="dl-btn dl-btn-outline ms-2">Contact Support</a>
    </div>
</div>
@endsection
