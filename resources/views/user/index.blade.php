<x-app-layout>

    <div class="container-xl p-2">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="text-2xl font-bold text-gray-800">Daftar User</h2>
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

        <a href="{{ route('user.create') }}" class="inline-flex items-center gap-x-2 bg-indigo-600 hover:bg-indigo-700 btn btn-sm btn-outline-primary my-2">
            <!-- Icon Plus yang lebih ramping -->
            <svg xmlns="http://w3.org" class="w-4 h-4 transition-transform duration-200 group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>

            <span class="hidden sm:inline">Tambah User</span>
        </a>

        <!-- Card Wrapper -->
        <div class="card shadow-sm">

            <div class="table-responsive">
                <table class="table table-vcenter card-table table-hover">
                    <thead>
                        <tr class="bg-gray-100 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            <th class="px-5 py-3 text-nowrap w-1">No</th>
                            <th class="px-5 py-3 text-nowrap">Username</th>
                            <th class="px-5 py-3 text-nowrap">Role</th>
                            <th class="px-5 py-3 text-nowrap">Email</th>
                            <th class="px-5 py-3 text-nowrap">Nama</th>
                            <th class="px-5 py-3 text-nowrap text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($user as $index => $mhs)
                        <tr class="transition">
                            <td class="px-5 py-3 text-sm text-muted">{{ $index + 1 }}</td>
                            <td class="px-5 py-3 text-sm font-medium text-gray-900">
                                {{ $mhs->username }}
                            </td>
                            <td class="px-5 py-3 text-sm font-medium text-gray-900">
                                {{ $mhs->role->role }}
                            </td>
                            <td class="px-5 py-3 text-sm text-muted">
                                {{ $mhs->mahasiswa->email ?? '-' }}
                            </td>
                            <td class="px-5 py-3 text-sm font-medium">
                                {{ $mhs->mahasiswa->name ?? '-' }}
                            </td>

                            <td class="px-5 py-3 text-sm text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('user.edit', $mhs->id) }}" class="btn btn-sm btn-outline-primary">
                                        <svg xmlns="http://w3.org" class="icon icon-tabler icon-tabler-edit" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none""" ) />>
                                            <path d="M12 15l8.385 -8.415a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3z" />
                                            <path d="M16 5l3 3" />
                                            <path d="M9 7.07a7 7 0 0 0 1 13.93a7 7 0 0 0 6.929 -6" />
                                        </svg>
                                        Edit User
                                    </a>
                                     <form action="{{ route('user.destroy', $mhs->id) }}" method="POST" 
              onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-outline-danger btn-icon">
                <svg xmlns="http://w3.org" class="icon icon-tabler icon-tabler-trash" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none""")/>>
                    <path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" />
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
                            <td colspan="5" class="px-5 py-10 text-center text-gray-500 italic">
                                Belum ada data mahasiswa.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($user->hasPages())
            <div class="card-footer d-flex align-items-center border-top-0">
                <p class="m-0 text-muted">
                    Showing <span>{{ $user->firstItem() }}</span> to <span>{{ $user->lastItem() }}</span> of <span>{{ $user->total() }}</span> entries
                </p>
                <div class="pagination m-0 ms-auto">
                    {{ $user->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>


</x-app-layout>