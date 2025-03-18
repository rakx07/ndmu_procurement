<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrator Dashboard</title>

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
            margin-left: 12px;
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
            top: 15px;
            left: 250px;
            z-index: 50;
        }
        .sidebar.collapsed + .toggle-btn {
            left: 90px;
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
                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-3 py-2 hover:bg-gray-700 rounded">
                    <i class="fas fa-home w-6 text-center"></i> <span class="nav-text">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.pending_requests') }}" class="flex items-center px-3 py-2 hover:bg-gray-700 rounded">
                    <i class="fas fa-clock w-6 text-center"></i> 
                    <span class="nav-text">Pending Requests</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.approved_requests') }}" class="flex items-center px-3 py-2 hover:bg-gray-700 rounded">
                    <i class="fas fa-check-circle w-6 text-center"></i> 
                    <span class="nav-text">Approved Requests</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.rejected_requests') }}" class="flex items-center px-3 py-2 hover:bg-gray-700 rounded">
                    <i class="fas fa-times-circle w-6 text-center"></i> 
                    <span class="nav-text">Rejected Requests</span>
                </a>
            </li>
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

    <!-- ✅ Toggle Button -->
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
