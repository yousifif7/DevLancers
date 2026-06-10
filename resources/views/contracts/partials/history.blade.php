@forelse ($events as $event)
    <div class="dl-history-item d-flex gap-3 pb-3 mb-3 border-bottom">
        <div class="dl-history-icon flex-shrink-0">
            <span class="rounded-circle d-inline-flex align-items-center justify-content-center bg-light border"
                style="width:36px;height:36px">
                <i class="fa-solid fa-{{ $event['icon'] }} text-muted small"></i>
            </span>
        </div>
        <div class="flex-grow-1 min-w-0">
            <div class="d-flex justify-content-between align-items-start gap-2 flex-wrap">
                <strong class="small">{{ $event['title'] }}</strong>
                <span class="text-muted small text-nowrap">{{ $event['at']->diffForHumans() }}</span>
            </div>
            @if (!empty($event['user']))
                <span class="small text-primary">{{ $event['user'] }}</span>
            @endif
            @if (!empty($event['body']))
                <p class="small text-muted mb-0 mt-1">{{ $event['body'] }}</p>
            @endif
        </div>
    </div>
@empty
    <p class="text-muted small mb-0">No activity recorded yet.</p>
@endforelse
