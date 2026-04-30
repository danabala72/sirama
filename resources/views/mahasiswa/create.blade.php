<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Registrasi Mahasiswa') }}
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

                <form action="{{ route('mahasiswa.store') }}" method="POST">
                    @csrf

                    <!-- Username / NIM -->
                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-bold text-gray-700">Username</label>
                        <input type="text" autocomplete="disabled" name="username" value="{{ old('username') }}"
                            class="w-full border-gray-300 focus:border-green-500 focus:ring focus:ring-green-200 rounded-md shadow-sm transition duration-200"
                            placeholder="Username..."
                            required>
                    </div>

                    <!-- Password -->
                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-bold text-gray-700">Password</label>
                        <input type="password" autocomplete="new-password" name="password"
                            class="w-full border-gray-300 focus:border-green-500 focus:ring focus:ring-green-200 rounded-md shadow-sm transition duration-200"
                            placeholder="••••••••" required>
                    </div>

                    <!-- Konfirmasi Password (Opsional tapi disarankan) -->
                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-bold text-gray-700">Konfirmasi Password</label>
                        <input type="password" autocomplete="new-password" name="password_confirmation"
                            class="w-full border-gray-300 focus:border-green-500 focus:ring focus:ring-green-200 rounded-md shadow-sm transition duration-200"
                            placeholder="••••••••" required>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-between gap-4">
                        <a href="{{ route('mahasiswa.index') }}" class="text-sm text-gray-600 hover:text-gray-900 underline">
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