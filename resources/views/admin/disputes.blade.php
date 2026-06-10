@extends('layout')

@section('title')
    | Admin Disputes
@endsection

@section('content')
<div class="container py-4">
    @include('admin.partials.nav')

    @if (session()->has('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h4 class="fw-semibold mb-0">Disputes</h4>
        <div class="d-flex gap-2 flex-wrap">
            <a href="/admin/disputes" class="btn btn-sm {{ !$status ? 'btn-primary' : 'btn-outline-secondary' }}">All</a>
            <a href="/admin/disputes?status=open" class="btn btn-sm {{ $status === 'open' ? 'btn-danger' : 'btn-outline-secondary' }}">
                Open ({{ $openCount }})
            </a>
            <a href="/admin/disputes?status=resolved" class="btn btn-sm {{ $status === 'resolved' ? 'btn-success' : 'btn-outline-secondary' }}">
                Resolved ({{ $resolvedCount }})
            </a>
        </div>
    </div>

    <div class="alert alert-light border small mb-4">
        <strong>What disputes are for:</strong>
        When a client and worker disagree on a contract (quality, deadlines, payment, scope), either party can open a dispute from the contract page.
        The contract is paused, both parties are notified, and you review the case here — then resolve it with notes and set the contract outcome (resume, cancel, or complete).
    </div>

    @forelse ($disputes as $dispute)
        @php
            $task = $dispute->task;
            $statusColor = $dispute->status === 'open' ? 'danger' : 'success';
        @endphp
        <div class="card mb-3 {{ $dispute->status === 'open' ? 'border-warning' : '' }}">
            <div class="card-header d-flex justify-content-between align-items-start flex-wrap gap-2">
                <div>
                    <span class="badge bg-{{ $statusColor }}">{{ ucfirst($dispute->status) }}</span>
                    <strong class="ms-2">Contract #{{ $dispute->task_id }}</strong>
                    @if ($task?->gig)
                        <span class="text-muted"> — {{ $task->gig->title }}</span>
                    @endif
                </div>
                <small class="text-muted">Opened {{ $dispute->created_at->format('M d, Y g:i A') }}</small>
            </div>
            <div class="card-body">
                <div class="row small mb-3 g-2">
                    <div class="col-md-3">
                        <strong>Opened by:</strong>
                        <a href="/users/{{ $dispute->opened_by }}">{{ $dispute->opener->name }}</a>
                    </div>
                    <div class="col-md-3">
                        <strong>Worker:</strong>
                        @if ($task)
                            <a href="/users/{{ $task->user_id }}">{{ $task->user->name }}</a>
                        @else
                            —
                        @endif
                    </div>
                    <div class="col-md-3">
                        <strong>Client:</strong>
                        @if ($task)
                            <a href="/users/{{ $task->owner }}">{{ $task->ownerUser->name ?? 'Client' }}</a>
                        @else
                            —
                        @endif
                    </div>
                    <div class="col-md-3">
                        <strong>Amount:</strong>
                        @if ($task) ${{ number_format((float) $task->price, 2) }} @else — @endif
                    </div>
                </div>

                <div class="bg-light rounded p-3 mb-3">
                    <strong class="small d-block mb-1">Reason</strong>
                    <p class="mb-0">{{ $dispute->reason }}</p>
                </div>

                @if ($dispute->status === 'open')
                    <form method="POST" action="/admin/disputes/{{ $dispute->id }}/resolve">
                        @csrf
                        <label class="form-label small fw-semibold">Resolution notes (visible to both parties on the contract)</label>
                        <textarea class="form-control mb-2" name="resolution_notes" rows="3"
                            placeholder="Explain your decision and any next steps..." required></textarea>
                        <label class="form-label small fw-semibold">Contract outcome</label>
                        <select class="form-select mb-3" name="contract_status" required>
                            <option value="active">Resume contract (active)</option>
                            <option value="cancelled">Cancel contract</option>
                            <option value="completed">Mark completed</option>
                        </select>
                        <div class="d-flex flex-wrap gap-2">
                            <button class="btn btn-success btn-sm"><i class="fa-solid fa-gavel"></i> Resolve dispute</button>
                            <a href="/tasks/{{ $dispute->task_id }}" class="btn btn-outline-primary btn-sm">View contract</a>
                        </div>
                    </form>
                @else
                    <div class="border-start border-success border-3 ps-3">
                        <strong class="small">Resolution</strong>
                        <p class="mb-1">{{ $dispute->resolution_notes }}</p>
                        <small class="text-muted">Resolved {{ $dispute->updated_at->diffForHumans() }}</small>
                    </div>
                    <a href="/tasks/{{ $dispute->task_id }}" class="btn btn-outline-primary btn-sm mt-3">View contract</a>
                @endif
            </div>
        </div>
    @empty
        <div class="dl-card p-5 text-center text-muted">
            <i class="fa-solid fa-gavel fa-2x mb-3 text-warning"></i>
            <h5 class="fw-semibold text-dark">No disputes yet</h5>
            <p class="mb-2">Disputes appear here when a client or worker opens one on an active contract.</p>
            <p class="small mb-3">
                To test: log in as a client or worker, open an <strong>active</strong> contract, scroll to
                <em>Open a Dispute</em>, describe the issue, and submit.
            </p>
            <a href="/guide#disputes" class="btn btn-outline-primary btn-sm">Read dispute guide</a>
        </div>
    @endforelse

    {{ $disputes->links() }}
</div>
@endsection
