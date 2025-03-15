<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-900 dark:text-gray-200">
            {{ __('Change Password') }}
        </h2>
    </x-slot>

    <div class="flex items-center justify-center min-h-screen bg-gray-100 dark:bg-gray-900">
        <div class="w-full max-w-md p-6 bg-white dark:bg-gray-800 shadow-lg rounded-lg">
            <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-200 text-center mb-6">
                Change Your Password
            </h2>

            @if(session('success'))
                <div class="mb-4 text-green-500 text-sm font-semibold bg-green-100 p-2 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 text-red-500 text-sm font-semibold bg-red-100 p-2 rounded">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('update_password') }}" class="space-y-4">
                @csrf
                @method('PUT')

                <!-- Current Password -->
                <div>
                    <label for="current_password" class="block text-gray-700 dark:text-gray-300 font-medium">
                        Current Password
                    </label>
                    <input type="password" id="current_password" name="current_password" required
                        class="w-full px-4 py-2 mt-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                </div>

                <!-- New Password -->
                <div>
                    <label for="password" class="block text-gray-700 dark:text-gray-300 font-medium">
                        New Password
                    </label>
                    <input type="password" id="password" name="password" required
                        class="w-full px-4 py-2 mt-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                </div>

                <!-- Confirm New Password -->
                <div>
                    <label for="password_confirmation" class="block text-gray-700 dark:text-gray-300 font-medium">
                        Confirm New Password
                    </label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                        class="w-full px-4 py-2 mt-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                </div>

                <div class="mt-6">
                    <button type="submit"
                        class="w-full px-4 py-2 text-white bg-blue-600 hover:bg-blue-700 rounded-md shadow-md transition duration-200 font-semibold">
                        Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
