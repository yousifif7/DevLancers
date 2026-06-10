@php
    $budgetTotal = (float) $task->price;
    $allocated = $task->milestonesTotal();
    $remaining = $task->milestoneBudgetRemaining();
    $pct = $budgetTotal > 0 ? min(100, round(($allocated / $budgetTotal) * 100)) : 0;
    $isBalanced = $task->milestoneBudgetIsBalanced();
    $negotiating = $task->isNegotiatingMilestones();
    $planActive = $task->milestonePlanIsActive();
    $userAgreed = $task->hasUserAgreedToMilestones(Auth::id());
    $otherAgreed = $task->otherPartyHasAgreedToMilestones(Auth::id());
@endphp

<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <strong><i class="fa-solid fa-layer-group"></i> Payment & Milestones</strong>
        @if ($planActive)
            <span class="badge bg-success">Milestone plan active</span>
        @elseif ($negotiating)
            <span class="badge bg-warning text-dark">Awaiting mutual agreement</span>
        @else
            <span class="badge bg-secondary">Single payment</span>
        @endif
    </div>
    <div class="card-body">

        {{-- How it works --}}
        @if (!$planActive)
            <div class="alert alert-light border mb-4 small">
                <strong>How milestone payments work</strong> (like Upwork):
                <ol class="mb-0 mt-2 ps-3">
                    <li>Either the client or worker proposes milestones that split the full contract amount.</li>
                    <li>Both parties review and agree to the plan before work starts on milestones.</li>
                    <li>The worker delivers each milestone; the client approves and pays one at a time.</li>
                </ol>
            </div>
        @endif

        {{-- Payment structure choice (negotiation phase only) --}}
        @if ($task->canManageMilestonePlan(Auth::id()) && !$planActive)
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="border rounded p-3 h-100 {{ !$negotiating ? 'border-primary bg-light' : '' }}">
                        <h6 class="fw-semibold"><i class="fa-solid fa-receipt"></i> Pay on completion</h6>
                        <p class="small text-muted mb-2">One deliverable, one payment when the full job is done.</p>
                        @if (!$negotiating)
                            <span class="badge bg-primary">Current</span>
                        @else
                            <form method="POST" action="/tasks/{{ $task->id }}/milestones/single" class="d-inline"
                                onsubmit="return confirm('Remove all proposed milestones and use single payment?');">
                                @csrf
                                <button class="btn btn-sm btn-outline-secondary">Switch to single payment</button>
                            </form>
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="border rounded p-3 h-100 {{ $negotiating ? 'border-primary bg-light' : '' }}">
                        <h6 class="fw-semibold"><i class="fa-solid fa-list-check"></i> Pay by milestones</h6>
                        <p class="small text-muted mb-2">Split the contract into phases with separate payments.</p>
                        @if ($negotiating)
                            <span class="badge bg-primary">Selected</span>
                        @else
                            <form method="POST" action="/tasks/{{ $task->id }}/milestones/enable">
                                @csrf
                                <button class="btn btn-sm btn-primary">Use milestone payments</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        {{-- Negotiation phase --}}
        @if ($negotiating)
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="small fw-semibold">Budget allocation</span>
                    <span class="small">${{ number_format($allocated, 2) }} / ${{ number_format($budgetTotal, 2) }}</span>
                </div>
                <div class="progress mb-2" style="height: 8px;">
                    <div class="progress-bar {{ $isBalanced ? 'bg-success' : ($pct > 100 ? 'bg-danger' : 'bg-primary') }}"
                        style="width: {{ min(100, $pct) }}%"></div>
                </div>
                @if (!$isBalanced)
                    <p class="small text-muted mb-0">
                        <i class="fa-solid fa-circle-info"></i>
                        ${{ number_format($remaining, 2) }} remaining to allocate before both parties can agree.
                    </p>
                @else
                    <p class="small text-success mb-0">
                        <i class="fa-solid fa-check"></i> Milestones cover the full contract amount.
                    </p>
                @endif
            </div>

            {{-- Agreement status --}}
            <div class="row g-2 mb-4">
                <div class="col-md-6">
                    <div class="border rounded p-2 d-flex justify-content-between align-items-center">
                        <span class="small">Client ({{ $task->ownerUser->name ?? 'Client' }})</span>
                        @if ($task->milestones_agreed_client_at)
                            <span class="badge bg-success"><i class="fa-solid fa-check"></i> Agreed</span>
                        @else
                            <span class="badge bg-secondary">Pending</span>
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="border rounded p-2 d-flex justify-content-between align-items-center">
                        <span class="small">Worker ({{ $task->user->name }})</span>
                        @if ($task->milestones_agreed_worker_at)
                            <span class="badge bg-success"><i class="fa-solid fa-check"></i> Agreed</span>
                        @else
                            <span class="badge bg-secondary">Pending</span>
                        @endif
                    </div>
                </div>
            </div>

            @if ($otherAgreed && !$userAgreed)
                <div class="alert alert-info py-2 small">
                    <i class="fa-solid fa-bell"></i>
                    The other party agreed to this plan. Review the milestones below and approve if everything looks good.
                </div>
            @endif

            {{-- Proposed milestones list --}}
            @forelse ($task->milestones as $milestone)
                <div class="border rounded p-3 mb-3 bg-light">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                        <div>
                            <strong>{{ $milestone->title }}</strong>
                            <span class="badge bg-secondary ms-1">${{ number_format($milestone->amount, 2) }}</span>
                            @if ($milestone->due_date)
                                <span class="badge bg-light text-dark border ms-1">Due {{ $milestone->due_date->format('M d, Y') }}</span>
                            @endif
                        </div>
                        <div class="text-end">
                            <span class="badge bg-warning text-dark">Proposed</span>
                            @if ($milestone->proposer)
                                <small class="text-muted d-block">by {{ $milestone->proposer->name }}</small>
                            @endif
                        </div>
                    </div>
                    @if ($milestone->description)
                        <p class="small mb-2 mt-2">{{ $milestone->description }}</p>
                    @endif

                    @if ($milestone->canEdit(Auth::id()))
                        <button class="btn btn-sm btn-outline-primary" type="button"
                            data-bs-toggle="collapse" data-bs-target="#editMs{{ $milestone->id }}">
                            <i class="fa-solid fa-pencil"></i> Edit
                        </button>
                        <form method="POST" action="/milestones/{{ $milestone->id }}" class="d-inline"
                            onsubmit="return confirm('Remove this milestone?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                        </form>
                        <div class="collapse mt-2" id="editMs{{ $milestone->id }}">
                            <form method="POST" action="/milestones/{{ $milestone->id }}">
                                @csrf
                                @method('PUT')
                                <div class="row g-2">
                                    <div class="col-md-5">
                                        <input type="text" class="form-control form-control-sm" name="title"
                                            value="{{ $milestone->title }}" required>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="number" class="form-control form-control-sm" name="amount"
                                            value="{{ $milestone->amount }}" min="1" step="0.01" required>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="date" class="form-control form-control-sm" name="due_date"
                                            value="{{ $milestone->due_date?->format('Y-m-d') }}">
                                    </div>
                                </div>
                                <textarea class="form-control form-control-sm mt-2" name="description" rows="2">{{ $milestone->description }}</textarea>
                                <button class="btn btn-sm btn-primary mt-2">Save changes</button>
                            </form>
                        </div>
                    @endif
                </div>
            @empty
                <p class="text-muted">No milestones proposed yet. Either party can add the first one below.</p>
            @endforelse

            {{-- Add milestone form --}}
            @if ($task->canAddMilestones(Auth::id()) && $remaining >= 1)
                <form method="POST" action="/tasks/{{ $task->id }}/milestones" class="border-top pt-3 mt-3">
                    @csrf
                    <h6 class="fw-semibold mb-3"><i class="fa-solid fa-plus"></i> Propose a milestone</h6>
                    <div class="row g-2">
                        <div class="col-md-5 mb-2">
                            <input type="text" class="form-control" name="title" placeholder="Milestone title" required
                                value="{{ old('title') }}">
                        </div>
                        <div class="col-md-3 mb-2">
                            <input type="number" class="form-control" name="amount" placeholder="Amount $" min="1"
                                max="{{ $remaining }}" step="0.01" required value="{{ old('amount') }}">
                        </div>
                        <div class="col-md-4 mb-2">
                            <input type="date" class="form-control" name="due_date" value="{{ old('due_date') }}">
                        </div>
                    </div>
                    <textarea class="form-control mb-2" name="description" rows="2"
                        placeholder="What will be delivered in this phase? (optional)">{{ old('description') }}</textarea>
                    @error('amount')<p class="text-danger small">{{ $message }}</p>@enderror
                    @error('milestone')<p class="text-danger small">{{ $message }}</p>@enderror
                    <button class="btn btn-primary btn-sm">Add milestone</button>
                </form>
            @endif

            {{-- Agree / withdraw --}}
            <div class="border-top pt-3 mt-3 d-flex flex-wrap gap-2">
                @if ($task->canAgreeToMilestonePlan(Auth::id()))
                    <form method="POST" action="/tasks/{{ $task->id }}/milestones/agree">
                        @csrf
                        <button class="btn btn-success">
                            <i class="fa-solid fa-handshake"></i> I agree to this milestone plan
                        </button>
                    </form>
                @elseif ($userAgreed)
                    <span class="badge bg-success align-self-center py-2 px-3">
                        <i class="fa-solid fa-check"></i> You agreed — waiting for the other party
                    </span>
                    <form method="POST" action="/tasks/{{ $task->id }}/milestones/withdraw-agreement">
                        @csrf
                        <button class="btn btn-sm btn-outline-secondary">Withdraw approval</button>
                    </form>
                @elseif ($task->milestones->isNotEmpty() && !$isBalanced)
                    <p class="text-muted small mb-0 align-self-center">
                        Allocate the full ${{ number_format($budgetTotal, 2) }} before agreeing.
                    </p>
                @endif
            </div>
        @endif

        {{-- Active milestone workflow --}}
        @if ($planActive)
            <div class="mb-3">
                <div class="d-flex justify-content-between small mb-1">
                    <span>Paid: ${{ number_format($task->totalPaid(), 2) }}</span>
                    <span>{{ $task->milestones->where('status', 'paid')->count() }}/{{ $task->milestones->count() }} milestones paid</span>
                </div>
                <div class="progress" style="height: 8px;">
                    @php $paidPct = $task->milestones->count() ? ($task->milestones->where('status', 'paid')->count() / $task->milestones->count()) * 100 : 0; @endphp
                    <div class="progress-bar bg-success" style="width: {{ $paidPct }}%"></div>
                </div>
            </div>

            @foreach ($task->milestones as $loopIndex => $milestone)
                @php
                    $statusColors = [
                        'pending' => 'secondary',
                        'submitted' => 'info',
                        'approved' => 'warning',
                        'paid' => 'success',
                    ];
                    $statusColor = $statusColors[$milestone->status] ?? 'secondary';
                @endphp
                <div class="border rounded p-3 mb-3 {{ $milestone->status === 'paid' ? 'opacity-75' : '' }}">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                        <div>
                            <span class="text-muted small me-2">#{{ $loopIndex + 1 }}</span>
                            <strong>{{ $milestone->title }}</strong>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-dark">${{ number_format($milestone->amount, 2) }}</span>
                            <span class="badge bg-{{ $statusColor }}">{{ ucfirst($milestone->status) }}</span>
                        </div>
                    </div>
                    @if ($milestone->description)
                        <p class="small text-muted mb-2 mt-1">{{ $milestone->description }}</p>
                    @endif
                    @if ($milestone->due_date)
                        <p class="small mb-2"><i class="fa-regular fa-calendar"></i> Due {{ $milestone->due_date->format('M d, Y') }}</p>
                    @endif
                    @if ($milestone->delivery_notes)
                        <div class="bg-light rounded p-2 small mb-2">
                            <strong>Delivery:</strong> {{ $milestone->delivery_notes }}
                        </div>
                    @endif

                    {{-- Worker: submit --}}
                    @if ($milestone->canSubmit(Auth::id()))
                        <form method="POST" action="/milestones/{{ $milestone->id }}/submit" class="mt-2">
                            @csrf
                            <label class="form-label small fw-semibold">Submit work for this milestone</label>
                            <textarea class="form-control mb-2" name="delivery_notes" rows="3"
                                placeholder="Describe what you delivered, include links or notes..." required></textarea>
                            <button class="btn btn-sm btn-primary"><i class="fa-solid fa-paper-plane"></i> Submit milestone</button>
                        </form>
                    @endif

                    {{-- Client: approve / revision / pay --}}
                    @if ($milestone->canApprove(Auth::id()))
                        <div class="mt-2 d-flex flex-wrap gap-2 align-items-start">
                            <form method="POST" action="/milestones/{{ $milestone->id }}/approve" class="d-inline">
                                @csrf
                                <button class="btn btn-sm btn-success"><i class="fa-solid fa-check"></i> Approve</button>
                            </form>
                            <button class="btn btn-sm btn-warning" type="button" data-bs-toggle="collapse"
                                data-bs-target="#msRev{{ $milestone->id }}">
                                <i class="fa-solid fa-rotate-left"></i> Request revision
                            </button>
                        </div>
                        <div class="collapse mt-2" id="msRev{{ $milestone->id }}">
                            <form method="POST" action="/milestones/{{ $milestone->id }}/revision">
                                @csrf
                                <textarea class="form-control mb-2" name="revision_notes" rows="2"
                                    placeholder="What needs to be changed?" required></textarea>
                                <button class="btn btn-sm btn-warning">Send revision request</button>
                            </form>
                        </div>
                    @endif

                    @if ($isClient && $milestone->canPay())
                        <form action="/session" method="POST" class="mt-2">
                            @csrf
                            <input type="hidden" name="task" value="{{ $task->id }}">
                            <input type="hidden" name="milestone" value="{{ $milestone->id }}">
                            <button class="btn btn-sm btn-success" type="submit">
                                <i class="fa fa-money"></i> Pay ${{ number_format($milestone->amount, 2) }}
                            </button>
                        </form>
                    @endif

                    @if ($milestone->status === 'paid')
                        <p class="text-success small mb-0 mt-2"><i class="fa-solid fa-circle-check"></i> Paid</p>
                    @endif
                </div>
            @endforeach
        @endif

        {{-- Legacy: milestones added before negotiation flow (client-only, already pending) --}}
        @if (!$negotiating && !$planActive && $task->milestones->isNotEmpty())
            @foreach ($task->milestones as $milestone)
                <div class="border rounded p-3 mb-3">
                    <div class="d-flex justify-content-between">
                        <strong>{{ $milestone->title }}</strong>
                        <span class="badge bg-secondary">${{ $milestone->amount }} — {{ ucfirst($milestone->status) }}</span>
                    </div>
                    @if ($milestone->description)<p class="mb-1">{{ $milestone->description }}</p>@endif
                    @if ($milestone->delivery_notes)<p class="mb-1"><em>{{ $milestone->delivery_notes }}</em></p>@endif
                    @if ($milestone->canSubmit(Auth::id()))
                        <form method="POST" action="/milestones/{{ $milestone->id }}/submit" class="mt-2">
                            @csrf
                            <textarea class="form-control mb-2" name="delivery_notes" rows="2" placeholder="Delivery notes..." required></textarea>
                            <button class="btn btn-sm btn-primary">Submit Milestone</button>
                        </form>
                    @endif
                    @if ($milestone->canApprove(Auth::id()))
                        <form method="POST" action="/milestones/{{ $milestone->id }}/approve" class="mt-2 d-inline">
                            @csrf
                            <button class="btn btn-sm btn-success">Approve</button>
                        </form>
                    @endif
                    @if ($isClient && $milestone->canPay())
                        <form action="/session" method="POST" class="mt-2 d-inline">
                            @csrf
                            <input type="hidden" name="task" value="{{ $task->id }}">
                            <input type="hidden" name="milestone" value="{{ $milestone->id }}">
                            <button class="btn btn-sm btn-success" type="submit">Pay ${{ $milestone->amount }}</button>
                        </form>
                    @endif
                </div>
            @endforeach
        @endif

        @if (!$negotiating && !$planActive && $task->milestones->isEmpty())
            <p class="text-muted mb-0 small">
                Default: pay the full amount when work is complete. Either party can switch to milestone payments above.
            </p>
        @endif
    </div>
</div>
