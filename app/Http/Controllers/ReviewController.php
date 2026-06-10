<?php

namespace App\Http\Controllers;

use App\Models\Tasks;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\NotificationService;

class ReviewController extends Controller
{
    public function pending($id)
    {
        if ((int) $id !== Auth::id()) {
            abort(403);
        }

        $tasks = Tasks::where(function ($q) {
            $q->where('user_id', Auth::id())->orWhere('owner', Auth::id());
        })
            ->with(['gig', 'user', 'ownerUser', 'payments'])
            ->latest()
            ->get()
            ->filter(fn ($t) => $t->canReview(Auth::id()));

        return view('reviews.pending', ['tasks' => $tasks]);
    }

    public function store(Request $request, Tasks $task)
    {
        if (!$task->canReview(Auth::id())) {
            return back()->withErrors(['review' => 'You cannot leave a review for this contract yet.']);
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:2000',
        ]);

        $revieweeId = $task->revieweeFor(Auth::id());
        $type = $task->reviewTypeFor(Auth::id());

        $existing = Review::where('task_id', $task->id)
            ->where('reviewer_id', Auth::id())
            ->first();

        if ($existing) {
            $existing->update([
                'reviewee_id' => $revieweeId,
                'rating' => $validated['rating'],
                'comment' => $validated['comment'] ?? null,
                'type' => $type,
                'status' => Review::STATUS_PENDING,
            ]);
        } else {
            Review::create([
                'task_id' => $task->id,
                'reviewer_id' => Auth::id(),
                'reviewee_id' => $revieweeId,
                'rating' => $validated['rating'],
                'comment' => $validated['comment'] ?? null,
                'type' => $type,
                'status' => Review::STATUS_PENDING,
            ]);
        }

        $reviewee = \App\Models\User::find($revieweeId);
        NotificationService::send(
            $reviewee,
            'New review received',
            Auth::user()->name . ' left you a review. It is pending admin approval.',
            '/tasks/' . $task->id,
            'review'
        );

        $admins = \App\Models\User::where('is_admin', true)->get();
        foreach ($admins as $admin) {
            NotificationService::send(
                $admin,
                'Review pending moderation',
                Auth::user()->name . ' submitted a review on contract #' . $task->id . '.',
                '/admin/reviews',
                'review'
            );
        }

        return back()->with('message', 'Review submitted! It will appear publicly after admin approval.');
    }
}
