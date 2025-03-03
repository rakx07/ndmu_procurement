<x-app-layout>
    <div class="flex items-center justify-center min-h-screen bg-gray-100 dark:bg-gray-900">
        <div class="w-full max-w-md p-6 bg-white dark:bg-gray-800 shadow-lg rounded-lg">
            <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-200 text-center mb-6">
                Change Your Password
            </h2>

            @if(session('error'))
                <div class="mb-4 text-red-500 text-sm font-semibold bg-red-100 p-2 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('change_password') }}" class="space-y-4">
                @csrf
                @method('PUT')

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

                <!-- Submit Button -->
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
