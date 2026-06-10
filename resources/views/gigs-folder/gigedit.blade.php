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
                    value="{{$gig->salary}}" />

                    @error('salary')
                        <p class="text-danger">{{$message}}</p>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label"><strong>Current images</strong></label>
                    @php $existingImages = $gig->media->where('type', 'image'); @endphp
                    @if ($existingImages->isNotEmpty())
                        <div class="d-flex flex-wrap gap-2 mb-2">
                            @foreach ($existingImages as $media)
                                <div class="position-relative text-center">
                                    <img src="{{ $media->url() }}" height="80" class="rounded border">
                                    <form method="POST" action="/gigs/media/{{ $media->id }}/delete" class="mt-1">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Remove</button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted small">No images yet.</p>
                    @endif
                </div>

                <div class="mb-3">
                    <label for="formImages" class="form-label"><strong>Add images</strong> <small class="text-muted">(up to 6 total)</small></label>
                    <input class="form-control" type="file" id="formImages" name="images[]" accept="image/jpeg,image/png,image/webp" multiple>
                    @error('images.*')
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label"><strong>Current attachments</strong></label>
                    @php $existingFiles = $gig->media->where('type', 'attachment'); @endphp
                    @if ($existingFiles->isNotEmpty())
                        <ul class="list-group mb-2">
                            @foreach ($existingFiles as $media)
                                <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                                    <a href="{{ $media->url() }}" target="_blank">{{ $media->original_name ?? basename($media->path) }}</a>
                                    <form method="POST" action="/gigs/media/{{ $media->id }}/delete">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Remove</button>
                                    </form>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted small">No attachments yet.</p>
                    @endif
                </div>

                <div class="mb-3">
                    <label for="formAttachments" class="form-label"><strong>Add attachments</strong> <small class="text-muted">(PDF, DOC, ZIP, etc.)</small></label>
                    <input class="form-control" type="file" id="formAttachments" name="attachments[]" multiple>
                    @error('attachments.*')
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
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