<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>DevLancer @yield('title')</title>
    <link rel="icon" type="image/x-icon" href="/images/favicon.ico">
    <script src="https://kit.fontawesome.com/7ba6153525.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="/bootstrap/bootstrap.css">
    <link rel="stylesheet" href="/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>
</head>

<body>
    <nav class="navbar bg-body-tertiary">
        <div class="container-fluid">
            <a class="navbar-brand" href="/">
                <img src={{ asset('images/devlancer-logo.png') }} alt="Logo" width="70" height="50"
                    class="logo d-inline-block align-text-top">
            </a>
            <ul class="nav justify-content-end">
                @auth
                    <?php
                    $messages = App\Models\Requests::where('reciever', '=', Auth::user()->id)->get();
                    ?>

                    <li class="nav-item" style="margin-top:5px;">
                        <div class="container">
                            <a class="regbtn w-100" href="/gigs/create">Create POST</a>
                        </div>
                    </li>
                    <li class="nav-item" style="margin-top:5px;">
                        <i class="fa-solid fa-user"></i>
                        <a class="" href="/gigs/profile" style="text-decoration:none;">{{ Auth::user()->name }}</a>
                    </li>
                    <li class="nav-item" style="margin-top:5px;">
                        <a class="" href="/user/notifications/{{ Auth::user()->id }}"
                            style="text-decoration:none; color:red;">
                            @if (count($messages) == 0)
                                <i class="fa-solid fa-bell" style="color:black;"></i>
                            @else
                                <i class="fa-solid fa-bell fa-shake" style="color:black;"></i>
                            @endif
                            {{ count($messages) }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <form method="post" action="/logout">
                            @csrf
                            <button type="submit" class="regbtn btn-sm">Log out</button>
                        </form>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="regbtn btn-sm" href="/signup" style="text-decoration:none;">Sign Up</a>
                    </li>
                    <li class="nav-item">
                        <a class="regbtn btn-sm" href="/login" style="text-decoration:none;">Login</a>
                    </li>
                @endauth
            </ul>
        </div>
    </nav>
    <main>
        @yield('createGig')
        @yield('content')
    </main>
    <br><br>
    <footer class="text-center text-lg-start p-2 bg-dark">
        <!-- Copyright -->
        <div class="text-center " style="color: white;">
            <small> All rights are reserved <a class="text-dark" style="text-decoration:none;" href="#"><strong
                        style="color:white;">DevLancer</strong></a>
                <?php echo '&copy;' . date('Y'); ?><small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous">
    </script>
    <script src="/bootstrap/bootstrap.js"></script>
    <script src="/bootstrap/popper.min.js"></script>
    <script src="/bootstrap/jquery-5.3.0.min.js"></script>
    @yield('scripts')
</body>

</html>
