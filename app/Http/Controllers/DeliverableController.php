<?php

namespace App\Http\Controllers;

use App\Models\Tasks;
use App\Models\Deliverable;
use App\Services\PublicUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\NotificationService;

class DeliverableController extends Controller
{
    public function store(Request $request, Tasks $task)
    {
        if (!$task->canSubmitDeliverable(Auth::id())) {
            return back()->withErrors(['deliverable' => 'You cannot submit a deliverable for this contract.']);
        }

        $validated = $request->validate([
            'notes' => 'required|string|min:10|max:5000',
            'link' => 'nullable|url|max:500',
            'file' => 'nullable|file|mimes:pdf,doc,docx,zip,png,jpg,jpeg|max:10240',
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = PublicUploadService::store($request->file('file'), 'deliverables');
        }

        Deliverable::create([
            'task_id' => $task->id,
            'user_id' => Auth::id(),
            'notes' => $validated['notes'],
            'link' => $validated['link'] ?? null,
            'file_path' => $filePath,
            'status' => Deliverable::STATUS_SUBMITTED,
        ]);

        $task->update(['status' => Tasks::STATUS_IN_REVIEW]);

        NotificationService::send(
            $task->ownerUser,
            'Deliverable submitted',
            $task->user->name . ' submitted work for contract #' . $task->id,
            '/tasks/' . $task->id,
            'deliverable'
        );

        return back()->with('message', 'Deliverable submitted for client review.');
    }

    public function approve(Deliverable $deliverable)
    {
        $task = $deliverable->task;

        if (!$task->canApproveDeliverable(Auth::id())) {
            abort(403, 'Unauthorized action');
        }

        if ($deliverable->id !== $task->latestDeliverable?->id) {
            return back()->withErrors(['deliverable' => 'Only the latest submission can be approved.']);
        }

        $deliverable->update(['status' => Deliverable::STATUS_APPROVED]);
        $task->update(['status' => Tasks::STATUS_COMPLETED]);

        NotificationService::send(
            $task->user,
            'Deliverable approved',
            'Your work on contract #' . $task->id . ' was approved.',
            '/tasks/' . $task->id,
            'deliverable'
        );

        return back()->with('message', 'Deliverable approved. You can now proceed to payment.');
    }

    public function requestRevision(Request $request, Deliverable $deliverable)
    {
        $task = $deliverable->task;

        if (!$task->canRequestRevision(Auth::id())) {
            abort(403, 'Unauthorized action');
        }

        if ($deliverable->id !== $task->latestDeliverable?->id) {
            return back()->withErrors(['deliverable' => 'Only the latest submission can be revised.']);
        }

        $request->validate([
            'revision_notes' => 'required|string|min:10|max:2000',
        ]);

        $deliverable->update([
            'status' => Deliverable::STATUS_REVISION_REQUESTED,
            'revision_notes' => $request->revision_notes,
        ]);

        $task->update(['status' => Tasks::STATUS_ACTIVE]);

        NotificationService::send(
            $task->user,
            'Revision requested',
            'The client requested changes on contract #' . $task->id,
            '/tasks/' . $task->id,
            'deliverable'
        );

        return back()->with('message', 'Revision requested. The worker has been notified.');
    }
}
