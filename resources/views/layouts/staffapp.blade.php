<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex">

    <aside class="w-64 bg-gray-800 text-white h-screen p-6">
        <h2 class="text-2xl font-bold mb-6">Procurement System</h2>
        <ul>
            <li class="mb-4"><a href="{{ route('staff.dashboard') }}" class="hover:underline">Dashboard</a></li>
            <li class="mb-4"><a href="{{ route('staff.requests.create') }}" class="hover:underline">Create Request</a></li>
            <li class="mb-4"><a href="{{ route('profile.edit') }}" class="hover:underline">Profile</a></li>
            <li><a href="{{ route('logout') }}" class="text-red-400 hover:underline">Logout</a></li>
        </ul>
    </aside>

    <main class="flex-1 p-6">
        @yield('content')
   
