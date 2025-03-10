<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchasing App</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        .sidebar {
            width: 240px;
            transition: all 0.3s ease-in-out;
        }
        .sidebar.collapsed {
            width: 80px;
        }
        .sidebar .nav-text {
            margin-left: 12px; /* Ensure spacing between icon and text */
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
            top: 15px; /* Adjusted to align with sidebar */
            left: 250px; /* Moved closer to the yellow mark */
            z-index: 50;
        }
        .sidebar.collapsed + .toggle-btn {
            left: 90px; /* Adjusted position when collapsed */
        }
        .content {
            margin-left: 240px;
            transition: all 0.3s ease-in-out;
        }
        .content.collapsed {
            margin-left: 80px;
        }
    </style>
</head>

<body class="flex bg-gray-100">

    <!-- ✅ Sidebar -->
    <nav class="sidebar bg-gray-800 text-white h-screen fixed p-6 flex flex-col justify-between" id="sidebar">
        <ul class="space-y-4">
            <li>
                <a href="{{ route('staff.dashboard') }}" class="flex items-center px-3 py-2 hover:bg-gray-700 rounded">
                    <i class="fas fa-home w-6 text-center"></i> <span class="nav-text">Dashboard</span>
                </a>
            </li>
    <!--Added manage item-->

    <li class="nav-item">
                    <a class="nav-link" href="#" onclick="toggleSubMenu(event, 'userSubmenu')">
                        <i class="fas fa-users"></i> <span class="nav-text">Manage Items</span>
                        <i class="fas fa-chevron-down ms-auto"></i>
                    </a>
                    <ul id="userSubmenu" class="nav flex-column submenu">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('user.management') ? 'active' : '' }}" href="{{ route('user.management') }}">
                                <i class="fas fa-list"></i> <span class="nav-text">Item List</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('it-admin/create') ? 'active' : '' }}" href="{{ url('/it-admin/create') }}">
                                <i class="fas fa-user-plus"></i> <span class="nav-text">Add Item</span>
                            </a>
                        </li>
                    </ul>
    </li>

    <!--End-->
            <li>
                <a href="{{ route('profile.edit') }}" class="flex items-center px-3 py-2 hover:bg-gray-700 rounded">
                    <i class="fas fa-user w-6 text-center"></i> <span class="nav-text">Profile</span>
                </a>
            </li>
        </ul>
        <div class="mt-auto">
            <a href="{{ route('logout') }}" class="flex items-center px-3 py-2 text-red-400 hover:bg-gray-700 rounded"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt w-6 text-center"></i> <span class="nav-text">Logout</span>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                @csrf
            </form>
        </div>
    </nav>

    <!-- ✅ Toggle Button (Now Closer to Yellow Mark) -->
    <button class="toggle-btn text-white bg-gray-800 px-2 py-1 rounded" onclick="toggleSidebar()" id="toggleBtn">
        <i class="fas fa-bars"></i>
    </button>

    <!-- ✅ Main Content -->
    <main class="content flex-1 p-6 ml-[240px]" id="main-content">
        @yield('content')
    </main>

    <!-- ✅ JavaScript -->
    <script>
        function toggleSidebar() {
            let sidebar = document.getElementById("sidebar");
            let content = document.getElementById("main-content");
            let toggleBtn = document.getElementById("toggleBtn");

            sidebar.classList.toggle("collapsed");
            content.classList.toggle("collapsed");

            // Adjust button position dynamically
            if (sidebar.classList.contains("collapsed")) {
                toggleBtn.style.left = "90px"; 
            } else {
                toggleBtn.style.left = "250px";
            }
        }
    </script>

    <!-- Font Awesome for Icons -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
</body>
</html>
