<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Jurusan Baru') }}
        </h2>
    </x-slot>

    <div class="py-2">
        <div class="max-w-7xl mx-auto">
            {{-- Alert Error --}}
            @if ($errors->any())
            <div class="max-w-xl mb-4 bg-red-600 text-white p-3 rounded-md shadow-sm">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Container Form -->
            <div class="bg-white overflow-hidden p-2 max-w-xl">
                <div class="mb-2 pb-2">                    
                    <p class="text-sm text-gray-500">Daftarkan kode dan nama program studi baru ke dalam sistem.</p>
                </div>

                <form action="{{ route('jurusan.store') }}" method="POST">
                    @csrf

                    <!-- Kode Jurusan -->
                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-bold text-gray-700">Kode Jurusan</label>
                        <input type="text" name="kode_jurusan" value="{{ old('kode_jurusan') }}"
                            class="w-full border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-200 rounded-md shadow-sm transition duration-200 @error('kode_jurusan') border-red-500 @enderror"
                            placeholder="Kode Jurusan" required>
                        @error('kode_jurusan')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nama Jurusan -->
                    <div class="mb-6">
                        <label class="block mb-2 text-sm font-bold text-gray-700">Nama Jurusan</label>
                        <input type="text" name="nama_jurusan" value="{{ old('nama_jurusan') }}"
                            class="w-full border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-200 rounded-md shadow-sm transition duration-200 @error('nama_jurusan') border-red-500 @enderror"
                            placeholder="Contoh: Teknik Informatika" required>
                        @error('nama_jurusan')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-between gap-4 pt-2">
                        <a href="{{ route('jurusan.index') }}" class="text-sm text-gray-600 hover:text-gray-900 underline">
                            Batal
                        </a>
                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2 px-4 rounded-md shadow transition duration-200">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
