<x-app-layout>

    <div class="container-xl p-2">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="text-2xl font-bold text-gray-800">Daftar Mahasiswa</h2>
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

        @if($errors->any())
        <div class="alert alert-danger alert-dismissible" role="alert">
            <div class="d-flex">
                <div>
                    <h4 class="alert-title">Terjadi Kesalahan!</h4>
                    <div class="text-secondary">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
        </div>
        @endif

        <div class="d-flex flex-wrap gap-2 mb-3">
            <a href="{{ route('mahasiswa.create') }}" class="btn btn-outline-info btn-sm inline-flex items-center gap-x-2 my-2">
                <!-- Icon Plus Ramping -->
                <svg xmlns="http://w3.org" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span class="hidden sm:inline">Tambah Mahasiswa</span>
            </a>


            <button type="button" class="inline-flex items-center gap-x-2 bg-indigo-600 hover:bg-indigo-700 btn btn-sm btn-outline-success my-2" data-bs-toggle="modal" data-bs-target="#modalImport">
                <svg xmlns="http://w3.org" class="w-4 h-4" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                    <path d="M5 13v-8a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2h-5.5" />
                    <path d="M2 19h7" />
                    <path d="M7 16l3 3l-3 3" />
                </svg>
                Import Mahasiswa
            </button>

        </div>


        <!-- Card Wrapper -->
        <div class="card shadow-sm">
            <div class="table-responsive">
                <table id="userTable" class="table table-vcenter card-table table-hover">
                    <thead>
                        <tr class="bg-gray-100 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            <th class="text-nowrap w-1">No</th>
                            <th class="text-nowrap">Username</th>
                            <th class="text-nowrap">Nama</th>
                            <th class="text-nowrap">Jurusan</th>
                            <th class="text-nowrap">Email</th>
                            <th class="text-nowrap text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($mahasiswa as $index => $mhs)
                        <tr class="transition">
                            <td class="text-sm text-muted">{{ $index + 1 }}</td>
                            <td class="text-sm font-medium text-gray-900">{{ $mhs->username }}</td>
                            <td class="text-sm font-medium">{{ $mhs->mahasiswa->name ?? '-' }}</td>
                            <td class="text-sm font-medium">{{ $mhs->mahasiswa->jurusan->nama_jurusan ?? '-' }}</td>
                            <td class="text-sm text-muted">{{ $mhs->mahasiswa->email ?? '-' }}</td>
                            <td class="text-sm text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('mahasiswa.edit', $mhs->id) }}" class="btn btn-sm btn-outline-primary">
                                        <svg xmlns="http://w3.org" class="icon icon-tabler icon-tabler-edit" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M12 15l8.385 -8.415a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3z" />
                                            <path d="M16 5l3 3" />
                                            <path d="M9 7.07a7 7 0 0 0 1 13.93a7 7 0 0 0 6.929 -6" />
                                        </svg>
                                        Edit
                                    </a>
                                    <form action="{{ route('mahasiswa.destroy', $mhs->id) }}" method="POST" onsubmit="return confirm('Hapus mahasiswa ini?')" class="m-0">
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
                            <td colspan="6" class="px-5 py-10 text-center text-gray-500 italic">Belum ada data.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal modal-blur fade" id="modalImport" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="{{ route('mahasiswa.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Import Data Mahasiswa</h5>
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
                                    kode_jurusan, username, password, nama_lengkap, email, jenis_kelamin, tempat_lahir, tgl_lahir, no_hp
                                </code>
                            </div>
                        </div>
                        <div class="text-center">
                            <a href="{{ route('mahasiswa.template') }}" class="btn btn-sm btn-outline-info">
                                <svg xmlns="http://w3.org" class="icon icon-tabler icon-tabler-download" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2"></path>
                                    <path d="M7 11l5 5l5 -5"></path>
                                    <path d="M12 4l0 12"></path>
                                </svg>
                                Unduh Template Mahasiswa
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
            new DataTable('#userTable', {
                searching: true,
                paging: true,
                info: true,
                layout: {
                    topStart: null,
                    topEnd: 'search',
                    // Gunakan string 'info' dan 'paging' (bukan pagination)
                    bottomStart: 'info',
                    bottomEnd: 'paging'
                },
                language: {
                    search: "",
                    searchPlaceholder: "Cari mahasiswa...",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ mahasiswa",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 mahasiswa",
                    infoFiltered: "",
                    // Tambahkan ini jika ingin mengubah teks tombol

                }
            });
        })();
    </script>
    @endpush

</x-app-layout>