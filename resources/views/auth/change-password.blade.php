<form method="POST" action="{{ route('change_password') }}">
    @csrf
    @method('PUT')  <!-- ✅ Add this line to use PUT method -->

    <div>
        <label for="password" class="block text-gray-700">New Password</label>
        <input type="password" id="password" name="password" required class="w-full mt-2 p-2 border rounded">
    </div>

    <div class="mt-4">
        <label for="password_confirmation" class="block text-gray-700">Confirm New Password</label>
        <input type="password" id="password_confirmation" name="password_confirmation" required class="w-full mt-2 p-2 border rounded">
    </div>

    <div class="mt-4">
        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">
            Update Password
        </button>
    </div>
</form>
