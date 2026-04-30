<x-app-layout>

    <div class="container-xl p-2">
        <!-- Header -->

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="text-2xl font-bold text-gray-800">Daftar Mata Kuliah</h2>
        </div>

        <!-- Alert Success -->
        @if(session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            <div class="d-flex">
                <div>{{ session('success') }}</div>
            </div>
            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
        </div>
        @endif

        <div class="d-flex flex-wrap gap-2 mb-3">
            <button type="button" class="btn btn-outline-info btn-sm inline-flex items-center gap-x-2 my-2" data-bs-toggle="modal" data-bs-target="#modalTambahMK">
                <svg xmlns="http://w3.org" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span class="hidden sm:inline">Tambah MK</span>
            </button>


            <button type="button" class="inline-flex items-center gap-x-2 bg-indigo-600 hover:bg-indigo-700 btn btn-sm btn-outline-success my-2" data-bs-toggle="modal" data-bs-target="#modalImport">
                <svg xmlns="http://w3.org" class="w-4 h-4" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                    <path d="M5 13v-8a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2h-5.5" />
                    <path d="M2 19h7" />
                    <path d="M7 16l3 3l-3 3" />
                </svg>
                Import Mata Kuliah
            </button>
        </div>
        <!-- Card Wrapper -->
        <!-- Card Wrapper -->
        <div class="card shadow-sm">
            <div class="table-responsive">
                <table id="mataKuliahTable" class="table table-vcenter card-table table-hover">
                    <thead>
                        <tr class="bg-gray-100 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            <th class="text-nowrap w-1">No</th>
                            <th class="text-nowrap">Jurusan</th>
                            <th class="text-nowrap">Kode MK</th>
                            <th class="text-nowrap">Nama Mata Kuliah</th>
                            <th class="text-nowrap">Semester</th>
                            <th class="text-nowrap text-center">SKS</th>
                            <th class="text-nowrap text-center">Nilai Min.</th>
                            <th class="text-nowrap text-center">Aktif</th>
                            <th class="text-nowrap text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($mks as $index => $mk)
                        <tr class="transition">
                            <td class="text-sm text-muted">{{ $index + 1 }}</td>
                            <td class="text-sm">
                                <div class="font-bold text-emerald-600">{{ $mk->jurusan->nama_jurusan ?? '-' }}</div>
                            </td>
                            <td class="text-sm font-medium">
                                <span class="badge bg-blue-lt">{{ $mk->kode_mk }}</span>
                            </td>
                            <td class="text-sm font-medium text-gray-900">{{ $mk->nama_mk }}</td>
                            <td class="text-sm">
                                @forelse($mk->semester as $smt)
                                <span class="badge badge-outline text-purple small">{{ $smt->label }}</span>
                                @empty
                                <span class="text-muted small italic">Belum diset</span>
                                @endforelse
                            </td>
                            <td class="text-sm text-center">{{ $mk->sks }}</td>
                            <td class="text-sm text-center">
                                <span class="text-muted">{{ $mk->nilai_minimum ?? '60' }}</span>
                            </td>

                            <td class="text-sm text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <form action="{{ route('mk.toggle-status', $mk->id) }}" method="POST" class="m-0">
                                        @csrf
                                        @method('PATCH')
                                        <div class="form-check form-switch m-0">
                                            <input class="form-check-input" type="checkbox" role="switch"
                                                id="switch{{ $mk->id }}"
                                                {{ $mk->status ? 'checked' : '' }}
                                                onchange="this.form.submit()"
                                                style="cursor: pointer; width: 35px; height: 18px;">
                                        </div>
                                    </form>
                                </div>
                            </td>
                            <td class="text-sm text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <!-- Edit Button -->
                                    <a href="{{ route('mk.edit', $mk->id) }}" class="btn btn-sm btn-outline-primary">
                                        <svg xmlns="http://w3.org" class="icon icon-tabler icon-tabler-edit" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M12 15l8.385 -8.415a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3z" />
                                            <path d="M16 5l3 3" />
                                            <path d="M9 7.07a7 7 0 0 0 1 13.93a7 7 0 0 0 6.929 -6" />
                                        </svg>
                                        Edit
                                    </a>

                                    <!-- Delete Button -->
                                    <form action="{{ route('mk.destroy', $mk->id) }}" method="POST" onsubmit="return confirm('Hapus mata kuliah ini?')" class="m-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger d-flex align-items-center justify-content-center" style="height: 32px; width: 32px;">
                                            <svg xmlns="http://w3.org" class="icon icon-tabler icon-tabler-trash m-0" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
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
                        @empty
                        <tr>
                            <td colspan="7" class="px-5 py-10 text-center text-gray-500 italic">Belum ada data mata kuliah.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="modal modal-blur fade" id="modalTambahMK" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form action="{{ route('mk.store') }}" method="POST">
                        @csrf
                        <!-- Input Hidden untuk mengunci Jurusan -->


                        <div class="modal-header">
                            <h5 class="modal-title">Tambah Mata Kuliah </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label font-bold">Jurusan</label>
                                @if(Auth::user()->role->role === 'AdminJurusan')
                                <!-- Jika Admin Jurusan, kunci ke jurusannya sendiri -->
                                <input type="hidden" name="jurusan_id" value="{{ Auth::user()->jurusan_id }}">
                                <input type="text" class="form-control bg-light" value="{{ Auth::user()->jurusan->nama_jurusan }}" readonly>
                                @else
                                <!-- Jika Super Admin, tampilkan pilihan semua jurusan -->
                                <select name="jurusan_id" class="form-select @error('jurusan_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Jurusan --</option>
                                    @foreach($semuaJurusan as $j)
                                    <option value="{{ $j->id }}" {{ old('jurusan_id') == $j->id ? 'selected' : '' }}>
                                        {{ $j->nama_jurusan }}
                                    </option>
                                    @endforeach
                                </select>
                                @endif
                                @error('jurusan_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
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
                                <label class="block mb-2 text-sm font-bold text-gray-700">Tawarkan pada Semester</label>
                                <select name="semester_id"
                                    class="w-full border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-200 rounded-md shadow-sm select2"
                                    data-placeholder="Pilih satu atau lebih semester...">
                                    @foreach($semuaSemester as $smt)
                                    <option value="{{ $smt->id }}"
                                        {{ in_array($smt->id, old('semester_id', [])) ? 'selected' : '' }}>
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

    <div class="modal modal-blur fade" id="modalImport" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="{{ route('mk.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Import Data Mata Kuliah</h5>
                        <!-- Perbaikan: data-bs-dismiss="modal" -->
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Pilih File Excel (.xlsx / .xls)</label>
                            <input type="file" name="file" class="form-control @error('file') is-invalid @enderror" required accept=".xlsx, .xls">
                            @error('file') <div class="invalid-feedback">{{ $message }}</div> @enderror

                            <div class="mt-3 p-2 bg-light rounded border">
                                <small class="text-muted d-block mb-1 font-bold text-uppercase">Format Header Excel:</small>
                                <code class="text-primary" style="font-size: 0.75rem;">
                                    kode_jurusan, kode_mk, nama_mk, semester, sks, nilai_minimum
                                </code>
                            </div>
                        </div>
                        <div class="text-center">
                            <a href="{{ route('mk.template') }}" class="btn btn-sm btn-outline-info">
                                <svg xmlns="http://w3.org" class="icon icon-tabler icon-tabler-download" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2"></path>
                                    <path d="M7 11l5 5l5 -5"></path>
                                    <path d="M12 4l0 12"></path>
                                </svg>
                                Unduh Template Excel
                            </a>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Mulai Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        (() => {
            new DataTable('#mataKuliahTable', {
                searching: true,
                paging: true,
                info: true,
                lengthChange: true,
                layout: {
                    topStart: {
                        pageLength: {
                            menu: [10, 25, 50, 100, {
                                label: 'Semua',
                                value: -1
                            }]
                        }
                    },
                    topEnd: ['search'],
                    bottomStart: 'info',
                    bottomEnd: 'paging'
                },
                language: {
                    lengthMenu: "_MENU_ item per halaman",
                    search: "",
                    searchPlaceholder: "Cari mata kuliah...",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ mata kuliah",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 mata kuliah",
                    infoFiltered: "",
                    // Tambahkan ini jika ingin mengubah teks tombol

                }
            });
        })();
    </script>
    @endpush
</x-app-layout>