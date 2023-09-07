<?php

namespace App\Http\Controllers;

use App\Models\Gigs;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class GigController extends Controller
{
    //to show all content in DB and the main view
    public function index(){
        return view('gigs-folder/gigs',[
            'gigs' => Gigs::latest()->filter(request(['tag', 'search','type']))->paginate(8),
        ]);
    }

    //show a specifec gig by id
    public function show(Gigs $gig){
        return view('gigs-folder/gig',[
            'gig' => $gig
        ]);
    }

    // Redirect to store function to a create a new gig 
    public function create(){
        return view('gigs-folder/gigscreate');
    }

    //to store a new gig in the DB.
    public function store(Request $request)
    {
        $gig=$request->validate([
            'user_id'=> 'required',
            'gig_type'=> 'required',
            'title' => 'required',
            'email' => ['required', 'email'],
            'description' => 'required',
            'tag' => '',
            'salary'=> 'required|numeric|min:5|max:1000',
            'image'=> 'mimes:jpg,png',
        ]);

        if($request->hasFile('image')){
            // $gig['image'] = $request->file('image')->store('images','public');
            Gigs::create([
                'user_id'=> $request->user_id,
                'gig_type'=> $request->gig_type,
                'title' => $request->title,
                'email' => $request->email,
                'description' => $request->description,
                'tag' => $request->tag,
                'salary'=> $request->salary,
                'image' => $request->file('image')->store('images','public'),
            ]);
        }else{
            Gigs::create([
                'user_id'=> $request->user_id,
                'gig_type'=> $request->gig_type,
                'title' => $request->title,
                'email' => $request->email,
                'description' => $request->description,
                'tag' => $request->tag,
                'salary'=> $request->salary,

            ]);
        }
        $user = User::find(Auth::user()->id);
        // return view('registeration.user', ['user'=> $user])->with('message', 'Gig Posted succesfully!');
        if(Auth::user()->acc_type==1){

            return redirect()->route('profile',['user'=> $user])->with('message','Gig Posted succesfully');
        }
        return redirect()->route('profile',['user'=> $user])->with('message','Job Posted succesfully');
    }

    //to redirect a gig to be updated
    public function edit(Gigs $gig){
        return view('gigs-folder/gigedit', ['gig' => $gig]);
    }

    //update a gig
    public function update(Request $request, Gigs $gig)
    {
        // Make sure the user is the owner
        if($gig->user_id != Auth::user()->id){
            abort(404,'Unauthorized action');
        }

        $request->validate([
            'title' => 'required',
            'email' => ['required', 'email'],
            'description' => 'required',
            'salary'=> 'required|numeric|min:5|max:1000',
            'tag' => '',
        ]);
        
        if($request->hasFile('image')){
            // $gig['image'] = $request->file('image')->store('images','public');
            $gig->update([
                'title' => $request->title,
                'email' => $request->email,
                'description' => $request->description,
                'tag' => $request->tag,
                'salary'=> $request->salary,
                'image' => $request->file('image')->store('images','public'),
            ]);
        }else{
            $gig->update([
                'title' => $request->title,
                'email' => $request->email,
                'description' => $request->description,
                'tag' => $request->tag,
                'salary'=> $request->salary,
            ]);
        }

        
        if(substr(url()->previous(), -1) == '/profile'){
            return redirect('/gigs/profile')->with('message','Profile updated successfully!');
        }
        if(Auth::user()->acc_type==1){
            return redirect('gigs/'.$gig->id)->with('message','Gig updated successfully!');
        }
        return redirect('gigs/'.$gig->id)->with('message','Job updated successfully!');

    }

    public function destroy(Gigs $gig){
        // Make sure the user is the owner
        if($gig->user_id != Auth::user()->id){
            abort(404,'Unauthorized action');
        }
        
        if ($gig::exists(public_path('images/'.$gig->image))) {
            File::delete(public_path('images/'.$gig->image));
            $gig->delete();

        }else{
            $gig->delete();
        }

        // if(substr(url()->current(), -1) == '/profile'){
        //     return redirect('/gigs/profile')->with('message','Profile updated successfully!');
        // }
        $user = User::find(Auth::user()->id);
        if($user->acc_type==1){

            return redirect()->route('profile',['user'=> $user])->with('message','Gig Deleted succesfully');
        }
        return redirect()->route('profile',['user'=> $user])->with('message','Job Deleted succesfully');


    }

    //Gives access to owner profile
    public function profile(){
        $gigs = Gigs::find(Auth::user()->id);
        return view('registeration.profile', ['gigs'=> $gigs]);
    }

}
