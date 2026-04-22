<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Mata Kuliah') }}
        </h2>
    </x-slot>

    <div class="py-2">
        <div class="max-w-7xl mx-auto">
            {{-- Flash Message --}}
            @if(session('success'))
            <div class="max-w-xl mb-4 bg-emerald-500 text-white p-3 rounded-lg shadow-sm">
                {{ session('success') }}
            </div>
            @endif

            <div class="bg-white overflow-hidden p-2 max-w-xl">
                <div class="mb-4">
                    <p class="text-sm text-gray-500">Perbarui informasi kurikulum dan kriteria penilaian mata kuliah.</p>
                </div>

                <form action="{{ route('mk.update', $mk->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Jurusan (Readonly Info) -->
                    <div class="mb-4">
                        <label class="block mb-1 text-xs font-bold text-emerald-600 uppercase">Program Studi / Jurusan</label>
                        <div class="p-2 bg-emerald-50 border border-emerald-100 rounded text-sm text-emerald-800 font-semibold">
                            {{ $mk->jurusan->nama_jurusan }} ({{ $mk->jurusan->kode_jurusan }})
                        </div>
                        <input type="hidden" name="jurusan_id" value="{{ $mk->jurusan_id }}">
                    </div>

                    <!-- Kode MK -->
                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-bold text-gray-700">Kode Mata Kuliah</label>
                        <input type="text" name="kode_mk"
                            value="{{ old('kode_mk', $mk->kode_mk) }}"
                            class="w-full border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-200 rounded-md shadow-sm @error('kode_mk') border-red-500 @enderror"
                            required>
                        @error('kode_mk')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nama MK -->
                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-bold text-gray-700">Nama Mata Kuliah</label>
                        <input type="text" name="nama_mk"
                            value="{{ old('nama_mk', $mk->nama_mk) }}"
                            class="w-full border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-200 rounded-md shadow-sm @error('nama_mk') border-red-500 @enderror"
                            required>
                        @error('nama_mk')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-bold text-gray-700">Tawarkan pada Semester</label>
                        <select name="semester_id"
                            class="w-full border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-200 rounded-md shadow-sm select2"
                            data-placeholder="Pilih satu atau lebih semester...">
                            @foreach($semuaSemester as $smt)
                            <option value="{{ $smt->id }}"
                                {{ in_array($smt->id, old('semester_id', $mk->semester->pluck('id')->toArray())) ? 'selected' : '' }}>
                                {{ $smt->label }} {{ $smt->is_active ? '(Aktif)' : '' }}
                            </option>
                            @endforeach
                        </select>
                        @error('semester_id')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex gap-4 mb-6">
                        <!-- Kolom SKS -->
                        <div class="w-full">
                            <label class="block mb-2 text-sm font-bold text-gray-700">SKS</label>
                            <input type="number" name="sks"
                                value="{{ old('sks', $mk->sks) }}"
                                class="w-full border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-200 rounded-md shadow-sm"
                                required min="1">
                        </div>

                        <!-- Kolom Nilai Minimum -->
                        <div class="w-full">
                            <label class="block mb-2 text-sm font-bold text-gray-700 whitespace-nowrap">Nilai Minimum</label>
                            <input type="number" name="nilai_minimum"
                                value="{{ old('nilai_minimum', $mk->nilai_minimum) }}"
                                class="w-full border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-200 rounded-md shadow-sm"
                                placeholder="60">
                        </div>
                    </div>



                    <div class="flex items-center justify-between pt-4">
                        <a href="{{ route('mk.index', ['jurusan_id' => $mk->jurusan_id]) }}" class="text-sm text-gray-600 hover:underline">
                            Kembali
                        </a>
                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2 px-6 rounded-md shadow transition duration-200">
                            Update
                        </button>
                    </div>
                </form>
            </div>

            <div class="flex items-center justify-between my-4">
                <div class="flex gap-2">
                    <button type="button" data-bs-toggle="modal" data-bs-target="#modalTambahCp" class="text-xs btn btn-sm btn-outline-success">
                        + Tambah CPMK
                    </button>
                    <!-- Tombol Download Template -->
                    <a href="{{ route('mk.cpmk.template',  $mk->kode_mk) }}" class="text-xs btn btn-sm btn-outline-success">
                        Template CPMK
                    </a>
                    <!-- Tombol Trigger Modal Import -->
                    <button type="button" data-bs-toggle="modal" data-bs-target="#modalImportCp" class="text-xs btn btn-sm btn-primary">
                        Import CPMK
                    </button>
                </div>
            </div>


            <!-- Bagian Capaian Pembelajaran (CP) -->
            @if($mk->cps->count() > 0)
            <div class="mt-10 pt-6">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider mb-0">Indikator Capaian Kompetensi</h3>
                    <span class="bg-emerald-100 text-emerald-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">
                        {{ $mk->cps->count() }} Indikator
                    </span>
                </div>

                <div class="bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase w-1">No</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Deskripsi Indikator</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase w-1">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($mk->cps as $index => $cp)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3 text-sm text-gray-400 font-mono">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700 leading-relaxed italic">
                                    "{{ $cp->indikator_capaian }}"
                                </td>
                                <td class="px-4 py-3 text-sm text-center">
                                    <div class="flex justify-center items-center gap-2">

                                        <!-- Tombol Edit: Memicu Modal Berdasarkan ID CP -->
                                        <a href="{{ route('mk.edit', $mk->id) }}" class="text-emerald-600 hover:text-emerald-900 transition" data-bs-toggle="modal" data-bs-target="#modalEditCp{{ $cp->id }}" title="Edit CPMK">
                                            <svg xmlns="http://w3.org" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <!-- Tombol Delete -->
                                        <form action="{{ route('cpmk.destroy', $cp->id) }}" method="POST" onsubmit="return confirm('Hapus indikator ini?')" class="m-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700">
                                                <svg xmlns="http://w3.org" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>

                                    <!-- Modal Edit (Diletakkan di dalam looping agar data terikat langsung) -->
                                    <div class="modal modal-blur fade" id="modalEditCp{{ $cp->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <form action="{{ route('cpmk.update', $cp->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-header">
                                                        <h5 class="modal-title text-left">Edit Indikator Capaian</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body text-left">
                                                        <div class="mb-3">
                                                            <label class="form-label font-bold text-gray-700">Deskripsi Indikator</label>
                                                            <textarea name="indikator_capaian" class="form-control" rows="4" required>{{ $cp->indikator_capaian }}</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-primary ms-auto">Update Indikator</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-4 py-8 text-center text-sm text-gray-400 italic">Belum ada indikator capaian.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>

                </div>


            </div>
            @endif

        </div>
    </div>
    <div class="modal modal-blur fade" id="modalImportCp" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="{{ route('mk.cpmk.import', $mk->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Import CPMK: {{ $mk->nama_mk }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Pilih File CSV</label>
                            <input type="file" name="file" class="form-control" required accept=".csv">
                            <small class="text-muted d-block mt-2">
                                Format kolom CSV: <strong>kode_mk, indikator_capaian</strong>
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Proses Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Modal Tambah CPMK -->
    <div class="modal modal-blur fade" id="modalTambahCp" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <!-- Ganti route ke fungsi store CPMK Anda -->
                <form action="{{ route('cpmk.store') }}" method="POST">
                    @csrf
                    <!-- Input Hidden untuk mengunci ke Mata Kuliah ini -->
                    <input type="hidden" name="mata_kuliah_id" value="{{ $mk->id }}">

                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Indikator Capaian</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label font-bold text-gray-700">Deskripsi Indikator Baru</label>
                            <textarea name="indikator_capaian" class="form-control" rows="4"
                                placeholder="Contoh: Mahasiswa mampu menganalisis kompleksitas algoritma..." required></textarea>
                            <p class="mt-2 text-xs text-muted italic">Tuliskan kompetensi spesifik yang akan dicapai mahasiswa.</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary ms-auto">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


</x-app-layout>