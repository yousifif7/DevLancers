<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Tasks;
use App\Models\Dispute;
use App\Models\Gigs;
use App\Models\Proposal;
use App\Models\Payment;
use App\Models\Review;
use App\Models\SupportTicket;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use App\Services\NotificationService;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard', [
            'userCount' => User::count(),
            'workerCount' => User::where('acc_type', 1)->count(),
            'clientCount' => User::where('acc_type', 2)->count(),
            'gigCount' => Gigs::count(),
            'jobCount' => Gigs::where('gig_type', 'job')->count(),
            'contractCount' => Tasks::count(),
            'activeContracts' => Tasks::whereIn('status', ['active', 'in_review'])->count(),
            'proposalCount' => Proposal::count(),
            'paymentTotal' => Payment::where('status', 'completed')->sum('amount'),
            'reviewCount' => Review::count(),
            'pendingReviews' => Review::pending()->count(),
            'openDisputes' => Dispute::where('status', Dispute::STATUS_OPEN)->count(),
            'openTickets' => SupportTicket::where('status', SupportTicket::STATUS_OPEN)->count(),
            'recentUsers' => User::latest()->take(5)->get(),
            'recentContracts' => Tasks::with(['user', 'ownerUser'])->latest()->take(5)->get(),
        ]);
    }

    public function users()
    {
        $users = User::latest()->paginate(20);

        return view('admin.users', compact('users'));
    }

    public function gigs()
    {
        $gigs = Gigs::with('user')->latest()->paginate(20);

        return view('admin.gigs', compact('gigs'));
    }

    public function disputes(Request $request)
    {
        $status = $request->query('status');

        $disputes = Dispute::with(['task.gig', 'task.user', 'task.ownerUser', 'opener'])
            ->when($status === 'open', fn ($q) => $q->where('status', Dispute::STATUS_OPEN))
            ->when($status === 'resolved', fn ($q) => $q->where('status', Dispute::STATUS_RESOLVED))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.disputes', [
            'disputes' => $disputes,
            'status' => $status,
            'openCount' => Dispute::where('status', Dispute::STATUS_OPEN)->count(),
            'resolvedCount' => Dispute::where('status', Dispute::STATUS_RESOLVED)->count(),
        ]);
    }

    public function support()
    {
        $tickets = SupportTicket::with('user')->latest()->paginate(20);

        return view('admin.support', compact('tickets'));
    }

    public function replyTicket(Request $request, SupportTicket $ticket)
    {
        $request->validate([
            'admin_reply' => 'required|string|min:10|max:5000',
            'status' => 'required|in:replied,closed',
        ]);

        $ticket->update([
            'admin_reply' => $request->admin_reply,
            'status' => $request->status,
        ]);

        if ($ticket->user_id) {
            NotificationService::send(
                User::find($ticket->user_id),
                'Support ticket update',
                'Reply to: ' . $ticket->subject,
                '/support',
                'support'
            );
        }

        return back()->with('message', 'Ticket updated.');
    }

    public function settings()
    {
        $settings = SiteSetting::pluck('value', 'key');

        return view('admin.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $keys = [
            'site_name', 'site_tagline', 'support_email',
            'allow_registration', 'maintenance_mode',
            'hero_title', 'hero_subtitle',
        ];

        foreach ($keys as $key) {
            if ($request->has($key)) {
                SiteSetting::set($key, $request->input($key));
            }
        }

        return back()->with('message', 'Settings saved successfully.');
    }

    public function resolveDispute(Request $request, Dispute $dispute)
    {
        $request->validate([
            'resolution_notes' => 'required|string|min:10|max:5000',
            'contract_status' => 'required|in:active,cancelled,completed',
        ]);

        $dispute->update([
            'status' => Dispute::STATUS_RESOLVED,
            'resolution_notes' => $request->resolution_notes,
        ]);

        $dispute->task->update(['status' => $request->contract_status]);

        $task = $dispute->task;
        foreach ([$task->user_id, $task->owner] as $userId) {
            NotificationService::send(
                User::find($userId),
                'Dispute resolved',
                'Dispute on contract #' . $task->id . ' has been resolved by admin.',
                '/tasks/' . $task->id,
                'dispute'
            );
        }

        return back()->with('message', 'Dispute resolved.');
    }

    public function reviews(Request $request)
    {
        $status = $request->query('status');

        $reviews = Review::with(['reviewer', 'reviewee', 'task.gig'])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.reviews', [
            'reviews' => $reviews,
            'status' => $status,
            'pendingCount' => Review::pending()->count(),
            'approvedCount' => Review::approved()->count(),
            'deniedCount' => Review::where('status', Review::STATUS_DENIED)->count(),
        ]);
    }

    public function approveReview(Review $review)
    {
        $review->update(['status' => Review::STATUS_APPROVED]);

        $this->notifyReviewModerated($review, 'approved');

        return back()->with('message', 'Review approved.');
    }

    public function denyReview(Review $review)
    {
        $review->update(['status' => Review::STATUS_DENIED]);

        $this->notifyReviewModerated($review, 'denied');

        return back()->with('message', 'Review denied.');
    }

    public function updateReview(Request $request, Review $review)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:2000',
            'status' => 'required|in:pending,approved,denied',
        ]);

        $review->update($validated);

        return back()->with('message', 'Review updated.');
    }

    public function destroyReview(Review $review)
    {
        $review->delete();

        return back()->with('message', 'Review deleted.');
    }

    private function notifyReviewModerated(Review $review, string $action): void
    {
        NotificationService::send(
            User::find($review->reviewer_id),
            'Review ' . $action,
            'Your review on contract #' . $review->task_id . ' was ' . $action . ' by an admin.',
            '/tasks/' . $review->task_id,
            'review'
        );

        if ($action === 'approved') {
            NotificationService::send(
                User::find($review->reviewee_id),
                'Review published',
                'A review from ' . $review->reviewer->name . ' is now approved on contract #' . $review->task_id . '.',
                '/tasks/' . $review->task_id,
                'review'
            );

            $task = $review->task()->with('reviews')->first();
            if ($task && $task->bothReviewsApproved()) {
                foreach ([$task->user_id, $task->owner] as $userId) {
                    NotificationService::send(
                        User::find($userId),
                        'Reviews are now visible',
                        'Both approved reviews are available on contract #' . $task->id . '.',
                        '/tasks/' . $task->id,
                        'review'
                    );
                }
            }
        }
    }
}
