<?php

namespace App\Http\Controllers;

use App\Models\Tasks;
use App\Models\Milestone;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Services\NotificationService;

class MilestoneController extends Controller
{
    public function enableMilestones(Tasks $task)
    {
        if (!$task->canManageMilestonePlan(Auth::id())) {
            return back()->withErrors(['milestone' => 'Cannot switch to milestones on this contract.']);
        }

        $task->update(['payment_structure' => Tasks::PAYMENT_MILESTONES]);

        return back()->with('message', 'Milestone payments enabled. Both parties can now propose milestones.');
    }

    public function switchToSingle(Tasks $task)
    {
        if (!$task->canManageMilestonePlan(Auth::id())) {
            return back()->withErrors(['milestone' => 'Cannot switch payment structure on this contract.']);
        }

        $task->milestones()->delete();
        $task->resetMilestoneAgreements();

        return back()->with('message', 'Switched to single payment on completion.');
    }

    public function store(Request $request, Tasks $task)
    {
        if (!$task->canAddMilestones(Auth::id())) {
            return back()->withErrors(['milestone' => 'Cannot add milestones to this contract.']);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'amount' => 'required|numeric|min:1',
            'due_date' => 'nullable|date',
        ]);

        $remaining = $task->milestoneBudgetRemaining();
        if ($validated['amount'] > $remaining + 0.001) {
            return back()->withErrors([
                'amount' => 'Amount exceeds remaining budget of $' . number_format($remaining, 2),
            ]);
        }

        $nextOrder = (int) $task->milestones()->max('sort_order') + 1;

        Milestone::create([
            'task_id' => $task->id,
            'proposed_by' => Auth::id(),
            'sort_order' => $nextOrder,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'amount' => $validated['amount'],
            'due_date' => $validated['due_date'] ?? null,
            'status' => Milestone::STATUS_PROPOSED,
        ]);

        $task->update(['payment_structure' => Tasks::PAYMENT_MILESTONES]);
        $task->resetMilestoneAgreements();

        $this->notifyOtherParty(
            $task,
            'Milestone proposed',
            Auth::user()->name . ' added a milestone: "' . $validated['title'] . '" ($' . $validated['amount'] . '). Review and agree to the plan.'
        );

