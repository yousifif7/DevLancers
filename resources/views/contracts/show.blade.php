@extends('layout')

@section('title')
    | Contract #{{ $task->id }}
@endsection

@section('content')
    @if (session()->has('message'))
        <div class="alert alert-warning alert-dismissible fade show container" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close btn-danger" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="container">
        <button onclick="history.back()" class="back-btn fa-sharp fa-solid fa-left-long fa-xl"></button>
        <br>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="text-success mb-0">Contract #{{ $task->id }}</h3>
            @include('contracts.partials.status-badge', ['status' => $task->status])
        </div>

        @if ($task->dispute)
            <div class="alert alert-danger">
                <strong>Dispute {{ $task->dispute->status }}:</strong> {{ Str::limit($task->dispute->reason, 200) }}
                @if ($task->dispute->resolution_notes)
                    <hr class="my-2"><strong>Resolution:</strong> {{ $task->dispute->resolution_notes }}
                @endif
            </div>
        @endif

        @if ($task->gig)
            <p><strong>Listing:</strong> <a href="/gigs/{{ $task->gig->id }}">{{ $task->gig->title }}</a></p>
        @endif

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header"><strong>Worker</strong></div>
                    <div class="card-body">
                        <a href="/users/{{ $task->user_id }}">{{ $task->user->name }}</a>
                        @if ($task->worker_accepted_at)
                            <span class="badge bg-success ms-2">Accepted {{ $task->worker_accepted_at->diffForHumans() }}</span>
                        @else
                            <span class="badge bg-warning ms-2">Awaiting acceptance</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header"><strong>Client</strong></div>
                    <div class="card-body">
                        <a href="/users/{{ $task->owner }}">{{ $task->ownerUser->name ?? 'Client' }}</a>
                        @if ($task->client_accepted_at)
                            <span class="badge bg-success ms-2">Accepted {{ $task->client_accepted_at->diffForHumans() }}</span>
                        @else
                            <span class="badge bg-warning ms-2">Awaiting acceptance</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header"><strong>Contract Details</strong></div>
            <div class="card-body">
                <p><strong>Amount:</strong> ${{ $task->price }}
                    @if ($task->usesMilestones())
                        <small class="text-muted">({{ $task->milestones->where('status', 'paid')->count() }}/{{ $task->milestones->count() }} milestones paid)</small>
                    @endif
                </p>
                <p><strong>Paid so far:</strong> ${{ number_format($task->totalPaid(), 2) }}</p>
                @if ($task->start_date && $task->end_date)
                    <p><strong>Timeline:</strong> {{ $task->start_date->format('M d, Y') }} — {{ $task->end_date->format('M d, Y') }}</p>
                @endif
                <p><strong>Scope of work:</strong></p>
                <p>{{ $task->scope_of_work ?? $task->content }}</p>
                <p><strong>Terms:</strong></p>
                <p>{{ $task->terms ?? 'Standard contract terms apply.' }}</p>
            </div>
        </div>

        @if ($isWorker && !Auth::user()->hasStripeConnect())
            <div class="alert alert-info">
                <strong>Worker payouts (optional for testing)</strong>
                <p class="mb-2 small">
                    When a client pays, the platform records your earnings. <strong>Set up Stripe payouts</strong> links your bank account via Stripe Connect so money is transferred automatically — like Upwork paying freelancers.
                </p>
                <p class="mb-2 small text-muted">
                    This requires <a href="https://dashboard.stripe.com/settings/connect" target="_blank" rel="noopener">Stripe Connect enabled</a> on your Stripe dashboard (Test mode is fine). Without it, you can still test contracts and client payments; payouts stay pending in the app.
                </p>
                @error('connect')
                    <p class="text-danger small mb-2">{{ $message }}</p>
                @enderror
                @if (config('stripe.connect_enabled'))
                    <a href="/connect/onboard" class="btn btn-sm btn-primary">Set up Stripe payouts</a>
                @endif
            </div>
        @endif

        @if ($task->canAccept(Auth::id()))
            <form method="POST" action="/tasks/{{ $task->id }}/accept" class="mb-3">
                @csrf
                <button class="btn btn-success w-100"><i class="fa-solid fa-handshake"></i> Accept Contract</button>
            </form>
        @endif

        @if ($task->status === 'draft' && !$task->bothPartiesAccepted())
            <div class="alert alert-info">Both parties must accept before work begins.</div>
        @endif

        @if ($task->canCancel(Auth::id()))
            <form method="POST" action="/tasks/{{ $task->id }}/cancel" class="mb-4" onsubmit="return confirm('Cancel this contract?');">
                @csrf
                <button class="btn btn-outline-danger w-100">Cancel Contract</button>
            </form>
        @endif

        @if ($task->canOpenDispute(Auth::id()))
            <div class="card mb-4 border-warning">
                <div class="card-header"><strong><i class="fa-solid fa-gavel"></i> Open a Dispute</strong></div>
                <div class="card-body">
                    <p class="small text-muted mb-3">
                        Use this if you cannot resolve an issue with the other party (quality, deadlines, scope, or payment).
                        An admin will review the case. See the <a href="/guide#disputes">platform guide</a> for details.
                    </p>
                    <form method="POST" action="/tasks/{{ $task->id }}/dispute">
                        @csrf
                        <textarea class="form-control mb-2" name="reason" rows="3" placeholder="Describe the issue..." required></textarea>
                        @error('reason')<p class="text-danger">{{ $message }}</p>@enderror
                        <button class="btn btn-warning btn-sm">Open Dispute</button>
                    </form>
                </div>
            </div>
        @endif

        @include('contracts.partials.milestones')

        {{-- Contract activity thread --}}
        <div class="card mb-4">
            <div class="card-header">
                <strong><i class="fa-solid fa-clock-rotate-left"></i> Contract History</strong>
            </div>
            <div class="card-body">
                @include('contracts.partials.history', ['events' => $history])
            </div>
        </div>

        @if (!$task->usesMilestones() && !$task->isNegotiatingMilestones())
            <div class="card mb-4">
                <div class="card-header"><strong>Deliverables</strong></div>
                <div class="card-body">
                    @if ($task->canSubmitDeliverable(Auth::id()))
                        <form method="POST" action="/tasks/{{ $task->id }}/deliverables" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Delivery notes</label>
                                <textarea class="form-control" name="notes" rows="4" required>{{ old('notes') }}</textarea>
                            </div>
                            <div class="mb-3">
                                <input type="url" class="form-control" name="link" placeholder="Link (optional)">
                            </div>
                            <div class="mb-3">
                                <input type="file" class="form-control" name="file">
                            </div>
                            <button class="btn btn-primary">Submit for Review</button>
                        </form>
                    @endif

                    @foreach ($task->deliverables as $deliverable)
                        <div class="border rounded p-3 mb-3">
                            <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $deliverable->status)) }}</span>
                            <p class="mt-2">{{ $deliverable->notes }}</p>
                            @if ($deliverable->link)<p><a href="{{ $deliverable->link }}" target="_blank">View link</a></p>@endif
                            @if ($deliverable->file_path)<p><a href="{{ \App\Services\PublicUploadService::url($deliverable->file_path) }}" target="_blank">Download</a></p>@endif
                            @if ($deliverable->revision_notes)
                                <div class="alert alert-warning">{{ $deliverable->revision_notes }}</div>
                            @endif
                            @if ($loop->last && $task->canApproveDeliverable(Auth::id()) && $deliverable->isSubmitted())
                                <form method="POST" action="/deliverables/{{ $deliverable->id }}/approve" class="d-inline">
                                    @csrf
                                    <button class="btn btn-success btn-sm">Approve</button>
                                </form>
                                <button class="btn btn-warning btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#rev{{ $deliverable->id }}">Revision</button>
                                <div class="collapse mt-2" id="rev{{ $deliverable->id }}">
                                    <form method="POST" action="/deliverables/{{ $deliverable->id }}/revision">
                                        @csrf
                                        <textarea class="form-control mb-2" name="revision_notes" required></textarea>
                                        <button class="btn btn-warning btn-sm">Send</button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if ($isClient && !$task->usesMilestones() && !$task->isNegotiatingMilestones())
            <div class="card mb-4">
                <div class="card-header"><strong>Payment</strong></div>
                <div class="card-body">
                    @if ($task->canPay())
                        <form action="/session" method="POST">
                            @csrf
                            <input type="hidden" name="task" value="{{ $task->id }}">
                            <button class="btn btn-success" type="submit"><i class="fa fa-money"></i> Pay ${{ $task->price }}</button>
                        </form>
                    @elseif ($task->isFullyPaid() || $task->payment_flag)
                        <p class="text-success mb-0">Payment completed.</p>
                    @else
                        <p class="text-muted mb-0">Payment unlocks after deliverables are approved.</p>
                    @endif
                </div>
            </div>
        @elseif ($isWorker && ($task->isFullyPaid() || $task->payment_flag))
            <div class="alert alert-success">Payment received for this contract.</div>
        @endif

        @include('contracts.partials.reviews')
    </div>
@endsection
