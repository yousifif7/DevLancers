<?php

namespace App\Http\Controllers;

use App\Models\Gigs;
use App\Models\Tasks;
use App\Models\Proposal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\UserStatsService;
use App\Services\GigStatsService;

class ProposalController extends Controller
{
    public function store(Request $request, Gigs $gig)
    {
        if ((int) $gig->user_id === Auth::id()) {
            abort(403, 'You cannot submit a proposal on your own listing.');
        }

        if (!$gig->isOpen()) {
            return back()->withErrors(['proposal' => 'This listing is no longer accepting proposals.']);
        }

        if ($gig->gig_type === 'job' && Auth::user()->acc_type != 1) {
            return back()->withErrors(['proposal' => 'Only workers can submit proposals on jobs.']);
        }

        if ($gig->gig_type === 'gig' && Auth::user()->acc_type != 2) {
            return back()->withErrors(['proposal' => 'Only clients can submit proposals on gigs.']);
        }

        $existingProposal = Proposal::where('gig_id', $gig->id)
            ->where('user_id', Auth::id());

        if ($gig->isJob() && $existingProposal->exists()) {
            return back()->withErrors(['proposal' => 'You have already submitted a proposal for this job.']);
        }

        if ($gig->isGig() && $existingProposal->whereIn('status', [
            Proposal::STATUS_PENDING,
            Proposal::STATUS_SHORTLISTED,
        ])->exists()) {
            return back()->withErrors(['proposal' => 'You already have a pending order for this gig.']);
        }

        $validated = $request->validate([
            'cover_letter' => 'required|string|min:20|max:5000',
            'bid_amount' => 'required|numeric|min:5|max:10000',
            'delivery_days' => 'required|integer|min:1|max:365',
        ]);

        Proposal::create([
            'gig_id' => $gig->id,
            'user_id' => Auth::id(),
            'cover_letter' => $validated['cover_letter'],
            'bid_amount' => $validated['bid_amount'],
            'delivery_days' => $validated['delivery_days'],
            'status' => Proposal::STATUS_PENDING,
        ]);

        NotificationService::send(
            User::find($gig->user_id),
            'New proposal received',
            Auth::user()->name . ' submitted a proposal on "' . $gig->title . '"',
            '/gigs/' . $gig->id . '/proposals',
            'proposal'
        );

        return back()->with('message', 'Proposal submitted successfully!');
    }

    public function inbox($id)
    {
        if ((int) $id !== Auth::id()) {
            abort(403, 'Unauthorized action');
        }

        $proposals = Proposal::whereHas('gig', function ($query) {
            $query->where('user_id', Auth::id());
        })
            ->with(['gig', 'user', 'task'])
            ->latest()
            ->get();

        $grouped = $proposals->groupBy('gig_id');

        $applicantStats = $proposals->mapWithKeys(
            fn ($p) => [$p->user_id => UserStatsService::for($p->user)]
        );

        $gigStats = $grouped->mapWithKeys(
            fn ($group, $gigId) => [$gigId => GigStatsService::for($group->first()->gig)]
        );

        return view('proposals.inbox', [
            'user' => Auth::user(),
            'proposals' => $proposals,
            'grouped' => $grouped,
            'applicantStats' => $applicantStats,
            'gigStats' => $gigStats,
        ]);
    }

    public function forGig(Gigs $gig)
    {
        if ((int) $gig->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action');
        }

        $gig->load('user');

        $proposals = $gig->proposals()
            ->with(['user', 'task'])
            ->latest()
            ->get();

        $applicantStats = $proposals->mapWithKeys(
            fn ($p) => [$p->user_id => UserStatsService::for($p->user)]
        );

        return view('proposals.gig-proposals', [
            'gig' => $gig,
            'proposals' => $proposals,
            'listingStats' => GigStatsService::for($gig),
            'ownerStats' => UserStatsService::for($gig->user),
            'applicantStats' => $applicantStats,
        ]);
    }

    public function myProposals($id)
    {
        if ((int) $id !== Auth::id()) {
            abort(403, 'Unauthorized action');
        }

        $proposals = Proposal::where('user_id', Auth::id())
            ->with(['gig', 'gig.user', 'task'])
            ->latest()
            ->get();

        return view('proposals.my-proposals', [
            'user' => Auth::user(),
            'proposals' => $proposals,
            'stats' => UserStatsService::for(Auth::user()),
        ]);
    }

