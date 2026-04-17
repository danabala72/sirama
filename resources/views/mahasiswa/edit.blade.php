<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Mahasiswa') }}
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
                    <p class="text-sm text-gray-500">Perbarui kredensial akses untuk user ini.</p>
                </div>

                <form action="{{ route('mahasiswa.update', $user->id) }}" method="POST">
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



                    <!-- Tambahkan Field Konfirmasi Ini -->
                    <div class="mb-4">
                        <label class="block mb-1 text-sm font-bold text-gray-700">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation"
                            class="w-full border-gray-300 focus:border-green-500 focus:ring focus:ring-green-200 rounded-md shadow-sm transition duration-200"
                            placeholder="••••••••">
                    </div>



                    @if(!empty($user->mahasiswa))
                    <!-- Keterangan Data Diri -->
                    <div class="mb-4 mt-6 border-b pb-1">
                        <h3 class="text-sm font-bold text-gray-800">Data Diri Mahasiswa</h3>
                    </div>

                    <!-- Nama Lengkap -->
                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-bold text-gray-700">Nama Lengkap</label>
                        <input type="text" value="{{ $user->mahasiswa->name }}"
                            class="w-full border-gray-300 bg-gray-100 text-gray-500 rounded-md shadow-sm cursor-not-allowed"
                            readonly>
                    </div>

                    <!-- Email -->
                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-bold text-gray-700">Email</label>
                        <input type="text" value="{{ $user->mahasiswa->email }}"
                            class="w-full border-gray-300 bg-gray-100 text-gray-500 rounded-md shadow-sm cursor-not-allowed"
                            readonly>
                    </div>

                    <!-- Tempat & Tanggal Lahir -->
                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-bold text-gray-700">Tempat, Tanggal Lahir</label>
                        <input type="text" value="{{ $user->mahasiswa->tempat_lahir }}, {{ \Carbon\Carbon::parse($user->mahasiswa->tgl_lahir)->format('d-m-Y') }}"
                            class="w-full border-gray-300 bg-gray-100 text-gray-500 rounded-md shadow-sm cursor-not-allowed"
                            readonly>
                    </div>

                    <!-- No HP -->
                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-bold text-gray-700">Nomor HP</label>
                        <input type="text" value="{{ $user->mahasiswa->no_hp }}"
                            class="w-full border-gray-300 bg-gray-100 text-gray-500 rounded-md shadow-sm cursor-not-allowed"
                            readonly>
                    </div>

                    <!-- Alamat -->
                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-bold text-gray-700">Alamat Rumah</label>
                        <textarea class="w-full border-gray-300 bg-gray-100 text-gray-500 rounded-md shadow-sm cursor-not-allowed"
                            rows="2" readonly>{{ $user->mahasiswa->alamat_rumah }}</textarea>
                    </div>


                    <!-- Pilihan Asesor (Multiple Dropdown) -->
                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-bold text-gray-700">Pilih Asesor</label>
                        <select id="asesor-select" name="asesor_ids[]" multiple placeholder="Pilih asesor..." autocomplete="off">
                            @foreach($asesors as $asesor)
                            <option value="{{ $asesor->id }}"
                                data-username="{{ $asesor->user->username ?? '-' }}"
                                {{ in_array($asesor->id, $selectedAsesors ?? []) ? 'selected' : '' }}>
                                {{ $asesor->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    @endif

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

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const asesorSelect = document.querySelector('#asesor-select');

            if (window.TomSelect && asesorSelect) {
                new TomSelect(asesorSelect, {
                    plugins: ['remove_button'],
                    dropdownParent: 'body',
                    copyClassesToDropdown: true,
                    onItemAdd: function() {
                        this.setTextboxValue('');
                        this.refreshOptions();
                    },
                    render: {
                        item: function(data, escape) {
                            return `<div>${escape(data.text)} <span class="text-xs text-gray-500 ms-1">[${escape(data.username)}]</span></div>`;
                        },
                        dropdown: function() {
                            return '<div class="dropdown-menu ts-dropdown"></div>';
                        },
                        option: function(data, escape) {
                            return `<div>
                        <span class="text-dark">${escape(data.text)}</span>
                        <span class="fw-bold text-primary ms-1" style="font-size: 0.75rem;">[${escape(data.username)}]</span>
                    </div>`;
                        }
                    }
                });
            }
        });
    </script>
    <style>
        /* Memastikan border luar muncul seperti input Tabler */
        .ts-wrapper.single .ts-control,
        .ts-wrapper.multi .ts-control {
            border: 1px solid #dce1e7 !important;
            /* Border standar Tabler */
            border-radius: 4px !important;
            min-height: calc(1.5em + 0.75rem + 2px);
            /* Menyamakan tinggi dengan form-control */
            padding: 0.4375rem 0.75rem !important;
        }

        /* Memperbaiki posisi dropdown agar selalu di atas elemen lain */
        .ts-dropdown {
            z-index: 9999 !important;
            /* Nilai tinggi agar tidak tertutup */
            background: #ffffff !important;
            border: 1px solid #dce1e7 !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
            border-radius: 4px !important;
            margin-top: 2px !important;
        }

        /* Menghapus outline biru default browser */
        .ts-control:focus,
        .focus .ts-control {
            outline: none !important;
            border-color: #10b981 !important;
            /* Warna emerald sesuai tema Anda */
            box-shadow: 0 0 0 0.05rem #2563eb !important;
        }

        /* Merapikan tampilan opsi di dalam dropdown */
        .ts-dropdown .option {
            padding: 0.5rem 0.75rem !important;
        }

        .ts-dropdown .option.active {
            background-color: #f0fdf4 !important;
            /* Hijau sangat muda (emerald-50) */
            color: #059669 !important;
            /* Teks hijau emerald (emerald-600) */
            cursor: pointer !important;
        }

        /* Tambahan: Efek hover manual jika class active belum cukup */
        .ts-dropdown .option:hover {
            background-color: #f0fdf4 !important;
            color: #059669 !important;
        }

        /* Menghilangkan garis tepi biru default pada opsi */
        .ts-dropdown .option {
            outline: none !important;
            border: none !important;
        }
    </style>

</x-app-layout>