<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Registrasi Asesor') }}
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
                    <p class="text-sm text-gray-500">Silakan isi kredensial untuk membuat user baru.</p>
                </div>

                <form action="{{ route('asesor.store') }}" method="POST">
                    @csrf

                    <h3 class="mb-2">Login User</h3>
                    <!-- Username / NIM -->
                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-bold text-gray-700">Username</label>
                        <input type="text" name="username" autocomplete="off" value="{{ old('username') }}"
                            class="w-full border-gray-300 focus:border-green-500 focus:ring focus:ring-green-200 rounded-md shadow-sm transition duration-200"
                            required>
                    </div>

                    <!-- Password -->
                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-bold text-gray-700">Password</label>
                        <input type="password" name="password" autocomplete="new-password"
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

                    <hr class="my-3 border-gray-200">
                    <h3 class="">Profil</h3>
                    <!-- Nama Lengkap -->
                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-bold text-gray-700">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name') }}"
                            class="w-full border-gray-300 focus:border-green-500 focus:ring focus:ring-green-200 rounded-md shadow-sm transition duration-200"
                            required>
                    </div>

                    <!-- Email -->
                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-bold text-gray-700">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                            class="w-full border-gray-300 focus:border-green-500 focus:ring focus:ring-green-200 rounded-md shadow-sm transition duration-200"
                            placeholder="email@contoh.com">
                    </div>

                    <!-- Jenis Kelamin -->
                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-bold text-gray-700">Jenis Kelamin</label>
                        <select name="jenis_kelamin"
                            class="w-full border-gray-300 focus:border-green-500 focus:ring focus:ring-green-200 rounded-md shadow-sm transition duration-200">
                            <option value="">-- Pilih Jenis Kelamin --</option>
                            <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>

                    <!-- No HP -->
                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-bold text-gray-700">No. HP</label>
                        <input type="text" name="no_hp" value="{{ old('no_hp') }}"
                            class="w-full border-gray-300 focus:border-green-500 focus:ring focus:ring-green-200 rounded-md shadow-sm transition duration-200"
                            placeholder="081234567890">
                    </div>


                    <!-- Action Buttons -->
                    <div class="flex items-center justify-between gap-4">
                        <a href="{{ route('asesor.index') }}" class="text-sm text-gray-600 hover:text-gray-900 underline">
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