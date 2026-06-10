<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\NotificationService;

class SupportController extends Controller
{
    public function index()
    {
        $tickets = Auth::check()
            ? SupportTicket::where('user_id', Auth::id())->latest()->get()
            : collect();

        return view('support.index', compact('tickets'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:20|max:5000',
        ]);

        $ticket = SupportTicket::create([
            'user_id' => Auth::id(),
            'name' => $validated['name'],
            'email' => $validated['email'],
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'status' => SupportTicket::STATUS_OPEN,
        ]);

        foreach (User::where('is_admin', true)->get() as $admin) {
            NotificationService::send(
                $admin,
                'New support ticket',
                $validated['subject'],
                '/admin/support',
                'support'
            );
        }

        return back()->with('message', 'Support request submitted. We will get back to you soon.');
    }
}
