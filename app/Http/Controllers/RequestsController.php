<?php

namespace App\Http\Controllers;

use App\Models\Gigs;
use App\Models\User;
use App\Models\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RequestsController extends Controller
{
    public function submitMsg($id)
    {
        return redirect('/user/chats/' . Auth::id() . '/with/' . $id);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'sender' => 'required',
            'message' => 'required',
        ]);

        if ((int) $request->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action');
        }

        Requests::create([
            'user_id' => $request->user_id,
            'gig_id' => $request->gig_id,
            'reciever' => $request->reciever,
            'sender' => $request->sender,
            'message' => $request->message,
        ]);

        return back()->with('message', 'Message sent successfully!');
    }

    public function destroy($id)
    {
        $message = Requests::findOrFail($id);

        if ((int) $message->user_id !== Auth::id() && (int) $message->reciever !== Auth::id()) {
            abort(403, 'Unauthorized action');
        }

        $message->delete();

        return back()->with('message', 'Message deleted succesfully!');
    }

    public function destroyAllSent($id)
    {
        if ((int) $id !== Auth::id()) {
            abort(403, 'Unauthorized action');
        }

        Requests::where('user_id', Auth::id())->delete();

        return back()->with('message', 'All sent messages deleted succesfully!');
    }

    public function destroyAllRecieved($id)
    {
        if ((int) $id !== Auth::id()) {
            abort(403, 'Unauthorized action');
        }

        Requests::where('reciever', Auth::id())->delete();

        return back()->with('message', 'All received messages deleted succesfully!');
    }
}
