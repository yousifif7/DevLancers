<?php

namespace App\Services;

use App\Models\Tasks;
use App\Models\Review;
use App\Models\Payment;
use App\Models\Milestone;
use App\Models\Deliverable;
use Illuminate\Support\Collection;

class ContractHistoryService
{
    public static function for(Tasks $task): Collection
    {
        $events = collect();

        $events->push([
            'at' => $task->created_at,
            'icon' => 'file-signature',
            'title' => 'Contract created',
            'user' => null,
            'body' => 'Contract #' . $task->id . ' was created for $' . number_format((float) $task->price, 2) . '.',
        ]);

        if ($task->worker_accepted_at) {
            $events->push([
                'at' => $task->worker_accepted_at,
                'icon' => 'handshake',
                'title' => 'Worker accepted contract',
                'user' => $task->user->name ?? null,
                'body' => null,
            ]);
        }

        if ($task->client_accepted_at) {
            $events->push([
                'at' => $task->client_accepted_at,
                'icon' => 'handshake',
                'title' => 'Client accepted contract',
                'user' => $task->ownerUser->name ?? null,
                'body' => null,
            ]);
        }

        if ($task->bothPartiesAccepted() && $task->worker_accepted_at && $task->client_accepted_at) {
            $activatedAt = $task->worker_accepted_at->greaterThan($task->client_accepted_at)
                ? $task->worker_accepted_at
                : $task->client_accepted_at;

            $events->push([
                'at' => $activatedAt,
                'icon' => 'play',
                'title' => 'Contract activated',
                'user' => null,
                'body' => 'Both parties accepted — work could begin.',
            ]);
        }

        if ($task->milestones_agreed_client_at) {
            $events->push([
                'at' => $task->milestones_agreed_client_at,
                'icon' => 'list-check',
                'title' => 'Client agreed to milestone plan',
                'user' => $task->ownerUser->name ?? null,
                'body' => null,
            ]);
        }

        if ($task->milestones_agreed_worker_at) {
            $events->push([
                'at' => $task->milestones_agreed_worker_at,
                'icon' => 'list-check',
                'title' => 'Worker agreed to milestone plan',
                'user' => $task->user->name ?? null,
                'body' => null,
            ]);
        }

        foreach ($task->milestones as $milestone) {
            $events->push([
                'at' => $milestone->created_at,
                'icon' => 'flag',
                'title' => 'Milestone added: ' . $milestone->title,
                'user' => $milestone->proposer?->name,
                'body' => '$' . number_format((float) $milestone->amount, 2)
                    . ($milestone->due_date ? ' · Due ' . $milestone->due_date->format('M d, Y') : ''),
            ]);

            if (in_array($milestone->status, [
                Milestone::STATUS_SUBMITTED,
                Milestone::STATUS_APPROVED,
                Milestone::STATUS_PAID,
            ], true)) {
                $events->push([
                    'at' => $milestone->updated_at,
                    'icon' => 'paper-plane',
                    'title' => 'Milestone submitted: ' . $milestone->title,
                    'user' => $task->user->name ?? null,
                    'body' => $milestone->delivery_notes
                        ? \Illuminate\Support\Str::limit($milestone->delivery_notes, 120)
                        : null,
                ]);
            }

            if (in_array($milestone->status, [Milestone::STATUS_APPROVED, Milestone::STATUS_PAID], true)) {
                $events->push([
                    'at' => $milestone->updated_at,
                    'icon' => 'check',
                    'title' => 'Milestone approved: ' . $milestone->title,
                    'user' => $task->ownerUser->name ?? null,
                    'body' => null,
                ]);
            }

            if ($milestone->status === Milestone::STATUS_PAID) {
                $events->push([
                    'at' => $milestone->updated_at,
                    'icon' => 'money-bill',
                    'title' => 'Milestone paid: ' . $milestone->title,
                    'user' => $task->ownerUser->name ?? null,
                    'body' => '$' . number_format((float) $milestone->amount, 2),
                ]);
            }
        }

        foreach ($task->deliverables as $deliverable) {
            $events->push([
                'at' => $deliverable->created_at,
                'icon' => 'box',
                'title' => 'Deliverable submitted',
                'user' => $deliverable->user->name ?? null,
                'body' => \Illuminate\Support\Str::limit($deliverable->notes, 120),
            ]);

            if ($deliverable->status === Deliverable::STATUS_REVISION_REQUESTED) {
                $events->push([
                    'at' => $deliverable->updated_at,
                    'icon' => 'rotate-left',
                    'title' => 'Revision requested',
                    'user' => $task->ownerUser->name ?? null,
                    'body' => $deliverable->revision_notes
                        ? \Illuminate\Support\Str::limit($deliverable->revision_notes, 120)
                        : null,
                ]);
            }

            if ($deliverable->status === Deliverable::STATUS_APPROVED) {
                $events->push([
                    'at' => $deliverable->updated_at,
                    'icon' => 'check-double',
                    'title' => 'Deliverable approved',
                    'user' => $task->ownerUser->name ?? null,
                    'body' => null,
                ]);
            }
        }

        foreach ($task->payments->where('status', Payment::STATUS_COMPLETED) as $payment) {
            $events->push([
                'at' => $payment->paid_at ?? $payment->updated_at,
                'icon' => 'credit-card',
                'title' => $payment->milestone_id
                    ? 'Milestone payment completed'
                    : 'Contract payment completed',
                'user' => $task->ownerUser->name ?? null,
                'body' => '$' . number_format((float) $payment->amount, 2),
            ]);
        }

        if ($task->dispute) {
            $events->push([
                'at' => $task->dispute->created_at,
                'icon' => 'gavel',
                'title' => 'Dispute opened',
                'user' => null,
                'body' => \Illuminate\Support\Str::limit($task->dispute->reason, 120),
            ]);
        }

        foreach ($task->reviews as $review) {
            $body = match ($review->status) {
                Review::STATUS_PENDING => 'Submitted — pending admin approval',
                Review::STATUS_DENIED => 'Denied by admin',
                default => $task->bothReviewsApproved() && $review->isApproved()
                    ? $review->rating . ' stars' . ($review->comment ? ' — ' . \Illuminate\Support\Str::limit($review->comment, 80) : '')
                    : 'Approved (visible when both parties\' reviews are approved)',
            };

            $events->push([
                'at' => $review->created_at,
                'icon' => 'star',
                'title' => 'Review submitted',
                'user' => $review->reviewer->name ?? null,
                'body' => $body,
            ]);
        }

        if ($task->status === Tasks::STATUS_CANCELLED) {
            $events->push([
                'at' => $task->updated_at,
                'icon' => 'ban',
                'title' => 'Contract cancelled',
                'user' => null,
                'body' => null,
            ]);
        }

        if ($task->status === Tasks::STATUS_COMPLETED) {
            $events->push([
                'at' => $task->updated_at,
                'icon' => 'circle-check',
                'title' => 'Contract completed',
                'user' => null,
                'body' => 'All payments received.',
            ]);
        }

        return $events
            ->sortByDesc(fn ($e) => $e['at'])
            ->values();
    }
}
