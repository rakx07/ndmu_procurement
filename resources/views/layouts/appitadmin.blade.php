<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
            display: flex;
        }
        .sidebar {
            width: 240px;
            height: 100vh;
            background: #343a40;
            transition: all 0.3s;
        }
        .sidebar.collapsed {
            width: 80px;
        }
        .sidebar .nav-link {
            color: white;
            display: flex;
            align-items: center;
            padding: 10px;
        }
        .sidebar .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
        }
        .sidebar .nav-link.active {
            background: #28a745; /* Active link color */
        }
        .sidebar .nav-link i {
            width: 30px;
            text-align: center;
        }
        .sidebar .nav-text {
            display: inline-block;
            transition: all 0.3s;
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
        }
    </style>
</head>
<body>
    <nav class="sidebar" id="sidebar">
        <button class="toggle-btn" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <i class="fas fa-home"></i> <span class="nav-text">Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('profile') ? 'active' : '' }}" href="{{ route('profile.edit') }}">
                    <i class="fas fa-user"></i> <span class="nav-text">Profile</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('user-management') ? 'active' : '' }}" href="{{ route('user.management') }}">
                    <i class="fas fa-users"></i> <span class="nav-text">Manage Users</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('settings') ? 'active' : '' }}" href="{{ route('settings') }}">
                    <i class="fas fa-cog"></i> <span class="nav-text">Settings</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-danger" href="{{ route('logout') }}" 
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i> <span class="nav-text">Logout</span>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </li>
        </ul>
    </nav>
    
    <div class="content p-4 flex-grow-1">
        @yield('content')
    </div>
</body>
</html>