    public function updateStatus(Request $request, Proposal $proposal)
    {
        $gig = $proposal->gig;

        if ((int) $gig->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action');
        }

        $request->validate([
            'status' => 'required|in:shortlisted,rejected',
        ]);

        if (!$proposal->isActionable()) {
            return back()->withErrors(['status' => 'This proposal can no longer be updated.']);
        }

        $proposal->update(['status' => $request->status]);

        $label = $request->status === Proposal::STATUS_SHORTLISTED ? 'shortlisted' : 'rejected';

        return back()->with('message', "Proposal {$label} successfully.");
    }

    public function withdraw(Proposal $proposal)
    {
        if ((int) $proposal->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action');
        }

        if (!$proposal->isActionable()) {
            return back()->withErrors(['status' => 'This proposal can no longer be withdrawn.']);
        }

        $proposal->update(['status' => Proposal::STATUS_WITHDRAWN]);

        return back()->with('message', 'Proposal withdrawn successfully.');
    }

    public function hire(Proposal $proposal)
    {
        $gig = $proposal->gig;

        if ((int) $gig->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action');
        }

        if (!$gig->isOpen()) {
            return back()->withErrors(['hire' => 'This listing is no longer open for hiring.']);
        }

        if (!$proposal->isActionable()) {
            return back()->withErrors(['hire' => 'This proposal cannot be accepted.']);
        }

        if ($proposal->task) {
            return back()->with('message', 'This proposal has already been hired.');
        }

        $task = null;

        DB::transaction(function () use ($proposal, $gig, &$task) {
            $workerId = $gig->gig_type === 'job' ? $proposal->user_id : $gig->user_id;
            $ownerId = $gig->gig_type === 'job' ? $gig->user_id : $proposal->user_id;

            $acceptance = $gig->gig_type === 'job'
                ? ['client_accepted_at' => now(), 'worker_accepted_at' => null]
                : ['worker_accepted_at' => now(), 'client_accepted_at' => null];

            $task = Tasks::create(array_merge([
                'user_id' => $workerId,
                'gig_id' => $gig->id,
                'proposal_id' => $proposal->id,
                'status' => Tasks::STATUS_DRAFT,
                'owner' => $ownerId,
                'content' => $proposal->cover_letter,
                'scope_of_work' => $proposal->cover_letter,
                'terms' => 'Payment upon client approval of deliverables. Delivery within '
                    . $proposal->delivery_days . ' days of contract activation.',
                'start_date' => now()->toDateString(),
                'end_date' => now()->addDays($proposal->delivery_days)->toDateString(),
                'price' => $proposal->bid_amount,
                'payment_flag' => 0,
            ], $acceptance));

            $proposal->update(['status' => Proposal::STATUS_ACCEPTED]);

            if ($gig->isJob()) {
                Proposal::where('gig_id', $gig->id)
                    ->where('id', '!=', $proposal->id)
                    ->whereIn('status', [Proposal::STATUS_PENDING, Proposal::STATUS_SHORTLISTED])
                    ->update(['status' => Proposal::STATUS_REJECTED]);

                $gig->update(['status' => Gigs::STATUS_FILLED]);
            }
        });

        $pendingUserId = $gig->isJob() ? $task->user_id : $task->owner;
        $notificationTitle = $gig->isGig() ? 'New order received' : 'You have been hired!';
        NotificationService::send(
            User::find($pendingUserId),
            $notificationTitle,
            'A new contract #' . $task->id . ' was created. Please review and accept.',
            '/tasks/' . $task->id,
            'contract'
        );

        $pendingParty = $gig->isJob() ? 'the worker' : 'the client';
        $successMessage = $gig->isGig()
            ? "Order accepted! Waiting for {$pendingParty} to accept before work begins."
            : "Contract created! Waiting for {$pendingParty} to accept before work begins.";

        return redirect('/tasks/' . $task->id)->with('message', $successMessage);
    }

    public function closeGig(Gigs $gig)
    {
        if ((int) $gig->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action');
        }

        if ($gig->status === Gigs::STATUS_FILLED) {
            return back()->withErrors(['status' => 'Cannot close a listing that has been filled.']);
        }

        $gig->update(['status' => Gigs::STATUS_CLOSED]);

        Proposal::where('gig_id', $gig->id)
            ->whereIn('status', [Proposal::STATUS_PENDING, Proposal::STATUS_SHORTLISTED])
            ->update(['status' => Proposal::STATUS_REJECTED]);

        return back()->with('message', 'Listing closed successfully.');
    }
}
