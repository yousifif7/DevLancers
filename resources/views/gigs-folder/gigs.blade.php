@extends('layout')

@section('title')| Latest Gigs @endsection



@section('content')
@if (session()->has('message'))
    <div class="alert alert-warning alert-dismissible fade show container" role="alert">
        {{session('message')}}
        <button type="button" class="btn-close btn-danger" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
@endif
<link rel="stylesheet" href="/styles.css">
<br>
<div class="container img">
    <img src={{asset('images/gigs_panel.png')}} class="card-img-top" alt="..." 
    style="object-fit:contain;">
</div>

    <br>
<div class="container">
    <form class="d-flex " action="/">
        <input class="form-control me-2 search" type="search" placeholder="Search" aria-label="Search" name="search">
        <button class="search-btn" type="submit">Search</button>
    </form>
</div>
<br>

<div class="container">
    <br><br>
    <hr class="dropdown-divider">
    @unless (count($gigs) == 0)
        <div class="row">
            @foreach ($gigs as $gig )
                @php
                    $tags=explode(',',$gig->tag)
                @endphp
                <div class="col-lg-6" style="margin-bottom:5px">
                    <div class="card card-bg">
                        <div class="row">
                            <div class="col-3">
                                @if ($gig->image)
                                <a href="/gigs/{{$gig->id}}">
                                    <img src={{asset('storage/'.$gig->image)}} class="card-img-top" style="object-fit: cover">
                                </a>
                                @else
                                    @if ($gig->gig_type == 'gig')    
                                        <a href="/gigs/{{$gig->id}}">
                                            <img src={{asset('images/gig-logo_default_1024x1024.png')}} class="card-img-top" style="object-fit: cover">
                                        </a>
                                    @else 
                                        <a href="/gigs/{{$gig->id}}">
                                            <img src={{asset('images/job1.png')}} class="card-img-top" style="object-fit: cover">
                                        </a>   
                                    @endif
                                @endif
                            </div>
                            <div class="col-9">
                                <div class="row">
                                    <div class="col-9">
                                        <a class="card-body" href="/gigs/{{$gig->id}}">
                                            <h5>
                                                <a class="gig-title" href="/gigs/{{$gig->id}}">{{$gig->title}}</a>
                                            </h5>                     
                                        </a>
                                    </div>
                                    <div class="col-3" style="margin-top:5px;">
                                        <?php #$user = app\models\User::find($gig->user_id); ?>
                                        @if ($gig->gig_type == "gig")
                                            <a class="list-gig" href="/?type={{$gig->gig_type}}">Gig</a>
                                        @else
                                            <a class="list-job" href="/?type={{$gig->gig_type}}">Job</a>
                                        @endif
                                        <a class="list-price">{{$gig->salary}}$</a>
                                    </div>
                                </div>
                                <div class="row">
                                    <ul class="nav mb-1" style="margin-left:30px">
                                        @foreach ($tags as $tag)
                                        <li class="nav-item tags-main">
                                        <a href="/?tag={{$tag}}" class="nav-link" ><b>{{$tag}}</b></a>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>   
                        </div>    
                    </div>
                </div>
            @endforeach
        </div>    
        @else
            <h5 class="bg-light text-danger p-1">No gigs found!</h5>
    @endunless
</div><br>
<div class="Page navigation example">
    {{$gigs->links()}}
</div><br>
@endsection