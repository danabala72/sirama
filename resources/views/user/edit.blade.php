<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit') }}
        </h2>
    </x-slot>

    <div class="py-2">
        <div class="max-w-7xl mx-auto ">
            @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
            @endif
            @if ($errors->any())
            <div style="background: red; color: white; padding: 10px; margin-bottom: 10px;">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            <!-- Container Form -->
            <div class="bg-white overflow-hidden p-2 max-w-xl">
                <div class="mb-2 pb-2">
                    <h3 class="text-lg font-bold text-gray-800">Informasi Login</h3>
                    <p class="text-sm text-gray-500">Perbarui kredensial akses untuk user ini.</p>
                </div>

                <form action="{{ route('user.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Username / NIM -->
                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-bold text-gray-700">Username</label>
                        <input type="text" name="username" value="{{ $user->username }}"
                            class="w-full border-gray-300 focus:border-green-500 focus:ring focus:ring-green-200 rounded-md shadow-sm transition duration-200"
                            required>
                    </div>

                    <!-- Password -->
                    <div class="mb-4">
                        <label class="block mb-1 text-sm font-bold text-gray-700">Password Baru</label>
                        <input type="password" name="password"
                            class="w-full border-gray-300 focus:border-green-500 focus:ring focus:ring-green-200 rounded-md shadow-sm transition duration-200"
                            placeholder="••••••••">
                        <p class="mt-2 text-xs text-amber-600 flex items-center">
                            <svg xmlns="http://w3.org" class="inline-block w-3 h-3 mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                            </svg>
                            Biarkan kosong jika tidak ingin mengganti password.
                        </p>
                    </div>

                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-bold text-gray-700">Role</label>
                        <select name="role"
                            class="w-full border-gray-300 focus:border-green-500 focus:ring focus:ring-green-200 rounded-md shadow-sm transition duration-200"
                            required>
                            <option value="" disabled>Pilih Role...</option>
                            @foreach($roles as $role)
                            @if($role->role != 'Admin')
                            <option value="{{ $role->id }}"
                                {{ (old('role', $user->role_id) == $role->id) ? 'selected' : '' }}>
                                {{ $role->role }}
                            </option>
                            @endif
                            @endforeach
                        </select>
                        @error('role')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>


                    <!-- Action Buttons -->
                    <div class="flex items-center justify-between gap-4">
                        <a href="{{ url()->previous() }}" class="text-sm text-gray-600 hover:text-gray-900 underline">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary mt-3">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>