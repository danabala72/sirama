<x-app-layout>

    <div class="container-xl">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Data Skema</h3>
                <div class="card-actions">
                    <button class="btn btn-outline-info btn-sm inline-flex items-center gap-x-2" data-bs-toggle="modal" data-bs-target="#modalCreate">
                        <svg xmlns="http://www.w3.org/2000/2000" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Tambah Skema
                    </button>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th>Nama Skema</th>
                        <th>Jurusan</th>
                        <th>Deskripsi</th>
                        <th class="w-1"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($skemas as $skema)
                    <tr>
                        <td>{{ $skema->nama_skema }}</td>
                        <td>{{ $skema->jurusan->nama_jurusan ?? '-' }}</td>
                        <td>{{ $skema->deskripsi ?? '-' }}</td>
                        <td>
                            <div class="d-flex justify-content-center gap-2">
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEdit" data-id="{{ $skema->id }}">
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
                    @empty
                    <tr>
                        <td colspan="4" class="text-center">Belum ada data skema.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Create --}}
<div class="modal modal-blur fade" id="modalCreate" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('skema.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Skema</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Skema</label>
                        <input type="text" name="nama_skema" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jurusan</label>
                        <select name="jurusan_id" class="form-select" required>
                            <option value="">-- Pilih Jurusan --</option>
                            @foreach($jurusans as $j)
                            <option value="{{ $j->id }}">{{ $j->nama_jurusan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Edit --}}
<div class="modal modal-blur fade" id="modalEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="formEditSkema" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Skema</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Skema</label>
                        <input type="text" name="nama_skema" id="editNamaSkema" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jurusan</label>
                        <select name="jurusan_id" id="editJurusanId" class="form-select" required>
                            <option value="">-- Pilih Jurusan --</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" id="editDeskripsi" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modalEdit = document.getElementById('modalEdit');
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
                
                const selectJurusan = document.getElementById('editJurusanId');
                selectJurusan.innerHTML = '<option value="">-- Pilih Jurusan --</option>';
                data.jurusans.forEach(j => {
                    const option = document.createElement('option');
                    option.value = j.id;
                    option.textContent = j.nama_jurusan;
                    if (j.id == data.skema.jurusan_id) option.selected = true;
                    selectJurusan.appendChild(option);
                });
            });
    });
});
</script>
</x-app-layout>
