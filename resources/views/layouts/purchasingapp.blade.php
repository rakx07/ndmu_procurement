<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Font Awesome & Bootstrap -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            display: flex;
            height: 100vh;
            background-color: #f8f9fa;
        }
        .sidebar {
            width: 240px;
            height: 100vh;
            background: #343a40;
            transition: all 0.3s ease-in-out;
            position: fixed;
            top: 0;
            left: 0;
            overflow-y: auto;
            padding-top: 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .sidebar.collapsed {
            width: 80px;
        }
        .sidebar .nav-link {
            color: white;
            display: flex;
            align-items: center;
            padding: 12px 15px;
            text-decoration: none;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
        }
        .sidebar .nav-link.active {
            background: #28a745 !important;
            font-weight: bold;
            border-radius: 5px;
        }
        .sidebar .nav-link i {
            width: 30px;
            text-align: center;
        }
        .sidebar .nav-text {
            display: inline-block;
            transition: all 0.3s ease-in-out;
        }
        .sidebar.collapsed .nav-text {
            display: none;
        }
        .toggle-btn {
            background: none;
            border: none;
            color: white;
            padding: 10px;
            cursor: pointer;
            position: absolute;
            top: 10px;
            left: 10px;
        }
        .content {
            margin-left: 240px;
            padding: 20px;
            flex-grow: 1;
            transition: all 0.3s ease-in-out;
            min-height: 100vh;
        }
        .content.collapsed {
            margin-left: 80px;
        }
        .submenu {
            display: none;
            padding-left: 30px;
        }
        .submenu.open {
            display: block;
        }
        .card {
            border-radius: 8px;
            background: white;
            padding: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <!-- ✅ Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div>
            <button class="toggle-btn" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            <ul class="nav flex-column mt-4">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                        <i class="fas fa-home"></i> <span class="nav-text">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}" href="{{ route('profile.edit') }}">
                        <i class="fas fa-user"></i> <span class="nav-text">Profile</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('purchasing_officer.create') ? 'active' : '' }}" href="{{ route('purchasing_officer.create') }}">
                        <i class="fas fa-plus-circle"></i> <span class="nav-text">Create Item</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('item-categories.index') ? 'active' : '' }}" href="{{ route('item-categories.index') }}">
                        <i class="fas fa-list"></i> <span class="nav-text">Manage Categories</span>
                    </a>
                </li>
            </ul>
        </div>
        <div class="mt-auto p-3">
            <a class="nav-link text-danger" href="{{ route('logout') }}" 
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt"></i> <span class="nav-text">Logout</span>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </div>
    </nav>

    <!-- ✅ Page Content -->
    <div class="content" id="main-content">
        @yield('content')
    </div>

    <!-- ✅ JavaScript -->
    <script>
        function toggleSidebar() {
            let sidebar = document.getElementById("sidebar");
            let content = document.getElementById("main-content");
            sidebar.classList.toggle("collapsed");
            content.classList.toggle("collapsed");
        }
    </script>
</body>
</html>