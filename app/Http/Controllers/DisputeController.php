<?php

namespace App\Http\Controllers;

use App\Models\Tasks;
use App\Models\Dispute;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\NotificationService;

class DisputeController extends Controller
{
    public function store(Request $request, Tasks $task)
    {
        if (!$task->canOpenDispute(Auth::id())) {
            return back()->withErrors(['dispute' => 'Cannot open a dispute on this contract.']);
        }

        $validated = $request->validate([
            'reason' => 'required|string|min:20|max:5000',
        ]);

        Dispute::create([
            'task_id' => $task->id,
            'opened_by' => Auth::id(),
            'reason' => $validated['reason'],
            'status' => Dispute::STATUS_OPEN,
        ]);

        $task->update(['status' => Tasks::STATUS_DISPUTED]);

        $otherUserId = $task->isWorker(Auth::id()) ? $task->owner : $task->user_id;
        NotificationService::send(
            User::find($otherUserId),
            'Dispute opened',
            'A dispute was opened on contract #' . $task->id,
            '/tasks/' . $task->id,
            'dispute'
        );

        foreach (User::where('is_admin', true)->get() as $admin) {
            NotificationService::send(
                $admin,
                'New dispute',
                'Dispute opened on contract #' . $task->id,
                '/admin/disputes',
                'dispute'
            );
        }

        return back()->with('message', 'Dispute opened. An admin will review it.');
    }
}
