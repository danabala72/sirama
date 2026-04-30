<x-app-layout>

    <div class="container-xl p-2">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="text-2xl font-bold text-gray-800">Daftar Jurusan</h2>
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

        @if(Auth::user()->role->role === 'Admin')
        <div class="d-flex flex-wrap gap-2 mb-3">
            <a href="{{ route('jurusan.create') }}" class="btn btn-outline-info btn-sm inline-flex items-center gap-x-2 my-2">
                <svg xmlns="http://w3.org" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span class="hidden sm:inline">Tambah Jurusan</span>
            </a>



            <button type="button" class="inline-flex items-center gap-x-2 bg-indigo-600 hover:bg-indigo-700 btn btn-sm btn-outline-success my-2" data-bs-toggle="modal" data-bs-target="#modalImport">
                <svg xmlns="http://w3.org" class="w-4 h-4" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                    <path d="M5 13v-8a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2h-5.5" />
                    <path d="M2 19h7" />
                    <path d="M7 16l3 3l-3 3" />
                </svg>
                Import Jurusan
            </button>

        </div>
        @endif
        <!-- Card Wrapper -->

        <!-- Card Wrapper -->
        <div class="card shadow-sm">
            <div class="table-responsive">
                <table id="jurusanTable" class="table table-vcenter card-table table-hover">
                    <thead>
                        <tr class="bg-gray-100 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            <th class="text-nowrap w-1">No</th>
                            <th class="text-nowrap">Kode Jurusan</th>
                            <th class="text-nowrap">Nama Jurusan</th>
                            <th class="text-nowrap text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($jurusan as $index => $item)
                        <tr class="transition">
                            <td class="text-sm text-muted">{{ $index + 1 }}</td>
                            <td class="text-sm font-medium text-gray-900">
                                <span class="badge bg-blue-lt">{{ $item->kode_jurusan }}</span>
                            </td>
                            <td class="text-sm font-medium">{{ $item->nama_jurusan }}</td>
                            <td class="text-sm text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    @if($item->mataKuliah->count() > 0)
                                    <a href="{{ route('mk.index', ['jurusan_id' => $item->id]) }}" class="btn btn-sm btn-outline-info">
                                        <svg xmlns="http://w3.org" class="icon icon-tabler icon-tabler-book" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M3 19a9 9 0 0 1 9 0a9 9 0 0 1 9 0" />
                                            <path d="M3 6a9 9 0 0 1 9 0a9 9 0 0 1 9 0" />
                                            <line x1="3" y1="6" x2="3" y2="19" />
                                            <line x1="12" y1="6" x2="12" y2="19" />
                                            <line x1="21" y1="6" x2="21" y2="19" />
                                        </svg>
                                        MK <span class="badge bg-info-lt ms-1">{{ $item->mataKuliah->count() }}</span>
                                    </a>
                                    @else
                                    <button class="btn btn-sm btn-outline-secondary disabled" title="Tidak ada mata kuliah" disabled>
                                        <svg xmlns="http://w3.org" class="icon icon-tabler icon-tabler-book-off" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M3 19a9 9 0 0 1 9 0a9 9 0 0 1 9 0" />
                                            <path d="M3 6a9 9 0 0 1 3 0m4 0a9 9 0 0 1 9 0" />
                                            <line x1="3" y1="6" x2="3" y2="19" />
                                            <line x1="12" y1="8" x2="12" y2="19" />
                                            <line x1="21" y1="6" x2="21" y2="19" />
                                        </svg>
                                        MK <span class="badge bg-secondary-lt ms-1">0</span>
                                    </button>
                                    @endif
                                    <!-- Edit Button -->
                                    <a href="{{ route('jurusan.edit', $item->id) }}" class="btn btn-sm btn-outline-primary">
                                        <svg xmlns="http://w3.org" class="icon icon-tabler icon-tabler-edit" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M12 15l8.385 -8.415a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3z" />
                                            <path d="M16 5l3 3" />
                                            <path d="M9 7.07a7 7 0 0 0 1 13.93a7 7 0 0 0 6.929 -6" />
                                        </svg>
                                        Edit
                                    </a>

                                    @if(Auth::user()->role->role === 'Admin')
                                    <!-- Delete Button -->
                                    <form action="{{ route('jurusan.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus jurusan ini?')" class="m-0">
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
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-5 py-10 text-center text-gray-500 italic">Belum ada data jurusan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="modal modal-blur fade" id="modalImport" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <!-- Route diarahkan ke proses import khusus jurusan -->
                    <form action="{{ route('jurusan.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Import Data Jurusan</h5>
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
                                        kode_jurusan, nama_jurusan
                                    </code>
                                </div>
                            </div>
                            <div class="text-center">
                                <!-- Tombol unduh template jurusan -->
                                <a href="{{ route('jurusan.template') }}" class="btn btn-sm btn-outline-info">
                                    <svg xmlns="http://w3.org" class="icon icon-tabler icon-tabler-download" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2"></path>
                                        <path d="M7 11l5 5l5 -5"></path>
                                        <path d="M12 4l0 12"></path>
                                    </svg>
                                    Unduh Template Jurusan
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


    </div>

    @push('scripts')
    <script>
        (() => {
            new DataTable('#jurusanTable', {
                searching: true,
                paging: true,
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
                    topEnd: 'search',
                    // Gunakan string 'info' dan 'paging' (bukan pagination)
                    bottomStart: 'info',
                    bottomEnd: 'paging'
                },
                language: {
                    lengthMenu: "_MENU_ item per halaman",
                    search: "",
                    searchPlaceholder: "Cari jurusan ...",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ jurusan",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 jurusan",
                    infoFiltered: "",
                    // Tambahkan ini jika ingin mengubah teks tombol

                }
            });
        })();
    </script>
    @endpush

</x-app-layout>