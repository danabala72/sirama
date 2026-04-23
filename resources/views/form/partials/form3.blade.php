<form action="{{ url('/mata-kuliah') }}" method="GET">
    <div class="">
        <div class="">
            <h3 class="card-title">Cari Mata Kuliah</h3>
        </div>
        <div class="">
            @if(isset($mataKuliahPilihan) && isset($mataKuliah))
            @php
            $kodeDipilih = $mataKuliahPilihan->pluck('kode_mk');
            $mataKuliahTersisa = $mataKuliah->whereNotIn('kode_mk', $kodeDipilih);
            @endphp
            @endif

            @if(isset($mataKuliah) && !$mataKuliah->count())
            <div class="alert alert-danger my-2">
                <p>Tidak ada mata kuliah yang bisa dipilih pada jursan dan semester yang dipilih</p>
            </div>
            @elseif(isset($mataKuliahTersisa) && $mataKuliahTersisa->isEmpty())
            <div class="alert alert-success my-2">
                <p>Semua mata kuliah sudah dipilih</p>
            </div>
            @endif
            <form action="{{ url('/mata-kuliah') }}" method="GET">
                <div class="mb-3">
                    <label class="form-label required">Tahun Ajaran / Semester</label>
                    <div class="input-icon">
                        <select name="semester_id" id="semester_id" class="form-select" required style="max-width:300px">
                            @foreach($semester as $s)
                            <option value="{{ $s->id }}"
                                {{ old('semester_id', request('semester_id') ?? ($semester->where('is_active', true)->first()?->id)) == $s->id ? 'selected' : '' }}>
                                {{ $s->label }} {{ $s->is_active ? '— Aktif' : '' }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-footer mt-4">
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="ti ti-search me-1"></i> Cari
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</form>

@if(isset($mataKuliah) && $mataKuliah->count())
<div class="card my-4">
    <div class="card-header bg-primary">
        <h3 class="card-title text-white">Tambah Mata Kuliah Pilihan</h3>
    </div>

    <div class="card-body">
        @if ($errors->any())
        <div class="alert alert-danger my-2">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Form Blade -->
        <form id="form-mk" action="{{ route('mk-pilihan.store') }}" method="POST">
            @csrf
            <input type="hidden" name="_method" id="form-method" value="POST">
            <input type="hidden" name="mata_kuliah_id" id="hidden-mk-id" value="">
            <div class="row g-3">
                <div class="col-md-12 col-lg-6" id="select-mk-wrapper">
                    <label class="form-label fw-bold">Pilih Mata Kuliah</label>
                    <select id="select-api" name="mata_kuliah_id_select" class="form-select select2" required>
                        <option value="">-- Cari Mata Kuliah --</option>
                        @foreach($mataKuliahTersisa as $mk)
                        <option value="{{ $mk['id'] }}" data-nama="{{ $mk['nama_mk'] }}" data-sks="{{ $mk['sks'] }}">
                            {{ $mk['kode_mk'] }} - {{ $mk['nama_mk'] }}
                        </option>
                        @endforeach
                    </select>
                </div>

                @include('form.partials.input-opsi')
            </div>
        </form>


        <!-- <form action="{{ route('mk-pilihan.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-12 col-lg-6">
                    <label class="form-label fw-bold">Pilih Mata Kuliah</label>
                    <select id="select-api" name="mata_kuliah_id" class="form-select select2" required>
                        <option value="">-- Cari Mata Kuliah --</option>
                        @foreach($mataKuliahTersisa as $mk)
                        <option value="{{ $mk['id'] }}" data-nama="{{ $mk['nama_mk'] }}" data-sks="{{ $mk['sks'] }}">
                            {{ $mk['kode_mk'] }} - {{ $mk['nama_mk'] }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div id="input-opsi" class="col-md-12" @if(!$errors->any()) style="display:none;" @endif>
                    <fieldset class="form-fieldset bg-light border-dashed">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">
                                    Bukti Pendukung
                                </label>
                                <select required name="attachment_ids[]" id="attachment-select" class="form-select" multiple placehoder="Pilih beberapa file">
                                    @foreach($attachment as $file)
                                    <option value="{{ $file->id }}">{{ $file->label }} - {{ $file->file_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">SKS</label>
                                <div class="input-icon">
                                    <span class="input-icon-addon">
                                        <i class="ti ti-book"></i>
                                    </span>
                                    <input type="text" name="sks" id="in-sks" class="form-control text-center bg-azure-lt" readonly>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Nilai Huruf</label>
                                <input type="text" required name="nilai_huruf" class="form-control text-center" placeholder="A">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Nilai Angka</label>
                                <input type="number" required name="nilai_angka" class="form-control text-center" placeholder="0">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="ti ti-device-floppy me-2"></i>
                                    Simpan
                                </button>
                            </div>
                        </div>
                    </fieldset>
                </div>
            </div>
        </form> -->
    </div>
</div>
@endif

@if(!empty($mataKuliahPilihan))
<div class="card mt-4">
    <div class="card-header">
        <h3 class="card-title">Mata Kuliah Terpilih</h3>
    </div>

    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead>
                <tr>
                    <th>Kode MK</th>
                    <th>Nama MK</th>
                    <th>SKS</th>
                    <th>Bukti Pendukung</th>
                    <th>Nilai Huruf</th>
                    <th>Nilai Angka</th>
                    <th class="w-1">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse($mataKuliahPilihan as $mk)
                <tr>
                    <td>{{ $mk->kode_mk }}</td>

                    <td>{{ $mk->nama_mk }}</td>

                    <td>
                        <span class="badge bg-blue-lt">
                            {{ $mk->sks }} SKS
                        </span>
                    </td>
                    <td>
                        @forelse($mk->attachment as $item)
                        <div class="d-flex align-items-center mb-1">
                            <span class="me-2 text-truncate" style="max-width:180px;">
                                {{ $item->file_name }}
                            </span>

                            <a href="{{ asset('storage/' . $item->file_path) }}"
                                target="_blank"
                                class="btn btn-icon btn-ghost-primary btn-sm"
                                title="Preview">
                                <i class="ti ti-eye"></i>
                            </a>
                        </div>
                        @empty
                        <span class="text-muted">Tidak ada file</span>
                        @endforelse
                    </td>
                    <td>
                        <span class="badge bg-green-lt">
                            {{ $mk->nilai_huruf }}
                        </span>
                    </td>

                    <td>{{ $mk->nilai_angka }}</td>

                    <td>
                        <div class="d-flex gap-2">

                            <button type="button"
                                class="btn btn-warning btn-sm btn-edit"
                                data-id="{{ $mk->id }}"
                                data-nilai_huruf="{{ $mk->nilai_huruf }}"
                                data-nilai_angka="{{ $mk->nilai_angka }}"
                                data-attachments='@json($mk->attachment->pluck("id"))'
                                data-sks="{{ $mk->sks }}">

                                <i class="ti ti-pencil"></i>
                            </button>

                            <form action="{{ route('mk-pilihan.destroy', $mk->id) }}" method="POST"
                                onsubmit="return confirm('Hapus mata kuliah ini?')">
                                @csrf
                                @method('DELETE')

                                <button class="btn btn-icon btn-warning btn-sm">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </form>

                        </div>
                    </td>
                </tr>

                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted">
                        Belum ada mata kuliah pilihan
                    </td>
                </tr>
                @endforelse
            </tbody>

        </table>
    </div>
</div>
@endif

<div class="mt-4 d-flex gap-2">


    <a
        href="{{ route('form.step', 'step=4') }}"
        class="btn btn-outline-primary"
        title="Lanjut ke Formulir 4">
        <i class="ti ti-arrow-right me-1"></i>
        Ke Form 4
    </a>
</div>

<script>
    let attachmentTom = null;
    document.addEventListener("DOMContentLoaded", function() {
        const attachmentSelect = document.querySelector('#attachment-select');

        if (window.TomSelect && attachmentSelect) {

            attachmentTom = new TomSelect(attachmentSelect, {
                plugins: ['remove_button'],
                dropdownParent: 'body',
                copyClassesToDropdown: true,
                onItemAdd: function() {
                    this.setTextboxValue('');
                    this.refreshOptions();
                },
                render: {
                    item: function(data, escape) {
                        return `<div>${escape(data.text)} <span class="fw-bold ms-1">[${escape(data.label)}]</span></div>`;
                    },

                    dropdown: function() {
                        return '<div class="dropdown-menu ts-dropdown"></div>';
                    },
                    option: function(data, escape) {
                        return `<div>
                        <span class="text-dark">${escape(data.text)}</span>
                        <span class="fw-bold text-primary ms-1">[${escape(data.label)}]</span>
                    </div>`;
                    }
                }
            });

        }

        const selectMK = document.getElementById("select-api");
        const inputOpsi = document.getElementById("input-opsi");
        const sksInput = document.getElementById("in-sks");

        if (!selectMK) return; // hentikan script jika select tidak ada


        selectMK.addEventListener("change", function() {

            const selectedOption = this.options[this.selectedIndex];
            if (!selectedOption) return;

            if (this.value !== "") {

                const namaMk = selectedOption.dataset.nama || '';
                const sks = selectedOption.dataset.sks || '';

                const textSplit = selectedOption.text.split(' - ');
                const kodeMk = textSplit[0]?.trim() ?? '';

                if (sksInput) {
                    sksInput.value = sks;
                }

                updateHiddenInput('kode_mk', kodeMk);
                updateHiddenInput('nama_mk', namaMk);

                if (inputOpsi) {
                    inputOpsi.style.display = "block";
                }

            } else {

                if (inputOpsi) {
                    inputOpsi.style.display = "none";
                }

                if (sksInput) {
                    sksInput.value = '';
                }

                removeHiddenInputs();
            }
        });


        function updateHiddenInput(name, value) {
            let input = document.querySelector(`input[name="${name}"]`);

            if (!input) {
                input = document.createElement('input');
                input.type = 'hidden';
                input.name = name;

                if (selectMK.parentNode) {
                    selectMK.parentNode.appendChild(input);
                }
            }

            input.value = value;
        }

        function removeHiddenInputs() {
            ['kode_mk', 'nama_mk'].forEach(name => {
                const input = document.querySelector(`input[name="${name}"]`);
                if (input) input.remove();
            });
        }

    });

    document.querySelectorAll('.btn-edit').forEach(btn => {

        btn.addEventListener('click', function() {

            const attachments = JSON.parse(this.dataset.attachments);

            if (attachmentTom) {
                attachmentTom.setValue(attachments);
            }

            const id = this.dataset.id
            const huruf = this.dataset.nilai_huruf
            const angka = this.dataset.nilai_angka
            const sks = this.dataset.sks

            const form = document.getElementById('form-mk')

            // ubah action ke update
            form.action = `/mata-kuliah-pilihan/${id}`

            // ubah method
            document.getElementById('form-method').value = 'PUT'

            // isi nilai
            document.querySelector('[name="nilai_huruf"]').value = huruf
            document.querySelector('[name="nilai_angka"]').value = angka
            const sksInput = document.getElementById('in-sks')
            if (sksInput) {
                sksInput.value = sks
            }

            const hiddenMk = document.getElementById('hidden-mk-id');

            if (hiddenMk) {
                hiddenMk.value = id;
            }

            const select = document.getElementById('select-api');

            if (select) {
                // Gunakan removeAttribute
                select.removeAttribute('required');

                // Opsional: Jika ingin mendisable juga
                select.disabled = true;

                // Jika ingin menyembunyikan kontainer induknya
                select.closest('.col-md-12').style.display = 'none';
            }

            document.getElementById('select-mk-wrapper').style.display = 'none';

            // tampilkan form opsi
            document.getElementById('input-opsi').style.display = 'block'

            // scroll ke form
            console.log(form.offsetHeight)
            form.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });

        })

    })
</script>

<style>
    .ts-dropdown {
        background: #ffffff !important;
        border: 1px solid rgba(145, 158, 171, 0.24) !important;
        box-shadow: 0 12px 24px -4px rgba(145, 158, 171, 0.12) !important;
        border-radius: 4px !important;
        margin-top: 4px !important;
        z-index: 2000 !important;
    }

    .ts-control .item {
        background-color: #f1f5f9 !important;
        /* bg-light tabler */
        color: #1d273b !important;
        border: 1px solid #e6e7e9 !important;
        border-radius: 3px !important;
        padding: 2px 8px !important;
        display: flex;
        align-items: center;
    }

    .ts-dropdown .option.active {
        background-color: rgba(32, 107, 196, 0.06) !important;
        /* azure-lt tabler */
        color: #206bc4 !important;
        /* primary tabler */
    }

    .ts-control:focus {
        box-shadow: 0 0 0 0.25rem rgba(32, 107, 196, 0.25) !important;
        border-color: #90b5e2 !important;
    }
</style>