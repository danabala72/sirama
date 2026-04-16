<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Asesor') }}
        </h2>
    </x-slot>

    <div class="py-2">
        <div class="max-w-7xl mx-auto">
            {{-- Alert --}}
            @if(session('success'))
            <div class="max-w-xl mb-4 bg-green-500 text-white p-3 rounded shadow">
                {{ session('success') }}
            </div>
            @endif

            @if ($errors->any())
            <div class="max-w-xl mb-4 bg-red-500 text-white p-3 rounded shadow">
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
                    <p class="text-sm text-gray-500">Perbarui profil dan kredensial akses untuk user ini.</p>
                </div>

                <form action="{{ route('asesor.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')


                    <h3>Login User</h3>
                    <!-- Username -->
                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-bold text-gray-700">Username</label>
                        <input type="text" name="username" value="{{ old('username', $user->username) }}"
                            class="w-full border-gray-300 focus:border-green-500 focus:ring focus:ring-green-200 rounded-md shadow-sm transition duration-200"
                            required>
                    </div>

                    <!-- Password -->
                    <div class="mb-4">
                        <label class="block mb-1 text-sm font-bold text-gray-700">Password Baru</label>
                        <input type="password" name="password" autocomplete="new-password"
                            class="w-full border-gray-300 focus:border-green-500 focus:ring focus:ring-green-200 rounded-md shadow-sm transition duration-200 @error('password') border-red-500 @enderror"
                            placeholder="••••••••">

                        @error('password')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror

                        <p class="mt-2 text-xs text-amber-600 flex items-center">
                            <svg xmlns="http://w3.org" class="inline-block w-3 h-3 mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                            </svg>
                            Biarkan kosong jika tidak ingin mengganti password.
                        </p>
                    </div>

                    <!-- Konfirmasi Password -->
                    <div class="mb-4">
                        <label class="block mb-1 text-sm font-bold text-gray-700">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation"
                            class="w-full border-gray-300 focus:border-green-500 focus:ring focus:ring-green-200 rounded-md shadow-sm transition duration-200"
                            placeholder="••••••••">
                    </div>

                    <hr class="my-3 border-gray-200">
                    <h3>Profil</h3>
                    <!-- Nama Lengkap -->
                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-bold text-gray-700">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name', $user->asesor->name ?? '') }}"
                            class="w-full border-gray-300 focus:border-green-500 focus:ring focus:ring-green-200 rounded-md shadow-sm transition duration-200"
                            required>
                    </div>

                    <!-- Email -->
                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-bold text-gray-700">Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->asesor->email ?? '') }}"
                            class="w-full border-gray-300 focus:border-green-500 focus:ring focus:ring-green-200 rounded-md shadow-sm transition duration-200"
                            placeholder="email@contoh.com">
                    </div>

                    <!-- Jenis Kelamin -->
                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-bold text-gray-700">Jenis Kelamin</label>
                        <select name="jenis_kelamin"
                            class="w-full border-gray-300 focus:border-green-500 focus:ring focus:ring-green-200 rounded-md shadow-sm transition duration-200">
                            <option value="">-- Pilih Jenis Kelamin --</option>
                            <option value="L" {{ old('jenis_kelamin', $user->asesor->jenis_kelamin ?? '') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin', $user->asesor->jenis_kelamin ?? '') == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>

                    <!-- No HP -->
                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-bold text-gray-700">No. HP</label>
                        <input type="text" name="no_hp" value="{{ old('no_hp', $user->asesor->no_hp ?? '') }}"
                            class="w-full border-gray-300 focus:border-green-500 focus:ring focus:ring-green-200 rounded-md shadow-sm transition duration-200"
                            placeholder="0812...">
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-between gap-4 pt-4">
                        <a href="{{ route('asesor.index') }}" class="text-sm text-gray-600 hover:text-gray-900 underline">
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