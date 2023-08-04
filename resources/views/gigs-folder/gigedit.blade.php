@extends('layout')

@section('content')
<div>
    <button onclick="history.back()" class="back-btn fa-sharp fa-solid fa-left-long fa-xl"></button>
</div>
<br>
<div class="">
    <div class="row">
        <div class="col"></div>
        <div class="col-10">
            <h2 class="text-center text-primary">Edit a gig</h2>
            <form class="form" method="POST" action="/gigs/{{$gig->id}}" enctype="multipart/form-data"><br>        
                @csrf
                @method('PUT')    
                <!-- Text input -->
                <p class="text-secondary"> <b>Change the following required fields</b></p>
                <div class="form-outline mb-4">
                    <label class="form-label" for="form6Example3"><strong>Gig title</strong></label>
                    <input type="text" placeholder="Web Development" id="form6Example3" class="form-control" name="title"
                    value="{{$gig->title}}" />

                    @error('title')
                        <p class="text-danger">{{$message}}</p>
                    @enderror
                </div>
            
                <!-- Text input -->
                <div class="form-outline mb-4">
                    <label class="form-label" for="form6Example4"><strong>Gig tags</strong> </label>
                    <label class="form-label"></label>
                    <input type="text" id="form6Example4" class="form-control" name="tag" placeholder="Laravel, Bootstrap, ..." value="{{$gig->tag}}"/>
                    @error('tag')
                        <p class="text-danger">{{$message}}</p>
                    @enderror
                    <label class="form-label text-primary" for="form6Example4" style="font-size: 12px">Tags must be seperated by colon ( , )</label>
                    
                </div>
            
                <!-- Email input -->
                <div class="form-outline mb-4">
                    <label class="form-label" for="form6Example5"><strong>Contact Email</strong></label>
                    <input type="email" id="form6Example5" class="form-control" name="email" placeholder="test@example.com" 
                    value="{{$gig->email}}"/>

                    @error('email')
                        <p class="text-danger">{{$message}}</p>
                    @enderror
                </div>
            
                <!-- Message input -->
                <div class="form-outline mb-4">
                    <label class="form-label" for="form6Example7"><strong>Gig description</strong></label>
                    <textarea class="form-control" id="form6Example7" rows="4" name="description">{{$gig->description}}</textarea>

                    @error('description')
                        <p class="text-danger">{{$message}}</p>
                    @enderror
                </div>

                <div class="form-outline mb-4">
                    <label class="form-label" for="form6Example8"><strong>Basic salary</strong></label>
                    <input type="number" id="form6Example8" class="form-control" name="salary" placeholder="100$" 
                    value="{{$gig->salary}}" min="5" max="100" />

                    @error('salary')
                        <p class="text-danger">{{$message}}</p>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="formFile" class="form-label"><strong>Gig thumbnail / logo</strong></label>
                    <input class="form-control" type="file" id="formFile" name="image">
                    @if ($gig->image)
                                <a href="/gigs/{{$gig->id}}">
                                    <img src={{asset('storage/'.$gig->image)}} height="100px">
                                </a>
                    @endif
                </div>
                <br>  
            
                <!-- Submit button -->
                <button type="submit" class="search-btn w-100">Submit</button>
            </form> <br> 
        </div>
        <div class="col"></div>
    </div>
</div>
  @endsection