<x-app-layout>
    <div class="container-xl p-2">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="text-2xl font-bold text-gray-800">Asesmen Pendidikan Non Formal</h2>
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

        <!-- Card Wrapper -->
        <div class="card shadow-sm border-0">
            <div class="table-responsive">
                <table id="mhsFormalTable" class="table table-vcenter card-table table-hover">
                    <thead>
                        <tr class="bg-gray-100 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            <th class="text-nowrap w-1">No</th>
                            <th class="text-nowrap">Mahasiswa</th>
                            <th class="text-nowrap">Jurusan</th>
                            <th class="text-nowrap">Kontak & Email</th>
                            <th class="text-nowrap text-center">Asesmen</th>
                            <th class="text-nowrap text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($mahasiswas as $index => $mhs)
                        <tr class="transition">
                            <td class="text-sm text-muted">{{ $index + 1 }}</td>
                            <td class="text-sm">
                                <div class="font-medium text-gray-900">{{ $mhs->name }}</div>
                            </td>
                            <td class="text-sm">
                                <div class="font-medium text-gray-900">{{ $mhs->jurusan->nama_jurusan }}</div>
                            </td>
                            <td class="text-sm">
                                <div class="d-flex align-items-center mb-1">
                                    <i class="ti ti-mail me-1 text-muted"></i> {{ $mhs->email }}
                                </div>
                                <div class="text-muted small">
                                    <i class="ti ti-phone me-1"></i> {{ $mhs->no_hp }}
                                </div>
                            </td>
                            <td class="text-sm text-center">
                                @php
                                    $belumDinilai = collect($mhs->mataKuliahPilihan ?? [])->filter(function($mk) {
                                        // 1. Jika data transfer belum dibuat SAMA SEKALI
                                        if (!$mk->transferSksNonFormal) {
                                            return true;
                                        }

                                        // 2. Jika data transfer sudah ada, tapi kolomnya masih ada yang NULL
                                        return is_null($mk->transferSksNonFormal->kesenjangan) || 
                                            is_null($mk->transferSksNonFormal->nilai) || 
                                            is_null($mk->transferSksNonFormal->catatan_asesor);
                                    })->count();
                                @endphp

                                @if($belumDinilai > 0)
                                <span class="badge bg-warning-lt" title="Ada mata kuliah yang belum lengkap penilaiannya">
                                    {{ $belumDinilai }} / {{ count($mhs->mataKuliahPilihan ?? []) }} MK Belum Lengkap
                                </span>
                                @else
                                <span class="badge bg-green-lt">
                                    <i class="ti ti-check me-1"></i> Semua Dinilai
                                </span>
                                @endif
                            </td>
                            <td class="text-sm text-center">
                            <div class="d-flex justify-content-center gap-2">
                                @php
                                    $mkPilihan = collect($mhs->mataKuliahPilihan ?? []);
                                    
                                    $hasDataNonFormal = $mkPilihan->contains(function($mk) {
                                        return $mk->attachment && $mk->attachment->count() > 0;
                                    });
                                @endphp

                                @if($hasDataNonFormal)
                                    <a href="{{ route('asesmen.nonformal.review', $mhs->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="ti ti-checklist me-1"></i>
                                        Mulai Penilaian
                                    </a>
                                @else
                                    <button class="btn btn-sm btn-outline-secondary disabled" disabled>
                                        <i class="ti ti-file-off me-1"></i>
                                        Belum Ada Bukti
                                    </button>
                                @endif

                            </div>
                        </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                <div class="text-muted small uppercase fw-bold">Tidak ada data mahasiswa ditemukan</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @push('style')
    <style>
        .dataTables_filter input {
            border: 1px solid #e2e8f0 !important;
            border-radius: 0.375rem !important;
            padding: 0.25rem 0.75rem !important;
            outline: none;
        }

        .dataTables_length select {
            border: 1px solid #e2e8f0 !important;
            border-radius: 0.375rem !important;
            padding: 0.25rem 0.5rem !important;
        }

        /* Memperbaiki jarak antar baris */
        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, .02);
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        (() => {
            new DataTable('#mhsFormalTable', {
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
                    searchPlaceholder: "Cari mahasiswa ...",
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