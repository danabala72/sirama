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

    </div>

    @push('scripts')
    <script>
        (() => {
            new DataTable('#mataKuliahTable', {
                searching: true,
                paging: true,
                info: true,
                layout: {
                    topStart: null,
                    topEnd: 'search',
                    bottomStart: 'info',
                    bottomEnd: 'paging'
                },
                language: {
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