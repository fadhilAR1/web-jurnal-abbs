<!DOCTYPE html>
<html lang="id">
<head>
    <!-- Favicon / App icons -->
    <link rel="icon" type="image/png" href="{{ asset('images/Logo_SMA_ABBS.png') }}">
    <link rel="shortcut icon" href="{{ asset('images/Logo_SMA_ABBS.png') }}" type="image/png">
    <link rel="apple-touch-icon" href="{{ asset('images/Logo_SMA_ABBS.png') }}">
    <meta name="msapplication-TileImage" content="{{ asset('images/Logo_SMA_ABBS.png') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - SMA ABBS Surakarta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/custom-ui.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body class="bg-light">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12">
                <!-- Header -->
                <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
                    <div class="container">
                        @if(request()->routeIs('login'))
                            <div class="navbar-brand fw-bold d-flex align-items-center text-primary">
                                <img src="{{ asset('images/Logo_SMA_ABBS.png') }}" alt="SMA ABBS" class="me-2 logo-header" onerror="this.style.display='none'">
                                <span class="text-primary">SMA ABBS Surakarta</span>
                            </div>
                        @else
                            <a class="navbar-brand fw-bold d-flex align-items-center" href="/">
                                <img src="{{ asset('images/Logo_SMA_ABBS.png') }}" alt="SMA ABBS" class="me-2 logo-header" onerror="this.style.display='none'">
                                <span>SMA ABBS Surakarta</span>
                            </a>
                        @endif
                    </div>
                </nav>

                <!-- Main Content -->
                <main class="py-4">
                    @yield('content')
                </main>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>