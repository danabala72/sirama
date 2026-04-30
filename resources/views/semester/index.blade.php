<x-app-layout>
    <div class="container-xl p-2">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="text-2xl font-bold text-gray-800">Manajemen Semester</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-tambah">
                <i class="ti ti-plus me-1"></i> Tambah Semester
            </button>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            <div class="d-flex">
                <div><i class="ti ti-check me-2"></i> {{ session('success') }}</div>
            </div>
            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
        </div>
        @endif

        <div class="card shadow-sm">
            <div class="table-responsive">
                <table id="semesterTable" class="table table-vcenter card-table table-hover">
                    <thead>
                        <tr class="bg-gray-100 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            <th class="text-nowrap w-1">No</th>
                            <th class="text-nowrap">Kode</th>
                            <th class="text-nowrap">Label Semester</th>
                            <th class="text-nowrap text-center">Status Aktif</th>
                            <th class="text-nowrap text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($semesters as $index => $semester)
                        <tr class="transition">
                            <td class="text-sm text-muted">{{ $index + 1 }}</td>
                            <td class="text-sm font-medium">
                                <span class="badge bg-blue-lt">{{ $semester->kode }}</span>
                            </td>
                            <td class="text-sm font-medium text-gray-900">{{ $semester->label }}</td>
                            <td class="text-sm text-center">
                                @if($semester->is_active)
                                <span class="badge bg-green-lt px-3 py-1">
                                    <i class="ti ti-circle-check me-1"></i> Aktif
                                </span>
                                @else
                                <form action="{{ route('semester.set-aktif', $semester->id) }}" method="POST" class="m-0">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-outline-secondary">Set Aktif</button>
                                </form>
                                @endif
                            </td>
                            <td class="text-sm text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modal-edit-{{ $semester->id }}">
                                        <i class="ti ti-edit me-1"></i> Edit
                                    </button>

                                    <form action="{{ route('semester.destroy', $semester->id) }}" method="POST" onsubmit="return confirm('Hapus semester ini?')" class="m-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger btn-icon">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-5 py-10 text-center text-gray-500 italic">Belum ada data semester.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL EDIT - DI LUAR CARD & TABEL -->
    @foreach ($semesters as $semester)
    <div class="modal modal-blur fade" id="modal-edit-{{ $semester->id }}" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <form action="{{ route('semester.update', $semester->id) }}" method="POST" class="modal-content">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title font-bold">Edit Semester</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label font-semibold small">Kode Semester</label>
                        <input type="text" name="kode" class="form-control" value="{{ $semester->kode }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-semibold small">Label Semester</label>
                        <input type="text" name="label" class="form-control" value="{{ $semester->label }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary ms-auto">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
    @endforeach

    <!-- MODAL TAMBAH -->
    <div class="modal modal-blur fade" id="modal-tambah" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <form action="{{ route('semester.store') }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title font-bold">Tambah Semester Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label font-semibold small">Kode Semester</label>
                        <input type="text" name="kode" class="form-control" placeholder="Contoh: 20231" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-semibold small">Label Semester</label>
                        <input type="text" name="label" class="form-control" placeholder="Contoh: Ganjil 2023/2024" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary ms-auto">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        (() => {
            new DataTable('#semesterTable', {
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
                    topEnd: 'search',
                    bottomStart: 'info',
                    bottomEnd: 'paging'
                },
                language: {
                    lengthMenu: "_MENU_ item per halaman",
                    search: "",
                    searchPlaceholder: "Cari semester...",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ semester",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 semester",
                }
            });
        })();
    </script>
    @endpush
</x-app-layout>