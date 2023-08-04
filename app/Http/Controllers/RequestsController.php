<?php

namespace App\Http\Controllers;

use App\Models\Gigs;
use App\Models\User;
use App\Models\Requests;
use Illuminate\Http\Request;

class RequestsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function submitMsg($id)
    {
        $reciever=User::find($id);
        return view('messages.reply',['reciever'=>$reciever]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // $gig=Gigs::find($request->gig_id);
        $request->validate([
            'user_id'=>'required',
            'sender'=>'required',
            'message'=>'required',
        ]);

        Requests::create([
            'user_id'=>$request->user_id,
            'gig_id'=>$request->gig_id,
            'reciever'=>$request->reciever,
            'sender'=>$request->sender,
            'message'=>$request->message,
        ]);

        $user=User::find($request->user_id);
        return view('messages.notifications', ['user'=> $user])->with('message','Message sent successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $message=Requests::find($id);
        $message->delete();
        return back()->with('message', 'Message deleted succesfully!');
    }

    public function destroyAllSent($id)
    {
        $message=Requests::where("user_id","=",$id);
        $message->delete();
        return back()->with('message', 'All messages deleted succesfully!');
    }

    public function destroyAllRecieved($id)
    {
        $message=Requests::where("reciever","=",$id);
        $message->delete();
        return back()->with('message', 'All messages deleted succesfully!');
    }
}
