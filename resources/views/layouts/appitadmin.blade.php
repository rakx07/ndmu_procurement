<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - IT Admin</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Bootstrap & Tailwind -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        /* Sidebar Styles */
        :root {
            --primary-dark-green: #004225;
            --secondary-light-green: #77DD77;
            --hover-green: #005A30;
        }

        #sidebar {
            width: 14rem; /* Default width */
            transition: width 0.3s ease-in-out;
        }
        .sidebar-collapsed {
            width: 3rem; /* Smaller width when collapsed */
        }
        .sidebar-collapsed .sidebar-text {
            display: none;
        }
        .sidebar-collapsed .sidebar-icon {
            justify-content: center;
        }
        .sidebar-collapsed .toggle-icon {
            transform: rotate(180deg);
        }

        /* Adjust main content when sidebar is collapsed */
        #main-content {
            transition: margin-left 0.3s ease-in-out;
            margin-left: 14rem;
        }
        .content-collapsed {
            margin-left: 3rem;
        }
    </style>
</head>
<body class="bg-gray-100">

    <div id="app" class="flex">
        <!-- Sidebar -->
        <div id="sidebar" class="bg-[var(--primary-dark-green)] text-white h-screen fixed transition-all duration-300">
            <div class="p-4 flex justify-between items-center">
                <h1 class="text-xl font-bold sidebar-text">{{ config('app.name', 'Laravel') }} IT Admin</h1>
                <button id="sidebarToggle" class="text-white text-lg transform transition-all duration-300 toggle-icon">⏪</button>
            </div>
            <nav class="mt-4">
                <a href="{{ route('user.management') }}" class="block py-2.5 px-4 flex items-center space-x-2 sidebar-icon hover:bg-[var(--hover-green)] transition">
                    <span>👤</span><span class="sidebar-text">User Management</span>
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div id="main-content" class="flex-1 min-h-screen transition-all duration-300">
            <!-- Navbar -->
            <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
                <div class="container">
                    <a class="navbar-brand" href="{{ url('/') }}">
                        {{ config('app.name', 'Laravel') }}
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <!-- Left Side Of Navbar -->
                        <ul class="navbar-nav me-auto"></ul>

                        <!-- Right Side Of Navbar -->
                        <ul class="navbar-nav ms-auto">
                            @guest
                                @if (Route::has('login'))
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                    </li>
                                @endif
                                @if (Route::has('register'))
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                    </li>
                                @endif
                            @else
                                <li class="nav-item dropdown">
                                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                        {{ Auth::user()->name }}
                                    </a>

                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                        <a class="dropdown-item" href="{{ route('logout') }}"
                                           onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                            {{ __('Logout') }}
                                        </a>

                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                            @csrf
                                        </form>
                                    </div>
                                </li>
                            @endguest
                        </ul>
                    </div>
                </div>
            </nav>

            <!-- Page Content -->
            <main class="p-4">
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        const sidebar = document.getElementById("sidebar");
        const sidebarToggle = document.getElementById("sidebarToggle");
        const mainContent = document.getElementById("main-content");

        let isCollapsed = false;

        sidebarToggle.addEventListener("click", function () {
            isCollapsed = !isCollapsed;
            sidebar.classList.toggle("sidebar-collapsed", isCollapsed);
            mainContent.classList.toggle("content-collapsed", isCollapsed);
            sidebarToggle.innerHTML = isCollapsed ? "⏩" : "⏪";
        });

        document.getElementById("mobileSidebarToggle")?.addEventListener("click", function () {
            sidebar.classList.toggle("-translate-x-full");
        });
    </script>

</body>
</html>
