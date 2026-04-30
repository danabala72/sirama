<x-app-layout>
    <div class="container-xl p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-2xl font-bold text-gray-800">Daftar Admin Jurusan</h2>
            <!-- Tombol ini mencari ID #modalTambahAdmin -->
            <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahAdmin">
                <i class="fas fa-plus me-1"></i> Tambah Admin Jurusan
            </button>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table id="adminJurusanTable" class="table table-hover align-middle w-100">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 5%">No</th>
                                <th>Nama</th>
                                <th>Jurusan</th>
                                <th>Username</th>
                                <th>No. HP</th>
                                <th class="text-center" style="width: 15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($adminJurusans as $admin)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="font-semibold text-gray-700">{{ $admin->nama }}</td>
                                <td>
                                    <span class="badge bg-info-subtle text-info border border-info-subtle px-3">
                                        {{ $admin->jurusan->nama_jurusan }}
                                    </span>
                                </td>
                                <td>{{ $admin->user->username }}</td>
                                <td>{{ $admin->no_hp ?? '-' }}</td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <button type="button" class="btn btn-sm btn-outline-warning d-flex align-items-center edit-btn" data-id="{{ $admin->id }}">
                                            <i class="ti ti-edit fs-5"></i>
                                        </button>

                                        <form action="{{ route('admin_jurusan.destroy', $admin->id) }}" method="POST" onsubmit="return confirm('Hapus admin ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger d-flex align-items-center">
                                                <i class="ti ti-trash fs-5"></i>
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
        </div>
    </div>

    <!-- 1. MODAL TAMBAH (CREATE) -->
    <div class="modal fade" id="modalTambahAdmin"
        data-is-error="{{ ($errors->any() && !session('edit_action')) ? 'true' : 'false' }}"
        tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <h5 class="modal-title font-bold">Tambah Admin Jurusan Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin_jurusan.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label font-medium">Nama Lengkap</label>
                                <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama') }}" required>
                                @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label font-medium">Jenis Kelamin</label>
                                <select name="jenis_kelamin" class="form-select @error('jenis_kelamin') is-invalid @enderror">
                                    <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                                @error('jenis_kelamin') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-medium">Jurusan</label>
                                <select name="jurusan_id" class="form-select @error('jurusan_id') is-invalid @enderror" required>
                                    <option value="" selected disabled>Pilih Jurusan</option>
                                    @foreach($jurusans as $j)
                                    <option value="{{ $j->id }}" {{ old('jurusan_id') == $j->id ? 'selected' : '' }}>{{ $j->nama_jurusan }}</option>
                                    @endforeach
                                </select>
                                @error('jurusan_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-medium">Email</label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-12">
                                <label class="form-label font-medium">No. HP</label>
                                <input type="text" name="no_hp" class="form-control @error('no_hp') is-invalid @enderror" value="{{ old('no_hp') }}">
                                @error('no_hp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 mt-4">
                                <h6 class="text-primary font-bold border-bottom pb-2">Kredensial Login</h6>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label font-medium">Username</label>
                                <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username') }}" required>
                                @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label font-medium">Password</label>
                                <input type="password" autocomplete="new-password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label font-medium">Konfirmasi</label>
                                <input type="password" name="password_confirmation" autocomplete="new-password" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary px-4">Simpan Admin</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- 2. MODAL EDIT -->
    <div class="modal fade" id="modalEditAdmin"
        data-is-error="{{ session('edit_action') && $errors->any() ? 'true' : 'false' }}"
        tabindex="-1" aria-hidden="true">

        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <h5 class="modal-title font-bold">Edit Admin Jurusan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formEditAdmin" action="{{ session('edit_action') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label font-medium">Nama Lengkap</label>
                                <input type="text" name="nama" id="edit_nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama') }}" required>
                                @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label font-medium">Jenis Kelamin</label>
                                <select name="jenis_kelamin" id="edit_jk" class="form-select @error('jenis_kelamin') is-invalid @enderror">
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                                @error('jenis_kelamin') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-medium">Jurusan</label>
                                <select name="jurusan_id" id="edit_jurusan_id" class="form-select @error('jurusan_id') is-invalid @enderror" required>
                                    @foreach($jurusans as $j)
                                    <option value="{{ $j->id }}">{{ $j->nama_jurusan }}</option>
                                    @endforeach
                                </select>
                                @error('jurusan_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-medium">Email</label>
                                <input type="email" name="email" id="edit_email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-12">
                                <label class="form-label font-medium">No. HP</label>
                                <input type="text" name="no_hp" id="edit_no_hp" class="form-control @error('no_hp') is-invalid @enderror" value="{{ old('no_hp') }}">
                                @error('no_hp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 mt-4">
                                <h6 class="text-primary font-bold border-bottom pb-2">Kredensial Login (Opsional)</h6>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label font-medium text-primary">Username</label>
                                <input type="text" name="username" id="edit_username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username') }}" required>
                                @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label font-medium text-primary">Password Baru</label>
                                <input type="password" autocomplete="new-password" name="password" class="form-control @error('password') is-invalid @enderror">
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label font-medium text-primary">Konfirmasi</label>
                                <input type="password" autocomplete="new-password" name="password_confirmation" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary px-4">Update Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    @push('scripts')
    <script>
        $(document).ready(function() {
            // 1. Inisialisasi DataTable
            new DataTable('#adminJurusanTable', {
                searching: true,
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
                    searchPlaceholder: "Cari admin jurusan...",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ admin jurusan",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 admin jurusan",
                }
            });

            // 2. Logic AJAX Edit (Ambil data)
            $(document).on('click', '.edit-btn', function() {
                const id = $(this).data('id');
                $.get(`/admin/jurusan/${id}/edit`, function(data) {
                    $('#formEditAdmin').attr('action', `/admin/jurusan/${id}`);
                    $('#edit_nama').val(data.admin.nama);
                    $('#edit_jk').val(data.admin.jenis_kelamin);
                    $('#edit_jurusan_id').val(data.admin.user.jurusan_id);
                    $('#edit_email').val(data.admin.email);
                    $('#edit_no_hp').val(data.admin.no_hp);
                    $('#edit_username').val(data.username);

                    new bootstrap.Modal(document.getElementById('modalEditAdmin')).show();
                });
            });

            // 3. Logic Auto-Open (Cek atribut data)
            const modalTambah = document.getElementById('modalTambahAdmin');
            const modalEdit = document.getElementById('modalEditAdmin');

            if (modalTambah && modalTambah.getAttribute('data-is-error') === 'true') {
                new bootstrap.Modal(modalTambah).show();
            }

            if (modalEdit && modalEdit.getAttribute('data-is-error') === 'true') {
                new bootstrap.Modal(modalEdit).show();
            }
        });
    </script>
    @endpush

</x-app-layout>