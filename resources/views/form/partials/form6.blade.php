@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

<div>
    <form action="{{ route('transfer.store') }}" method="POST">
        @csrf
        <div>
            @foreach($mataKuliahPilihan as $mk)
            <div class="row border-bottom mb-4 pb-4">

                <!-- SISI KIRI: DATA TARGET -->
                <div class="col-md-5">
                    <label class="text-muted small">Mata Kuliah Target (Prodi Tujuan)</label>
                    <h5 class="mb-1">{{ $mk->nama_mk }}</h5>
                    <p class="text-primary font-weight-bold">{{ $mk->kode_mk }} </p>
                </div>

                <!-- SISI KANAN: INPUT MAHASISWA -->
                <div class="col-md-7 border-left">
                    <!-- Perhatikan penggunaan data[{{ $mk->id }}] agar data tidak tertukar -->
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label>Kode MK Asal</label>
                            <input type="text" name="data[{{ $mk->id }}][kode_mk_asal]" class="form-control"
                                value="{{ $mk->transferSks->kode_mk_asal ?? '' }}" placeholder="CS101">
                        </div>
                        <div class="col-md-8 form-group">
                            <label>Nama Mata Kuliah Asal</label>
                            <input type="text" name="data[{{ $mk->id }}][nama_mk_asal]" class="form-control"
                                value="{{ $mk->transferSks->nama_mk_asal ?? '' }}" placeholder="Nama MK Asal">
                        </div>
                    </div>

                    <div class="form-group my-2">
                        <label>Capaian Pembelajaran (CPMK) Asal</label>
                        <div id="cpmk-container-{{ $mk->id }}">
                            @if($mk->transferSks && $mk->transferSks->cpmkItems->count() > 0)
                            @foreach($mk->transferSks->cpmkItems as $cpmk)
                            <div class="d-flex align-items-start my-4">
                                <!-- Textarea -->
                                <textarea name="data[{{ $mk->id }}][item_cpmk][]"
                                    class="form-control"
                                    
                                    placeholder="Capaian Pembelajaran">{{ $cpmk->cpmk ?? '' }}</textarea>

                                <!-- Tombol Kecil (btn-sm) dengan Jarak (ms-2) -->
                                <button class="btn btn-icon btn-sm btn-outline-danger ms-2 flex-shrink-0"
                                    type="button"
                                    onclick="this.parentElement.remove()"
                                    title="Hapus">
                                    <svg xmlns="http://w3.org" class="icon icon-tabler icon-tabler-trash" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M4 7l16 0" />
                                        <path d="M10 11l0 6" />
                                        <path d="M14 11l0 6" />
                                        <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                        <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                    </svg>
                                </button>
                            </div>

                            @endforeach
                            @else
                            <div class="d-flex align-items-start mb-2">
                                <!-- Textarea -->
                                <textarea name="data[{{ $mk->id }}][item_cpmk][]"
                                    class="form-control"
                                    rows="2"
                                    placeholder="Capaian Pembelajaran">{{ $cpmk->item_cpmk ?? '' }}</textarea>

                                <!-- Tombol Kecil (btn-sm) dengan Jarak (ms-2) -->
                                <button class="btn btn-icon btn-sm btn-outline-danger ms-2 flex-shrink-0"
                                    type="button"
                                    onclick="this.parentElement.remove()"
                                    title="Hapus">
                                    <svg xmlns="http://w3.org" class="icon icon-tabler icon-tabler-trash" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M4 7l16 0" />
                                        <path d="M10 11l0 6" />
                                        <path d="M14 11l0 6" />
                                        <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                        <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                    </svg>
                                </button>
                            </div>


                            @endif
                        </div>

                        <!-- Tambahkan fungsi onclick kembali -->
                        <button type="button"
                            class="btn btn-outline-primary btn-sm mt-2 border-dashed"
                            onclick="addCpmkField('{{ $mk->id }}')">
                            <!-- Icon Plus (Tabler Icons) -->
                            <svg xmlns="http://w3.org" class="icon icon-tabler icon-tabler-plus" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M12 5l0 14"></path>
                                <path d="M5 12l14 0"></path>
                            </svg>
                            Tambah CPMK
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Tombol Simpan di luar loop -->
        <div class="text-right mt-3">
            <button type="submit" class="btn btn-primary ">Simpan</button>
        </div>
    </form>
</div>

<script>
    function addCpmkField(id) {
        const container = document.getElementById(`cpmk-container-${id}`);
        if (!container) return;

        const wrapper = document.createElement('div');
        wrapper.className = 'd-flex align-items-start mb-2';

        const textarea = document.createElement('textarea');
        textarea.name = `data[${id}][item_cpmk][]`;
        textarea.className = 'form-control';
        textarea.rows = 2;
        textarea.placeholder = 'Capaian Pembelajaran';

        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'btn btn-icon btn-sm btn-outline-danger ms-2 flex-shrink-0';
        removeBtn.title = 'Hapus Item';

        removeBtn.innerHTML = `
            <svg xmlns="http://w3.org" class="icon icon-tabler icon-tabler-trash" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <path d="M4 7l16 0" />
                <path d="M10 11l0 6" />
                <path d="M14 11l0 6" />
                <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
            </svg>
        `;

        removeBtn.onclick = function() {
            wrapper.remove();
        };

        wrapper.appendChild(textarea);
        wrapper.appendChild(removeBtn);
        container.appendChild(wrapper);
    }
</script>