        return back()->with('message', 'Milestone proposed. Both parties must agree before work begins.');
    }

    public function update(Request $request, Milestone $milestone)
    {
        if (!$milestone->canEdit(Auth::id())) {
            return back()->withErrors(['milestone' => 'Cannot edit this milestone.']);
        }

        $task = $milestone->task;

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'amount' => 'required|numeric|min:1',
            'due_date' => 'nullable|date',
        ]);

        $otherTotal = (float) $task->milestones()->where('id', '!=', $milestone->id)->sum('amount');
        if ($otherTotal + $validated['amount'] > (float) $task->price + 0.001) {
            return back()->withErrors([
                'amount' => 'Milestone amounts cannot exceed the contract total of $' . $task->price,
            ]);
        }

        $milestone->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'amount' => $validated['amount'],
            'due_date' => $validated['due_date'] ?? null,
            'proposed_by' => Auth::id(),
        ]);

        $task->resetMilestoneAgreements();

        $this->notifyOtherParty(
            $task,
            'Milestone updated',
            Auth::user()->name . ' updated milestone "' . $validated['title'] . '". Please review the plan again.'
        );

        return back()->with('message', 'Milestone updated.');
    }

    public function destroy(Milestone $milestone)
    {
        if (!$milestone->canEdit(Auth::id())) {
            return back()->withErrors(['milestone' => 'Cannot remove this milestone.']);
        }

        $task = $milestone->task;
        $title = $milestone->title;
        $milestone->delete();

        $task->resetMilestoneAgreements();

        if (!$task->milestones()->exists()) {
            $task->update(['payment_structure' => Tasks::PAYMENT_SINGLE]);
        }

        $this->notifyOtherParty(
            $task,
            'Milestone removed',
            Auth::user()->name . ' removed milestone "' . $title . '" from the plan.'
        );

        return back()->with('message', 'Milestone removed.');
    }

    public function agreePlan(Tasks $task)
    {
        if (!$task->canAgreeToMilestonePlan(Auth::id())) {
            return back()->withErrors(['milestone' => 'Cannot agree to this milestone plan yet. Ensure milestones total the full contract amount.']);
        }

        if ($task->isClient(Auth::id())) {
            $task->update(['milestones_agreed_client_at' => now()]);
        } else {
            $task->update(['milestones_agreed_worker_at' => now()]);
        }

        $task->refresh();

        if ($task->milestonePlanIsActive()) {
            $task->activateMilestonePlan();

            $this->notifyBothParties(
                $task,
                'Milestone plan activated',
                'Both parties agreed. The milestone payment plan is now active for contract #' . $task->id . '.'
            );

            return back()->with('message', 'Milestone plan is active! Work can proceed milestone by milestone.');
        }

        $this->notifyOtherParty(
            $task,
            'Milestone plan approval',
            Auth::user()->name . ' agreed to the milestone plan. Please review and approve to activate.'
        );

        return back()->with('message', 'You agreed to the milestone plan. Waiting for the other party.');
    }

    public function withdrawAgreement(Tasks $task)
    {
        if (!$task->canManageMilestonePlan(Auth::id())) {
            return back()->withErrors(['milestone' => 'Cannot withdraw agreement on this contract.']);
        }

        if ($task->isClient(Auth::id())) {
            $task->update(['milestones_agreed_client_at' => null]);
        } else {
            $task->update(['milestones_agreed_worker_at' => null]);
        }

        return back()->with('message', 'Your milestone plan approval was withdrawn.');
    }

    public function submit(Request $request, Milestone $milestone)
    {
        if (!$milestone->canSubmit(Auth::id())) {
            return back()->withErrors(['milestone' => 'Cannot submit this milestone.']);
        }

        $validated = $request->validate([
            'delivery_notes' => 'required|string|min:10|max:5000',
        ]);

        $milestone->update([
            'delivery_notes' => $validated['delivery_notes'],
            'status' => Milestone::STATUS_SUBMITTED,
        ]);

        $client = $milestone->task->ownerUser;
        NotificationService::send(
            $client,
            'Milestone submitted',
            Auth::user()->name . ' submitted milestone: ' . $milestone->title,
            '/tasks/' . $milestone->task_id,
            'milestone'
        );

        return back()->with('message', 'Milestone submitted for approval.');
    }

    public function approve(Milestone $milestone)
    {
        if (!$milestone->canApprove(Auth::id())) {
            abort(403, 'Unauthorized action');
        }

        $milestone->update(['status' => Milestone::STATUS_APPROVED]);

        $worker = $milestone->task->user;
        NotificationService::send(
            $worker,
            'Milestone approved',
            'Milestone "' . $milestone->title . '" was approved. Awaiting payment.',
            '/tasks/' . $milestone->task_id,
            'milestone'
        );

        return back()->with('message', 'Milestone approved. Client can now pay for it.');
    }

    public function requestRevision(Request $request, Milestone $milestone)
    {
        if (!$milestone->canApprove(Auth::id())) {
            abort(403, 'Unauthorized action');
        }

        $validated = $request->validate([
            'revision_notes' => 'required|string|min:10|max:5000',
        ]);

        $milestone->update([
            'status' => Milestone::STATUS_PENDING,
            'delivery_notes' => null,
        ]);

        $worker = $milestone->task->user;
        NotificationService::send(
            $worker,
            'Milestone revision requested',
            'Revision requested on "' . $milestone->title . '": ' . Str::limit($validated['revision_notes'], 120),
            '/tasks/' . $milestone->task_id,
            'milestone'
        );

        return back()->with('message', 'Revision request sent to the worker.');
    }

    private function notifyOtherParty(Tasks $task, string $title, string $body): void
    {
        $otherId = $task->isClient(Auth::id()) ? $task->user_id : $task->owner;
        NotificationService::send(
            User::find($otherId),
            $title,
            $body,
            '/tasks/' . $task->id,
            'milestone'
        );
    }

    private function notifyBothParties(Tasks $task, string $title, string $body): void
    {
        foreach ([$task->user_id, $task->owner] as $userId) {
            NotificationService::send(
                User::find($userId),
                $title,
                $body,
                '/tasks/' . $task->id,
                'milestone'
            );
        }
    }
}
