<?php

namespace App\Http\Controllers;

use App\Models\Gigs;
use App\Models\GigMedia;
use App\Models\Proposal;
use App\Models\User;
use App\Models\SiteSetting;
use App\Services\GigMediaService;
use App\Services\GigStatsService;
use App\Services\UserStatsService;
use App\Support\SeoMeta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;

class GigController extends Controller
{
    public function index()
    {
        return view('gigs-folder/gigs', [
            'gigs' => Gigs::where('status', Gigs::STATUS_OPEN)
                ->with('user')
                ->latest()
                ->filter(request(['tag', 'search', 'type']))
                ->paginate(8),
            'heroTitle' => Schema::hasTable('site_settings') ? SiteSetting::get('hero_title') : 'Find the perfect freelancer for your project',
            'heroSubtitle' => Schema::hasTable('site_settings') ? SiteSetting::get('hero_subtitle') : 'Browse jobs and gigs, submit proposals, and collaborate securely.',
        ]);
    }

    public function show(Gigs $gig)
    {
        $gig->load(['user', 'proposals', 'media']);

        $userProposal = null;
        $messages = collect();

        if (Auth::check()) {
            $userProposals = $gig->proposals()->where('user_id', Auth::id());

            $userProposal = $gig->isGig()
                ? $userProposals->whereIn('status', [
                    Proposal::STATUS_PENDING,
                    Proposal::STATUS_SHORTLISTED,
                ])->latest()->first()
                : $userProposals->first();

            $messages = \App\Models\Requests::where('gig_id', $gig->id)
                ->where(function ($query) {
                    $query->where('user_id', Auth::id())
                        ->orWhere('reciever', Auth::id());
                })
                ->latest()
                ->get();
        }

        return view('gigs-folder/gig', [
            'gig' => $gig,
            'userProposal' => $userProposal,
            'messages' => $messages,
            'seoMeta' => SeoMeta::gig($gig),
            'proposalCount' => $gig->proposals()->count(),
            'listingStats' => GigStatsService::for($gig),
            'ownerStats' => UserStatsService::for($gig->user),
        ]);
    }

    public function create()
    {
        return view('gigs-folder/gigscreate');
    }

    public function store(Request $request)
    {
        $request->validate($this->listingRules());

        $gig = Gigs::create([
            'user_id' => $request->user_id,
            'gig_type' => $request->gig_type,
            'status' => Gigs::STATUS_OPEN,
            'title' => $request->title,
            'email' => $request->email,
            'description' => $request->description,
            'tag' => $request->tag,
            'salary' => $request->salary,
        ]);

        GigMediaService::syncFromRequest($gig, $request);

        $user = User::find(Auth::user()->id);

        if (Auth::user()->acc_type == 1) {
            return redirect()->route('profile', ['user' => $user])->with('message', 'Gig Posted succesfully');
        }

        return redirect()->route('profile', ['user' => $user])->with('message', 'Job Posted succesfully');
    }

    public function edit(Gigs $gig)
    {
        if ($gig->user_id != Auth::user()->id) {
            abort(404, 'Unauthorized action');
        }

        $gig->load('media');

        return view('gigs-folder/gigedit', ['gig' => $gig]);
    }

    public function update(Request $request, Gigs $gig)
    {
        if ($gig->user_id != Auth::user()->id) {
            abort(404, 'Unauthorized action');
        }

        $request->validate($this->listingRules(true));

        $gig->update([
            'title' => $request->title,
            'email' => $request->email,
            'description' => $request->description,
            'tag' => $request->tag,
            'salary' => $request->salary,
        ]);

        GigMediaService::syncFromRequest($gig, $request, true);

        if (substr(url()->previous(), -1) == '/profile') {
            return redirect('/gigs/profile')->with('message', 'Profile updated successfully!');
        }

        if (Auth::user()->acc_type == 1) {
            return redirect('gigs/' . $gig->id)->with('message', 'Gig updated successfully!');
        }

        return redirect('gigs/' . $gig->id)->with('message', 'Job updated successfully!');
    }

    public function destroy(Gigs $gig)
    {
        if ($gig->user_id != Auth::user()->id) {
            abort(404, 'Unauthorized action');
        }

        GigMediaService::deleteAllForGig($gig);
        $gig->delete();

        $user = User::find(Auth::user()->id);

        if ($user->acc_type == 1) {
            return redirect()->route('profile', ['user' => $user])->with('message', 'Gig Deleted succesfully');
        }

        return redirect()->route('profile', ['user' => $user])->with('message', 'Job Deleted succesfully');
    }

    public function destroyMedia(GigMedia $media)
    {
        if ($media->gig->user_id != Auth::id()) {
            abort(403, 'Unauthorized action');
        }

        GigMediaService::deleteMedia($media);

        return back()->with('message', 'File removed successfully.');
    }

    public function profile()
    {
        $gigs = Gigs::where('user_id', Auth::id())
            ->withCount('proposals')
            ->latest()
            ->get();

        return view('registeration.profile', [
            'gigs' => $gigs,
            'stats' => UserStatsService::for(Auth::user()),
        ]);
    }

    private function listingRules(bool $isUpdate = false): array
    {
        $rules = [
            'title' => 'required',
            'email' => ['required', 'email'],
            'description' => 'required',
            'salary' => 'required|numeric|min:5|max:1000',
            'tag' => '',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'images' => 'nullable|array|max:6',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:5120',
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'file|mimes:pdf,doc,docx,zip,txt,png,jpg,jpeg|max:10240',
        ];

        if (!$isUpdate) {
            $rules['user_id'] = 'required';
            $rules['gig_type'] = 'required';
        }

        return $rules;
    }
}
