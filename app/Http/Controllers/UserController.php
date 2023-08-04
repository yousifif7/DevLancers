<?php

namespace App\Http\Controllers;

use App\Models\Gigs;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('registeration.signup');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $form=$request->validate([
            'name' => ['required', 'min:3'],
            'email' => ['required', 'email', Rule::unique("users", "email")],
            'password' => ['required ', 'confirmed', 'min:8' ],
            'acc_type' => 'required',
            'gender' => 'required',
        ]);
        $form['password'] = bcrypt($form['password']);

        $user =User::create($form);
        Auth()->login($user);
        return redirect('/')->with('message','User created and logged in succesfully! ');
    }

    //Log user out 
    public function logout(Request $request){
        auth()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('message','You have been logged out succesfully! ');
        
    }

    //Log user out 
    public function login(){

        return view('registeration.login');    
    }

    //Authenticate user to log in
    public function authenticate(Request $request){
        $form=$request->validate([
            'email' => ['required', 'email'],
            'password' => 'required ',
        ]);
       if(auth()->attempt($form)){
            $request->session()->regenerate();

            return redirect('/')->with('message','You have been logged in succesfully! '); 
       }

       return back()->withErrors(['email'=> 'Invalid Email'])->onlyInput('email');
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
    public function update(Request $request, User $user)
    {
        if($user->id != Auth::user()->id){
            abort(404,'Unauthorized action');
        }

        $request->validate([
            'name' => 'required',
            'Gender' => '',
        ]);

        $user->update([
            'name' => $request->name,
            'Gender' => $request->gender,
            'bio' => $request->bio,
            'address' => $request->address,
        ]);
        return redirect('/gigs/profile')->with('message','Profile updated successfully!');
    }
    
    //show user profile
    public function userProfile($id){
        $user = User::find($id);
        return view('registeration.user', ['user'=> $user]);
    }

    //Recieved messages 
    public function notifications($id){
        $user = User::find($id);
        return view('messages.notifications', ['user'=> $user]);
    }

    //Sent messages(replyes) by user
    public function sent($id){
        $user = User::find($id);
        return view('messages.sent', ['user'=> $user]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
