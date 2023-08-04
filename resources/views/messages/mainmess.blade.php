@extends('layout')

@section('content')
<div class="container">
    <ul class="nav justify-content-center" style="background-color: #e6e6e6">
        <li class="nav-item ">
            <a class="nav-link @yield('style1')" href="/user/notifications/{{Auth::user()->id}}">Recieved</a>
        </li>
        <li class="nav-item">
            <a class="nav-link @yield('style2')" href="/user/sent/{{Auth::user()->id}}">Sent</a>
        </li>
    </ul>
</div>
<br>
    @yield('contenttype')
@endsection