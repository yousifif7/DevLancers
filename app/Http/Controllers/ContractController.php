<?php

namespace App\Http\Controllers;

use App\Models\Tasks;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Services\NotificationService;
use App\Services\ContractHistoryService;

class ContractController extends Controller
{
    public function show(Tasks $task)
    {
        if (!$task->isParticipant(Auth::id())) {
            abort(403, 'Unauthorized action');
        }

        $task->load([
            'gig', 'user', 'ownerUser', 'proposal', 'deliverables.user',
            'latestDeliverable', 'milestones.proposer', 'reviews.reviewer', 'dispute',
            'payments',
        ]);

        return view('contracts.show', [
            'task' => $task,
            'isWorker' => $task->isWorker(Auth::id()),
            'isClient' => $task->isClient(Auth::id()),
            'history' => ContractHistoryService::for($task),
        ]);
    }

    public function accept(Tasks $task)
    {
        if (!$task->canAccept(Auth::id())) {
            return back()->withErrors(['contract' => 'You cannot accept this contract.']);
        }

        if ($task->isWorker(Auth::id())) {
            $task->update(['worker_accepted_at' => now()]);
        } else {
            $task->update(['client_accepted_at' => now()]);
        }

        $task->refresh();
        $task->activateIfReady();

        $otherUserId = $task->isWorker(Auth::id()) ? $task->owner : $task->user_id;
        NotificationService::send(
            User::find($otherUserId),
            'Contract accepted',
            Auth::user()->name . ' accepted contract #' . $task->id,
            '/tasks/' . $task->id,
            'contract'
        );

        return back()->with('message', 'Contract accepted. Work can begin once both parties have agreed.');
    }

    public function cancel(Tasks $task)
    {
        if (!$task->canCancel(Auth::id())) {
            abort(403, 'Unauthorized action');
        }

        $task->update(['status' => Tasks::STATUS_CANCELLED]);

        return redirect('/user/tasks/' . Auth::id())
            ->with('message', 'Contract cancelled successfully.');
    }
}
