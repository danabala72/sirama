<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Jurusan') }}
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

            @if(session('error'))
            <div class="max-w-xl mb-4 bg-red-500 text-white p-3 rounded-lg shadow-sm">
                {{ session('error') }}
            </div>
            @endif

            @if ($errors->any())
            <div class="max-w-xl mb-4 bg-red-500 text-white p-3 rounded-lg shadow-sm">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            @if(Auth::user()->role->role === 'Admin' || Auth::user()->role->role === 'AdminJurusan')
            <div class="bg-white overflow-hidden p-2 max-w-xl">
                <div class="mb-2">
                    <p class="text-sm text-gray-500">Sesuaikan informasi kode dan nama program studi.</p>
                </div>

                <form action="{{ route('jurusan.update', $jurusan->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Kode Jurusan (Unique) -->
                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-bold text-gray-700">Kode Jurusan</label>
                        <input type="text" name="kode_jurusan"
                            value="{{ old('kode_jurusan', $jurusan->kode_jurusan) }}"
                            class="w-full border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-200 rounded-md shadow-sm @error('kode_jurusan') border-red-500 @enderror"
                            required>
                        @error('kode_jurusan')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nama Jurusan -->
                    <div class="mb-6">
                        <label class="block mb-2 text-sm font-bold text-gray-700">Nama Jurusan</label>
                        <input type="text" name="nama_jurusan"
                            value="{{ old('nama_jurusan', $jurusan->nama_jurusan) }}"
                            class="w-full border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-200 rounded-md shadow-sm @error('nama_jurusan') border-red-500 @enderror"
                            required>
                        @error('nama_jurusan')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <!-- Ketua Jurusan -->
                    <div class="mb-6">
                        <label class="block mb-2 text-sm font-bold text-gray-700">Ketua Jurusan</label>
                        <input type="text" name="ketua_jurusan"
                            value="{{ old('ketua_jurusan', $jurusan->ketua_jurusan) }}"
                            class="w-full border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-200 rounded-md shadow-sm @error('ketua_jurusan') border-red-500 @enderror">
                        @error('ketua_jurusan')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between pt-4">
                        <div></div>
                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2 px-4 rounded-md shadow transition duration-200">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
            @endif
            <div class="flex gap-2">
                <!-- Tombol Trigger Modal -->
                <button type="button" class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2 px-4 rounded-md shadow transition duration-200" data-bs-toggle="modal" data-bs-target="#modalTambahMK">
                    + Tambah MK
                </button>

                <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-md shadow transition duration-200" data-bs-toggle="modal" data-bs-target="#modalTambahSkema">
                    + Tambah Skema
                </button>

            </div>

            <!-- Daftar Skema -->
            @if(isset($skemas) && $skemas->count() > 0)
            <div class="mt-6">
                <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider mb-2">Daftar Skema</h3>
                <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-bold text-gray-600 uppercase">Nama Skema</th>
                                <th class="px-4 py-2 text-left text-xs font-bold text-gray-600 uppercase">Deskripsi</th>
                                <th class="px-4 py-2 text-center text-xs font-bold text-gray-600 uppercase w-1">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach($skemas as $skema)
                            <tr>
                                <td class="px-4 py-2 text-sm font-medium text-gray-900">{{ $skema->nama_skema }}</td>
                                <td class="px-4 py-2 text-sm text-gray-600">{{ $skema->deskripsi ?? '-' }}</td>
                                <td class="px-4 py-2 text-sm text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEditSkema" data-id="{{ $skema->id }}">
                                            <svg xmlns="http://www.w3.org/2000/2000" class="icon icon-tabler icon-tabler-edit" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M12 15l8.385 -8.415a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3z" />
                                                <path d="M16 5l3 3" />
                                                <path d="M9 7.07a7 7 0 0 0 1 13.93a7 7 0 0 0 6.929 -6" />
                                            </svg>
                                            Edit
                                        </button>
                                        <form action="{{ route('skema.destroy', $skema->id) }}" method="POST" class="m-0" onsubmit="return confirm('Yakin hapus skema ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger d-flex align-items-center justify-content-center" style="height: 32px; width: 32px;">
                                                <svg xmlns="http://www.w3.org/2000/2000" class="icon icon-tabler icon-tabler-trash m-0" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M4 7l16 0" />
                                                    <path d="M10 11l0 6" />
                                                    <path d="M14 11l0 6" />
                                                    <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                    <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
            <!-- Daftar Mata Kuliah Terkait -->
            @if(!empty($jurusan->mataKuliah) && $jurusan->mataKuliah->count() > 0 )
            <div class="mt-10 pt-3">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider mb-0">Daftar Mata Kuliah</h3>

                    <span class="bg-emerald-100 text-emerald-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">
                        {{ $jurusan->mataKuliah->count() }} Mata Kuliah
                    </span>
                </div>
                <div class="bg-gray-50 rounded-lg border border-gray-200 overflow-hidden">
                    <!-- Tambahkan pembungkus overflow-x-auto di sini -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <!-- Tambahkan text-nowrap agar judul kolom tidak terpotong (line break) -->
                                    <th class="px-4 py-2 text-left text-xs font-bold text-gray-600 uppercase text-nowrap">Kode</th>
                                    <th class="px-4 py-2 text-left text-xs font-bold text-gray-600 uppercase text-nowrap">Nama MK</th>
                                    <th class="px-4 py-2 text-center text-xs font-bold text-gray-600 uppercase text-nowrap">Semester</th>
                                    <th class="px-4 py-2 text-center text-xs font-bold text-gray-600 uppercase text-nowrap">SKS</th>
                                    <th class="px-4 py-2 text-center text-xs font-bold text-gray-600 uppercase text-nowrap">Nilai Minimum</th>
                                    <th class="px-4 py-2 text-center text-xs font-bold text-gray-600 uppercase text-nowrap">Aksi</th>

                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @forelse($jurusan->mataKuliah as $mk)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-2 text-sm font-mono text-emerald-700 text-nowrap">{{ $mk->kode_mk }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-700">{{ $mk->nama_mk }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-700">
                                        @foreach($mk->semester as $smt)
                                        <span class="badge badge-outline text-purple">{{ $smt->label }}</span>
                                        @endforeach
                                    </td>
                                    <td class="px-4 py-2 text-sm text-center text-gray-600">{{ $mk->sks }}</td>
                                    <td class="px-4 py-2 text-sm text-center text-gray-600">{{ $mk->nilai_minimum ?? '-' }}</td>
                                    <td class="px-4 py-2 text-sm text-center">
                                        <div class="flex justify-center items-center gap-2">
                                            <!-- Tombol Edit MK -->
                                            <a href="{{ route('mk.edit', $mk->id) }}" class="text-emerald-600 hover:text-emerald-900 transition" title="Edit Mata Kuliah">
                                                <svg xmlns="http://w3.org" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>

                                            <!-- Tombol Hapus MK -->
                                            <form action="{{ route('mk.destroy', $mk->id) }}" method="POST" onsubmit="return confirm('Hapus mata kuliah ini?')" class="inline m-0">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700 transition" title="Hapus">
                                                    <svg xmlns="http://w3.org" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <!-- Jangan lupa naikkan colspan ke 4 karena kolom bertambah -->
                                    <td colspan="4" class="px-4 py-6 text-center text-sm text-gray-400 italic">
                                        Belum ada mata kuliah untuk jurusan ini.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>


                {{-- Shortcut ke Manajemen MK --}}
                <div class="flex items-center justify-between pt-4">
                    <a href="{{ route('jurusan.index') }}" class="text-sm text-gray-600 hover:underline">
                        Kembali
                    </a>
                    <a href="{{ route('mk.index', ['jurusan_id' => $jurusan->id]) }}"
                        class="text-xs text-emerald-600 hover:text-emerald-800 font-semibold flex items-center gap-1">
                        Kelola Mata Kuliah Selengkapnya
                        <svg xmlns="http://w3.org" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>
            @else
            <div class="flex items-center justify-between pt-4">
                <a href="{{ route('jurusan.index') }}" class="text-sm text-gray-600 hover:underline">
                    Kembali
                </a>
                <a href="{{ route('mk.index', ['jurusan_id' => $jurusan->id]) }}"
                    class="text-xs text-emerald-600 hover:text-emerald-800 font-semibold flex items-center gap-1">
                    Kelola Mata Kuliah Selengkapnya
                    <svg xmlns="http://w3.org" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
            @endif

        </div>
    </div>

    <div class="modal modal-blur fade" id="modalTambahMK" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="{{ route('mk.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="jurusan_id" value="{{ $jurusan->id }}">

                    <div class="modal-header">
                        <h5 class="modal-title">Tambah MK ke {{ $jurusan->nama_jurusan }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Kode MK -->
                        <div class="mb-3">
                            <label class="form-label font-bold">Kode Mata Kuliah</label>
                            <input type="text" name="kode_mk" class="form-control" placeholder="Contoh: TI101" required>
                        </div>
                        <!-- Nama MK -->
                        <div class="mb-3">
                            <label class="form-label font-bold">Nama Mata Kuliah</label>
                            <input type="text" name="nama_mk" class="form-control" placeholder="Nama Lengkap MK" required>
                        </div>
                         <div class="mb-4">
                        <label class="block mb-2 text-sm font-bold text-gray-700">Skema</label>
                        <select name="skema_id" class="w-full border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-200 rounded-md shadow-sm">
                            <option value="">-- Tanpa Skema --</option>
                            @if(isset($skemas))
                            @foreach($skemas as $skema)
                            <option value="{{ $skema->id }}">{{ $skema->nama_skema }}</option>
                            @endforeach
                            @endif
                        </select>
                    </div>
                        <div class="mb-4">
                        <label class="block mb-2 text-sm font-bold text-gray-700">Tawarkan pada Semester</label>
                        <select name="semester_id"
                            class="w-full border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-200 rounded-md shadow-sm select2"
                            data-placeholder="Pilih satu atau lebih semester...">
                            @foreach($semuaSemester as $smt)
                            <option value="{{ $smt->id }}"
                                 {{ old('semester_id') == $smt->id ? 'selected' : '' }}>
                                {{ $smt->label }} {{ $smt->is_active ? '(Aktif)' : '' }}
                            </option>
                            @endforeach
                        </select>
                        @error('semester_id')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="form-label font-bold">SKS</label>
                                    <input type="number" name="sks" class="form-control" min="1" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="form-label font-bold">Nilai Minimum</label>
                                    <input type="number" name="nilai_minimum" class="form-control" value="60" min="0" max="100">
                                </div>
                            </div>
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
</div>

    {{-- Modal Tambah Skema --}}
    <div class="modal modal-blur fade" id="modalTambahSkema" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="formTambahSkema" action="{{ route('skema.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="jurusan_id" value="{{ $jurusan->id ?? '' }}" required>
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Skema - {{ $jurusan->nama_jurusan ?? 'Jurusan' }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label font-bold">Nama Skema</label>
                            <input type="text" name="nama_skema" class="form-control" placeholder="Contoh: Skema A" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-bold">Deskripsi</label>
                            <textarea name="deskripsi" class="form-control" rows="3" placeholder="Deskripsi skema..."></textarea>
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

    {{-- Modal Edit Skema --}}
    <div class="modal modal-blur fade" id="modalEditSkema" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="formEditSkema" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="jurusan_id" id="editJurusanIdHidden">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Skema</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label font-bold">Nama Skema</label>
                            <input type="text" name="nama_skema" id="editNamaSkema" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-bold">Deskripsi</label>
                            <textarea name="deskripsi" id="editDeskripsi" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary ms-auto">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</x-app-layout>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modalEdit = document.getElementById('modalEditSkema');
    const formEdit = document.getElementById('formEditSkema');

    modalEdit.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const id = button.dataset.id;

        fetch(`/skema/${id}/edit`)
            .then(res => res.json())
            .then(data => {
                formEdit.action = `/skema/${id}`;
                document.getElementById('editNamaSkema').value = data.skema.nama_skema;
                document.getElementById('editDeskripsi').value = data.skema.deskripsi || '';
                document.getElementById('editJurusanIdHidden').value = data.skema.jurusan_id;
            });
    });
});
</script>