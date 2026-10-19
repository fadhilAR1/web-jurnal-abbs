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
    <link rel="preload" href="{{ asset('css/custom-ui.css?v=' . filemtime(public_path('css/custom-ui.css'))) }}" as="style">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/custom-ui.css?v=' . filemtime(public_path('css/custom-ui.css'))) }}" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('styles')
    <style>
        /* Ensure critical dashboard styles are loaded immediately */
        .summary-tile {
            border-radius: 12px;
            padding: 18px;
            background: white;
            border: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }
        .card {
            border-radius: 12px;
            border: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }
        .action-btn {
            transition: transform 0.12s ease !important;
        }

        /* Responsive Sidebar */
        html {
            height: 100%;
            margin: 0;
            overflow-x: hidden;
        }

        body {
            height: 100%;
            margin: 0;
            overflow-x: hidden;
            display: flex;
            flex-direction: row;
        }

        .sidebar {
            background-color: #f8f9fa;
            transition: transform 0.3s ease;
            overflow-y: auto;
            overflow-x: hidden;
            flex-shrink: 0;
        }

        #main-content {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
        }

        @media (max-width: 767px) {
            body {
                flex-direction: column;
            }

            .sidebar {
                position: fixed;
                top: 0;
                left: 0;
                width: 250px;
                height: 100vh;
                z-index: 1050;
                transform: translateX(-100%);
                box-shadow: 2px 0 5px rgba(0,0,0,0.1);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .sidebar-overlay {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: rgba(0,0,0,0.5);
                z-index: 1040;
                display: none;
            }

            .sidebar-overlay.show {
                display: block;
            }

            #main-content {
                width: 100%;
                flex: 1;
            }
        }

        @media (min-width: 768px) {
            body {
                flex-direction: row;
            }

            .sidebar {
                position: sticky;
                top: 0;
                height: 100vh;
                width: 250px;
                border-right: 1px solid #dee2e6;
                overflow-x: hidden;
            }

            .sidebar-overlay {
                display: none !important;
            }

            #main-content {
                flex: 1;
                width: calc(100% - 250px);
                overflow-x: hidden;
            }

            .navbar-toggler {
                display: none !important;
            }
        }

        /* Navbar styling */
        .navbar {
            position: relative;
        }

        .navbar-nav .dropdown-menu {
            min-width: 200px;
        }

        #toggleSidebar {
            display: none !important;
        }

        @media (max-width: 767px) {
            .navbar {
                padding: 0.5rem 0.5rem;
            }

            #toggleSidebar {
                display: inline-block !important;
                margin-right: 0.5rem;
                order: -1;
            }

            .navbar .container-fluid {
                gap: 0;
            }

            #navbar {
                margin-left: auto !important;
                margin-right: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar Overlay (Mobile) -->
    <div id="sidebarOverlay" class="sidebar-overlay"></div>

    <!-- Sidebar -->
    <nav id="sidebar" class="bg-light sidebar">
        <div class="position-sticky pt-3" style="top: 0;">
            <div class="sidebar-brand mb-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center brand-left">
                        <img src="{{ asset('images/Logo_SMA_ABBS.png') }}" alt="SMA ABBS" class="logo-sidebar me-2" onerror="this.style.display='none'">
                        <div class="brand-text"><strong>SMA ABBS SURAKARTA</strong></div>
                    </div>
                    <button type="button" class="btn-close d-md-none ms-2" id="closeSidebar" aria-label="Close"></button>
                </div>
            </div>
            <hr>
                    <ul class="nav flex-column">
                        @auth
                            @if(auth()->user()->isAdmin())
                                <!-- Admin Navigation -->
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" 
                                       href="{{ route('admin.dashboard') }}">
                                        Dashboard
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.users') ? 'active' : '' }}" 
                                       href="{{ route('admin.users') }}">
                                        Kelola Pengguna
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.master-siswa.*') ? 'active' : '' }}"
                                       href="{{ route('admin.master-siswa.index') }}">
                                        Kelola Siswa
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.master-kelas.*') ? 'active' : '' }}" 
                                       href="{{ route('admin.master-kelas.index') }}">
                                        Kelola Kelas
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.master-mapel.*') ? 'active' : '' }}" 
                                       href="{{ route('admin.master-mapel.index') }}">
                                        Kelola Mapel
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.master-ekskul.*') ? 'active' : '' }}" 
                                       href="{{ route('admin.master-ekskul.index') }}">
                                        Kelola Ekstrakurikuler
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.monitoring.kbm') ? 'active' : '' }}" 
                                       href="{{ route('admin.monitoring.kbm') }}">
                                        Jurnal KBM
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.monitoring.ekskul') ? 'active' : '' }}" 
                                       href="{{ route('admin.monitoring.ekskul') }}">
                                        Jurnal Ekstrakurikuler
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.monitoring.piket') ? 'active' : '' }}" 
                                       href="{{ route('admin.monitoring.piket') }}">
                                        Jurnal Piket
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.monitoring.kegiatan') ? 'active' : '' }}" 
                                       href="{{ route('admin.monitoring.kegiatan') }}">
                                        Jurnal Kegiatan
                                    </a>
                                </li>
                            @elseif(auth()->user()->isGuru())
                                <!-- Guru Navigation -->
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" 
                                       href="{{ route('dashboard') }}">
                                        Dashboard
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('jurnal.kbm') ? 'active' : '' }}" 
                                       href="{{ route('jurnal.kbm') }}">
                                        Jurnal KBM
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('jurnal.ekskul') ? 'active' : '' }}" 
                                       href="{{ route('jurnal.ekskul') }}">
                                        Jurnal Ekstrakurikuler
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('jurnal.piket') ? 'active' : '' }}" 
                                       href="{{ route('jurnal.piket') }}">
                                        Jurnal Piket
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('jurnal.kegiatan') ? 'active' : '' }}" 
                                       href="{{ route('jurnal.kegiatan') }}">
                                        Jurnal Kegiatan
                                    </a>
                                </li>
                            @endif
                        @endauth
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main id="main-content">
                <!-- Top navbar -->
                <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top">
                    <div class="container-fluid d-flex align-items-center flex-nowrap justify-content-start" style="gap: 0;">
                        <button class="navbar-toggler" type="button" id="toggleSidebar" aria-label="Toggle sidebar" style="border: none; padding: 0.25rem 0.5rem; display: none;">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        
                        <div id="navbar" class="ms-auto" style="margin-left: auto !important;">
                            <ul class="navbar-nav">
                                @auth
                                    <li class="nav-item dropdown">
                                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                            {{ auth()->user()->name }}
                                            @if(auth()->user()->isAdmin())
                                                <span class="badge bg-danger">Admin</span>
                                            @elseif(auth()->user()->isGuru())
                                                <span class="badge bg-success">Guru</span>
                                            @endif
                                        </a>
                                        <ul class="dropdown-menu">
                                            @if(auth()->user()->isGuru())
                                                <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Akun</a></li>
                                                <li><hr class="dropdown-divider"></li>
                                            @endif
                                            <li>
                                                <form method="POST" action="{{ route('logout') }}">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item">Logout</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </li>
                                @endauth
                            </ul>
                        </div>
                    </div>
                </nav>

                <!-- Page content -->
                <div class="container-fluid py-4 px-3 px-md-4">
                    @yield('content')
                </div>
            </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')

    <script>
        // Initialize SweetAlert for success/error messages
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6',
                    allowOutsideClick: false
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: '{{ session('error') }}',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#d33',
                    allowOutsideClick: false
                });
            @endif

            @if($errors->any())
                let errorMessages = '<ul style="text-align: left; margin: 0;">';
                @foreach($errors->all() as $error)
                    errorMessages += '<li>{{ $error }}</li>';
                @endforeach
                errorMessages += '</ul>';
                
                Swal.fire({
                    icon: 'warning',
                    title: 'Ada Kesalahan!',
                    html: errorMessages,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#ff9800',
                    allowOutsideClick: false
                });
            @endif
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            const toggleButton = document.getElementById('toggleSidebar');
            const closeButton = document.getElementById('closeSidebar');

            // Toggle sidebar on hamburger click
            if (toggleButton) {
                toggleButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    sidebar.classList.toggle('show');
                    sidebarOverlay.classList.toggle('show');
                });
            }

            // Close sidebar on close button click
            if (closeButton) {
                closeButton.addEventListener('click', function() {
                    sidebar.classList.remove('show');
                    sidebarOverlay.classList.remove('show');
                });
            }

            // Close sidebar on overlay click
            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', function() {
                    sidebar.classList.remove('show');
                    sidebarOverlay.classList.remove('show');
                });
            }

            // Close sidebar on link click (for mobile)
            const navLinks = sidebar.querySelectorAll('.nav-link');
            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth < 768) {
                        sidebar.classList.remove('show');
                        sidebarOverlay.classList.remove('show');
                    }
                });
            });
        });
    </script>
</body>
</html>