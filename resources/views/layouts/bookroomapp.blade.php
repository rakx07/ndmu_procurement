<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Book Room | {{ config('app.name', 'Laravel') }}</title>

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
            position: fixed;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .sidebar .nav-link {
            color: white;
            padding: 12px 15px;
            display: flex;
            align-items: center;
        }
        .sidebar .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
        }
        .sidebar .nav-link.active {
            background: #28a745;
            font-weight: bold;
            border-radius: 5px;
        }
        .content {
            margin-left: 240px;
            padding: 20px;
            flex-grow: 1;
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <nav class="sidebar">
        <div>
            <ul class="nav flex-column mt-4">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('bookroom.dashboard') ? 'active' : '' }}" href="{{ route('bookroom.dashboard') }}">
                        <i class="fas fa-home"></i> <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}" href="{{ route('profile.edit') }}">
                        <i class="fas fa-user"></i> <span>Profile</span>
                    </a>
                </li>
            </ul>
        </div>
        <div class="mt-auto p-3">
            <a class="nav-link text-danger" href="{{ route('logout') }}" 
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </div>
    </nav>

    <!-- Page Content -->
    <div class="content">
        @yield('content')
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
