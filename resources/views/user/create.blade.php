<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Registrasi User') }}
        </h2>
    </x-slot>

    <div class="py-2">
        <div class="max-w-7xl mx-auto">
            {{-- Alert Error --}}
            @if ($errors->any())
            <div class="max-w-xl mb-4" style="background: red; color: white; padding: 10px; border-radius: 4px;">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Container Form (Disamakan dengan Edit: max-w-xl & p-2) -->
            <div class="bg-white overflow-hidden p-2 max-w-xl">
                <div class="mb-2 pb-2">
                    <h3 class="text-lg font-bold text-gray-800">Registrasi User</h3>
                    <p class="text-sm text-gray-500">Silakan isi kredensial untuk membuat user baru.</p>
                </div>

                <form action="{{ route('user.store') }}" method="POST">
                    @csrf

                    <!-- Username / NIM -->
                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-bold text-gray-700">Username</label>
                        <input type="text" name="username" value="{{ old('username') }}"
                            class="w-full border-gray-300 focus:border-green-500 focus:ring focus:ring-green-200 rounded-md shadow-sm transition duration-200"
                            required>
                    </div>

                    <!-- Password -->
                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-bold text-gray-700">Password</label>
                        <input type="password" name="password"
                            class="w-full border-gray-300 focus:border-green-500 focus:ring focus:ring-green-200 rounded-md shadow-sm transition duration-200"
                            placeholder="••••••••" required>
                    </div>

                    <!-- Konfirmasi Password (Opsional tapi disarankan) -->
                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-bold text-gray-700">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation"
                            class="w-full border-gray-300 focus:border-green-500 focus:ring focus:ring-green-200 rounded-md shadow-sm transition duration-200"
                            placeholder="••••••••" required>
                    </div>

                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-bold text-gray-700">Daftar Sebagai</label>
                        <select name="role"
                            class="w-full border-gray-300 focus:border-green-500 focus:ring focus:ring-green-200 rounded-md shadow-sm transition duration-200"
                            required>
                             <option value="" disabled selected>Pilih Role...</option>
                            @foreach($roles as $role)
                                @if($role->role != 'Admin')
                                    <option value="{{ $role->id }}">{{ $role->role }}</option>
                                @endif
                            @endforeach
                        </select>
                        @error('role')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-between gap-4">
                        <a href="{{ route('user.index') }}" class="text-sm text-gray-600 hover:text-gray-900 underline">
                            Batal
                        </a>
                        <!-- Class button disamakan -->
                        <button type="submit" class="btn btn-primary mt-3">
                            Buat User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>