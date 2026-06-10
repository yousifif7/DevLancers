<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\NotificationService;

class ChatController extends Controller
{
    public function index($id)
    {
        if ((int) $id !== Auth::id()) {
            abort(403);
        }

        $conversations = $this->getConversations(Auth::id());

        return view('messages.conversations', [
            'conversations' => $conversations,
        ]);
    }

    public function show($id, User $user)
    {
        if ((int) $id !== Auth::id()) {
            abort(403);
        }

        if ($user->id === Auth::id()) {
            abort(403);
        }

        $this->markThreadAsRead($user->id);

        $messages = $this->getThreadMessages(Auth::id(), $user->id);

        return view('messages.chat', [
            'partner' => $user,
            'messages' => $messages,
        ]);
    }

    public function fetchMessages(Request $request, User $user)
    {
        if (!$this->canChatWith($user->id)) {
            abort(403);
        }

        $since = $request->query('since');

        $query = Requests::where(function ($q) use ($user) {
            $q->where(function ($q2) use ($user) {
                $q2->where('user_id', Auth::id())->where('reciever', $user->id);
            })->orWhere(function ($q2) use ($user) {
                $q2->where('user_id', $user->id)->where('reciever', Auth::id());
            });
        })->orderBy('created_at');

        if ($since) {
            $query->where('created_at', '>', $since);
        }

        $messages = $query->get();
        $this->markThreadAsRead($user->id);

        return response()->json([
            'messages' => $messages->map(fn ($m) => $this->formatMessage($m)),
        ]);
    }

    public function markRead(User $user)
    {
        if (!$this->canChatWith($user->id)) {
            abort(403);
        }

        $this->markThreadAsRead($user->id);

        return response()->json([
            'success' => true,
            'messages' => $this->unreadMessageCount(),
        ]);
    }

    public function send(Request $request, User $user)
    {
        if (!$this->canChatWith($user->id)) {
            abort(403);
        }

        $validated = $request->validate([
            'message' => 'required|string|min:1|max:5000',
            'gig_id' => 'nullable|exists:gigs,id',
        ]);

        $message = Requests::create([
            'user_id' => Auth::id(),
            'gig_id' => $validated['gig_id'] ?? null,
            'reciever' => $user->id,
            'sender' => Auth::user()->name,
            'message' => $validated['message'],
        ]);

        NotificationService::send(
            $user,
            'New message from ' . Auth::user()->name,
            \Illuminate\Support\Str::limit($validated['message'], 80),
            '/user/chats/' . $user->id . '/with/' . Auth::id(),
            'message'
        );

        return response()->json([
            'success' => true,
            'message' => $this->formatMessage($message),
        ]);
    }

    public function pollCounts()
    {
        $userId = Auth::id();

        $unreadMessages = $this->unreadMessageCount();
        $unreadAlerts = Auth::user()->unreadNotifications()->count();
        $pendingProposals = \App\Models\Proposal::whereHas('gig', fn ($q) => $q->where('user_id', $userId))
            ->whereIn('status', ['pending', 'shortlisted'])->count();

        $pendingReviews = \App\Models\Tasks::where(function ($q) use ($userId) {
            $q->where('user_id', $userId)->orWhere('owner', $userId);
        })->where('payment_flag', 1)
            ->whereDoesntHave('reviews', fn ($q) => $q->where('reviewer_id', $userId))
            ->count();

        return response()->json([
            'messages' => $unreadMessages,
            'alerts' => $unreadAlerts,
            'proposals' => $pendingProposals,
            'pending_reviews' => $pendingReviews,
            'alerts_list' => Auth::user()->unreadNotifications()->latest()->take(5)->get()->map(fn ($n) => [
                'id' => $n->id,
                'title' => $n->data['title'] ?? 'Alert',
                'message' => $n->data['message'] ?? '',
                'url' => $n->data['url'] ?? null,
                'time' => $n->created_at->diffForHumans(),
            ]),
        ]);
    }

    private function canChatWith(int $userId): bool
    {
        return Auth::check() && $userId !== Auth::id();
    }

    private function unreadMessageCount(): int
    {
        return Requests::where('reciever', Auth::id())->whereNull('read_at')->count();
    }

    private function markThreadAsRead(int $partnerId): void
    {
        Requests::where('reciever', Auth::id())
            ->where('user_id', $partnerId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    private function getThreadMessages(int $authId, int $partnerId)
    {
        return Requests::where(function ($q) use ($authId, $partnerId) {
            $q->where(fn ($q2) => $q2->where('user_id', $authId)->where('reciever', $partnerId))
                ->orWhere(fn ($q2) => $q2->where('user_id', $partnerId)->where('reciever', $authId));
        })->orderBy('created_at')->get();
    }

    private function getConversations(int $userId): array
    {
        $messages = Requests::where('user_id', $userId)
            ->orWhere('reciever', $userId)
            ->orderByDesc('created_at')
            ->get();

        $partners = [];

        foreach ($messages as $message) {
            $partnerId = $message->user_id == $userId ? $message->reciever : $message->user_id;

            if (!isset($partners[$partnerId])) {
                $partner = User::find($partnerId);
                if ($partner) {
                    $partners[$partnerId] = [
                        'user' => $partner,
                        'last_message' => $message->message,
                        'last_at' => $message->created_at,
                    ];
                }
            }
        }

        return collect($partners)->sortByDesc('last_at')->values()->all();
    }

    private function formatMessage(Requests $message): array
    {
        return [
            'id' => $message->id,
            'body' => $message->message,
            'sender_id' => $message->user_id,
            'sender_name' => $message->sender,
            'is_mine' => $message->user_id === Auth::id(),
            'created_at' => $message->created_at->toIso8601String(),
            'time' => $message->created_at->format('M d, H:i'),
        ];
    }
}
