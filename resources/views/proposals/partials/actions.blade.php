@if ($proposal->isActionable() && $gig->isOpen())
    <div class="d-flex gap-1 flex-wrap">
        @if ($proposal->status === 'pending')
            <form method="POST" action="/proposals/{{ $proposal->id }}/status">
                @csrf
                <input type="hidden" name="status" value="shortlisted">
                <button class="btn btn-sm btn-info" title="Shortlist">
                    <i class="fa-solid fa-star"></i>
                </button>
            </form>
        @endif
        <form method="POST" action="/proposals/{{ $proposal->id }}/hire">
            @csrf
            <button class="btn btn-sm btn-success" title="{{ $gig->isGig() ? 'Accept order' : 'Hire' }}">
                <i class="fa-solid fa-check"></i> {{ $gig->isGig() ? 'Accept' : 'Hire' }}
            </button>
        </form>
        <form method="POST" action="/proposals/{{ $proposal->id }}/status">
            @csrf
            <input type="hidden" name="status" value="rejected">
            <button class="btn btn-sm btn-danger" title="Reject">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </form>
    </div>
@elseif ($proposal->task)
    <span class="badge bg-success">Hired</span>
@else
    —
@endif
