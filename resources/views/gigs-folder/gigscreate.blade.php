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
                @if (Auth::user()->acc_type == 1)
                    <h2 class="text-center text-primary">Posting a gig</h2>
                @else
                    <h2 class="text-center text-primary">Posting a Job</h2>
                @endif
                <form class="form" method="POST" action="/gigs" enctype="multipart/form-data"><br>
                    @csrf
                    <input hidden value="{{ Auth::user()->id }}" name="user_id">
                    @if (Auth::user()->acc_type == 1)
                        <input hidden value="gig" name="gig_type">
                    @else
                        <input hidden value="job" name="gig_type">
                    @endif
                    <!-- Text input -->
                    <p class="text-secondary"> <b>Fill the following fields</b></p>
                    <div class="form-outline mb-4">
                        <label class="form-label" for="form6Example3"><strong>Title</strong></label>
                        <input type="text" placeholder="Web Development" id="form6Example3" class="form-control"
                            name="title" value="{{ old('title') }}" />

                        @error('title')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Text input -->
                    <div class="form-outline mb-4">
                        <label class="form-label" for="form6Example4"><strong>Tags</strong> </label>
                        <input type="text" id="form6Example4" class="form-control" name="tag"
                            placeholder="Laravel, Bootstrap, ..." value="{{ old('tag') }}" />
                        @error('tag')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                        <label class="form-label text-primary" for="form6Example4" style="font-size: 12px">Tags must be
                            seperated by colon ( , )</label>

                    </div>

                    <!-- Email input -->
                    <div class="form-outline mb-4">
                        <label class="form-label" for="form6Example5"><strong>Contact Email</strong></label>
                        <input type="email" id="form6Example5" class="form-control" name="email"
                            placeholder="test@example.com" value="{{ old('email') }}" />

                        @error('email')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Message input -->
                    <div class="form-outline mb-4">
                        <label class="form-label" for="form6Example7"><strong>Description</strong></label>
                        <textarea class="form-control" id="form6Example7" rows="4" name="description">{{ old('description') }}</textarea>

                        @error('description')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-outline mb-4">
                        <label class="form-label" for="form6Example8"><strong>Basic salary</strong></label>
                        <div class="input-group mb-3">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control" aria-label="Amount (to the nearest dollar)"
                                value="{{ old('salary') }}" name="salary" placeholder="100$">
                        </div>

                        @error('salary')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="formFile" class="form-label"><strong>Thumbnail / logo</strong></label>
                        <input class="form-control" type="file" id="formFile" name="image"
                            value="{{ old('image') }}">
                    </div>
                    <br>

                    <!-- Submit button -->
                    <button type="submit" class="search-btn w-100">Post</button>
                </form> <br>
            </div>
            <div class="col"></div>
        </div>
    </div>
@endsection
