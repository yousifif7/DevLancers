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
                <h4 class="text-center text-success">Log in and continue merching your business</h4>

                <div class="col-md-10 col-lg-6 col-xl-5 order-2 order-lg-1">
  
                  <p class="text-center h1 fw-bold mb-5 mx-1 mx-md-4 mt-4">Login</p>
  
                  <form class="mx-1 mx-md-4" method="post" action="/users/authenticate">
                    @csrf

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

                    <div class="d-flex justify-content-center mx-4 mb-3 mb-lg-4">
                      <button class="gigbtn btn-lg w-100" >Login</button>
                    </div>
                    
                  </form>
                  <p>Don't have an account? 
                    <a href="/signup">Sign up</a>
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