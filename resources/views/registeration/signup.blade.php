@extends('registeration.mainreg')

@section('title')Sign up @endsection

@section('content') 

<section class="vh-100" style="background-color: #eee;">
    <div class="container h-100">
      <div class="row d-flex justify-content-center align-items-center h-100">
        <div class="col-lg-12 col-xl-11">
          <div class="card text-black" style="border-radius: 25px;">
            <div class="card-body p-md-5">
              <div class="row justify-content-center">
                <h4 class="text-center text-success">Join us and start your own business</h4>

                <div class="col-md-10 col-lg-6 col-xl-5 order-2 order-lg-1">
  
                  <p class="text-center h1 fw-bold mb-5 mx-1 mx-md-4 mt-4">Sign up</p>
  
                  <form class="mx-1 mx-md-4" method="post" action="/users">
                    @csrf
                    <div class="d-flex flex-row align-items-center mb-4">
                      <div class="form-outline flex-fill mb-0">
                        <i class="fas fa-user fa-lg me-3 fa-fw"></i>
                        <label class="form-label" for="form3Example1c">Your Name</label>
                        <input type="text" id="form3Example1c" class="form-control" name="name" value="{{old('name')}}"/>
                        @error('name')
                          <p class="text-danger">{{$message}}</p>
                        @enderror
                      </div>
                    </div>

                    <div class="d-flex flex-row align-items-center mb-4">
                      <div class="form-outline flex-fill mb-0">
                        <i class="fas fa-envelope fa-lg me-3 fa-fw"></i>
                        <label class="form-label" for="form3Example3c">Your Email</label>
                        <input type="email" id="form3Example3c" class="form-control" name="email" value="{{old('email')}}"/>
                        @error('email')
                          <p class="text-danger">{{$message}}</p>
                        @enderror
                      </div>
                    </div>
  
                    <div class="d-flex flex-row align-items-center mb-4">
                      <div class="form-outline flex-fill mb-0">
                        <i class="fas fa-lock fa-lg me-3 fa-fw"></i>
                        <label class="form-label" for="form3Example4c">Password</label>
                        <input type="password" id="form3Example4c" class="form-control" name="password"/>
                        @error('password')
                          <p class="text-danger">{{$message}}</p>
                        @enderror
                      </div>
                    </div>
  
                    <div class="d-flex flex-row align-items-center mb-4">
                      <div class="form-outline flex-fill mb-0">
                        <i class="fas fa-key fa-lg me-3 fa-fw"></i>
                        <label class="form-label" for="form3Example4cd">Repeat your password</label>
                        <input type="password" id="form3Example4cd" class="form-control" name="password_confirmation"/>
                        @error('password_confirmation')
                          <p class="text-danger">{{$message}}</p>
                        @enderror
                      </div>
                    </div>

                    <div class="d-flex flex-row align-items-center mb-4">
                      <div class="form-outline flex-fill mb-0">
                        <i class="fas fa-briefcase fa-lg me-3 fa-fw"></i>
                        <label class="form-label" for="form3Example4cd">Account type</label>
                        <select class="form-select" aria-label="Default select example" name="acc_type">
                          <option ></option>
                          <option value="1">Worker</option>
                          <option value="2">Client</option>
                        </select>
                        @error('acc_type')
                          <p class="text-danger">Account type field is required</p>
                        @enderror
                      </div>
                    </div>

                    <div class="d-flex flex-row align-items-center mb-4">
                      <div class="form-outline flex-fill mb-0">
                        <i class="fas fa-briefcase fa-lg me-3 fa-fw"></i>
                        <label class="form-label" for="form3Example4cd">Your Gender</label>
                        <select class="form-select" aria-label="Default select example" name="gender">
                          <option ></option>
                          <option value="Male">Male</option>
                          <option value="Female">Female</option>
                          <option value="Other">Other</option>
                        </select>
                        @error('gender')
                          <p class="text-danger">Please choose your gender</p>
                        @enderror
                      </div>
                    </div>

                    <div class="d-flex justify-content-center mx-4 mb-3 mb-lg-4">
                      <button class="gigbtn btn-lg w-100" >Register</button>
                    </div>
                    
                  </form>
                  <p>Already have an account? 
                    <a href="/login">login</a>
                  </p>
                </div>
                <div class="col-md-10 col-lg-6 col-xl-7 d-flex align-items-center order-1 order-lg-2">
                  <img src="/images/devlancer-logo.png"
                    class="img-fluid" alt="Sample image" width="500px">
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
@endsection