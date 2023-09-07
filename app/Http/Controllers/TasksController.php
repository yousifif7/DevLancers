<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Tasks;
use Illuminate\Http\Request;

class TasksController extends Controller
{
    public function update(Request $request,Tasks $task){
        $task->update([
            'user_id'=> $request->user_id,
            'gig_id'=> $request->gig_id,
            'owner'=> $request->owner,
            'status'=> $request->status ,
            'content'=> $request->content ,
            'price'=> $request->price ,
            'payment_flag'=> $request->payment_flag ,
        
        ]);
        return back()->with('message','Task closed succesfully!');
    }
    public function tasks($id){
        $user = User::find($id);
        if($user->acc_type==1){
        return view('/tasks/wotasks', ['user'=> $user]);
            
        }
        return view('/tasks/cltasks', ['user'=> $user]);
    }

    public function store(Request $request)
    {
        $request->validate([
            // 'user_id'=>'required',
            // 'gig_id'=>'required',
            // 'status'=>'required',

        ]);

        Tasks::create([
            'user_id'=>$request->user_id,
            'gig_id'=>$request->gig_id,
            'status'=>$request->status,
            'owner'=>$request->owner,
            'content'=>$request->content,
            'price'=>$request->price,
            'payment_flag'=> $request->payment_flag ,
            
        ]);

        return back()->with("message","User hired successfuly! Check you're tasks list.");
    }

}
