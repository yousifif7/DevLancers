<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Devlance | @yield('title')</title>
    <script src="https://kit.fontawesome.com/7ba6153525.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="/bootstrap/bootstrap.css">
    <link rel="stylesheet" href="/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
</head>
<body>

    <nav class="navbar bg-body-tertiary">
        <div class="container-fluid">
            <a class="navbar-brand" href="/">
                <img src={{asset('images/devlancer-logo.png')}} alt="Logo" width="70" height="50" class="logo d-inline-block align-text-top">
            </a>                 
            <ul class="nav justify-content-end">
            </ul>
        </div>
    </nav>
    <main>
        @yield('content')
    </main>
</body>
</html>