<x-app-layout>
    <div class="container-xl p-2">

        <div class="mb-3">
            <h2 class="text-2xl font-bold text-gray-800">
                Review Asesmen: {{ $namaMahasiswa }}
            </h2>
            <p class="text-muted small">
                Silakan berikan penilaian pada setiap Capaian Pembelajaran (CPMK).
            </p>
        </div>

        <form action="{{ route('asesmen.update') }}" method="POST">
            @csrf
            @method('PUT')

            @foreach ($pilihanMk as $mk)
            @if ($mk->transferSks)
            @php
            $attachments = collect($mk->attachment ?? []);

            $formalFiles = $attachments->filter(function($file){
            $label = strtolower($file->label);
            return str_contains($label, 'ijazah') || str_contains($label, 'transkrip');
            });

            $nonFormalFiles = $attachments->filter(function($file){
            $label = strtolower($file->label);
            return !str_contains($label, 'ijazah') && !str_contains($label, 'transkrip');
            });
            @endphp

            {{-- ===================== CARD MK ===================== --}}
            <div class="card mb-4">

                <div class="card-header bg-blue-lt">
                    <div>
                        <div class="text-uppercase fw-bold text-blue small">
                            Mata Kuliah Target
                        </div>
                        <h3 class="card-title fw-bold text-dark">
                            {{ $mk->nama_mk }} ({{ $mk->kode_mk }})
                        </h3>
                    </div>
                </div>

                <div class="card-body py-3">

                    {{-- ===================== CPMK TARGET & ASAL ===================== --}}
                    <div class="row g-3 mb-4">

                        {{-- TARGET --}}
                        <div class="col-12 col-lg-6">
                            <div class="p-3 bg-blue-lt border border-blue-subtle rounded-3 h-100">
                                <label class="form-label fw-bold text-blue small uppercase mb-2">
                                    CPMK Mata Kuliah Target
                                </label>
                                <ul class="list-unstyled mb-0">
                                    @foreach ($mk->cpLevel ?? [] as $cpLevel)
                                    <li class="mb-3 d-flex align-items-start small">
                                        <span class="badge bg-blue text-blue-fg me-2 mt-1 px-2">
                                            {{ $loop->iteration }}
                                        </span>

                                        {{ $cpLevel->cp->indikator_capaian ?? '-' }}
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        {{-- ASAL --}}
                        <div class="col-12 col-lg-6">
                            <div class="p-3 bg-green-lt border border-green-subtle rounded-3 h-100">

                                <label class="form-label fw-bold text-green small uppercase mb-2">
                                    CPMK Mata Kuliah Asal
                                </label>

                                <ul class="list-unstyled mb-0">
                                    @foreach ($mk->transferSks->cpmkItems ?? [] as $cpmkAsal)
                                    <li class="mb-2 d-flex align-items-start small text-dark">
                                        <i class="ti ti-check text-green me-2 mt-1"></i>
                                        {{ $cpmkAsal->cpmk }}
                                    </li>
                                    @endforeach
                                </ul>

                                <div class="mt-3 pt-3 border-top border-green-subtle">
                                    <div class="d-flex flex-column flex-md-row justify-content-between gap-2">

                                        <span class="text-green fw-bold small uppercase">
                                            Nilai Mata Kuliah Asal
                                        </span>

                                        <div class="d-flex gap-2 flex-wrap justify-content-start justify-content-md-end">
                                            <span class="badge bg-green text-green-fg px-3 py-1 fs-4">
                                                Angka: {{ $mk->nilai_angka ?? '-' }}
                                            </span>
                                            <span class="badge bg-green text-green-fg px-3 py-1 fs-4">
                                                Huruf: {{ $mk->nilai_huruf ?? '-' }}
                                            </span>
                                        </div>

                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>

                    {{-- ===================== TABLE CPMK ===================== --}}
                    {{-- ===================== TABLE CPMK ===================== --}}
                    <div class="card mb-4">

                        <div class="card-header bg-light">
                            <span class="fw-bold small uppercase">
                                Verifikasi CPMK & Dokumen Pendukung
                            </span>
                        </div>

                        <div class="table-responsive px-3 pb-3">

                            <table class="table table-vcenter card-table table-bordered">

                                <thead class="text-center bg-light text-nowrap">
                                    <tr>
                                        <th rowspan="2" style="width:45%">
                                            Capaian Pembelajaran Mata Kuliah
                                        </th>

                                        <th rowspan="2" style="width:30%">
                                            Dokumen Pendukung
                                        </th>

                                        <th colspan="5" style="width:25%">
                                            Verifikasi Penilaian
                                        </th>
                                    </tr>

                                    <tr>
                                        <th>V</th>
                                        <th>A</th>
                                        <th>T</th>
                                        <th>M</th>
                                        <th class="bg-blue-lt">Isi Semua</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    @php
                                    $cpLevels = $mk->cpLevel ?? [];
                                    $rowspan = count($cpLevels);

                                    $hasFormal = $formalFiles->isNotEmpty();
                                    $hasNonFormal = $nonFormalFiles->isNotEmpty();

                                    $hasFile = $hasFormal || $hasNonFormal;
                                    @endphp

                                    @forelse ($cpLevels as $index => $cpmk)

                                    <tr>

                                        {{-- CPMK --}}
                                        <td class="text-wrap">
                                            {{ $cpmk->cp->indikator_capaian ?? '-' }}
                                        </td>

                                        {{-- DOKUMEN --}}
                                        @if ($index === 0)

                                        <td rowspan="{{ $rowspan }}" class="align-top">

                                            {{-- ================= FORMAL ================= --}}
                                            <div class="p-2 rounded bg-green-lt border border-green-subtle mb-3">

                                                <div class="fw-bold text-green small uppercase mb-2">
                                                    Dokumen Formal
                                                </div>

                                                @forelse ($formalFiles as $file)

                                                <div class="d-flex align-items-center justify-content-between mb-1 {{ !$loop->last ? 'border-bottom pb-1' : '' }}">

                                                    <div class="text-truncate me-2" style="max-width: 180px;">
                                                        <i class="ti ti-file-text text-green"></i>

                                                        <small class="text-capitalize">
                                                            {{ str_replace('_', ' ', $file->label) }}
                                                        </small>
                                                    </div>

                                                    <a href="{{ asset('storage/' . $file->file_path) }}"
                                                        target="_blank"
                                                        class="btn btn-icon btn-sm btn-ghost-success">

                                                        <i class="ti ti-eye"></i>
                                                    </a>

                                                </div>

                                                @empty

                                                <small class="text-muted">
                                                    Tidak ada dokumen formal
                                                </small>

                                                @endforelse

                                            </div>

                                            {{-- ================= NON FORMAL ================= --}}
                                            <div class="p-2 rounded bg-purple-lt border border-purple-subtle">

                                                <div class="fw-bold text-purple small uppercase mb-2">
                                                    Dokumen Non Formal
                                                </div>

                                                @forelse ($nonFormalFiles as $file)

                                                <div class="d-flex align-items-center justify-content-between mb-1 {{ !$loop->last ? 'border-bottom pb-1' : '' }}">

                                                    <div class="text-truncate me-2" style="max-width: 180px;">
                                                        <i class="ti ti-file-text text-purple"></i>

                                                        <small class="text-capitalize">
                                                            {{ str_replace('_', ' ', $file->label) }}
                                                        </small>
                                                    </div>

                                                    <a href="{{ asset('storage/' . $file->file_path) }}"
                                                        target="_blank"
                                                        class="btn btn-icon btn-sm btn-ghost-secondary">

                                                        <i class="ti ti-eye"></i>
                                                    </a>

                                                </div>

                                                @empty

                                                <small class="text-muted">
                                                    Tidak ada dokumen non formal
                                                </small>

                                                @endforelse

                                            </div>

                                        </td>

                                        @endif

                                        {{-- CHECKBOX --}}
                                        @foreach (['valid','asli','terkini','memadai'] as $attr)

                                        <td class="text-center">

                                            @if($hasFile)

                                            <input
                                                type="checkbox"
                                                class="form-check-input check-target-{{ $cpmk->id }}"
                                                name="verif[{{ $cpmk->id }}][{{ $attr }}]"
                                                value="1"
                                                {{ old("verif.$cpmk->id.$attr", $cpmk->$attr) ? 'checked' : '' }}>

                                            @else

                                            <small class="text-muted">-</small>

                                            @endif

                                        </td>

                                        @endforeach

                                        {{-- CHECK ALL --}}
                                        <td class="text-center bg-light">

                                            @if($hasFile)

                                            <input
                                                type="checkbox"
                                                class="form-check-input check-row"
                                                data-id="{{ $cpmk->id }}">

                                            @endif

                                        </td>

                                    </tr>

                                    @empty

                                    <tr>
                                        <td colspan="7" class="text-center text-muted">
                                            Tidak ada data
                                        </td>
                                    </tr>

                                    @endforelse

                                </tbody>

                            </table>

                        </div>

                        {{-- ===================== FORM PENILAIAN ===================== --}}
                        <div class="m-3">

                            <div class="row g-3">

                                {{-- FORMAL --}}
                                <div class="col-12">

                                    <div class="p-3 rounded bg-green-lt border border-green-subtle">

                                        <div class="fw-bold text-green mb-3 uppercase">
                                            Penilaian Formal
                                        </div>

                                        <div class="row g-3">

                                            <div class="col-12">
                                                <label class="form-label fw-bold">
                                                    Analisis Kesenjangan
                                                </label>

                                                <textarea
                                                    name="penilaian[{{ $mk->transferSks->id }}][kesenjangan]"
                                                    rows="2"
                                                    class="form-control @error('penilaian.'.$mk->transferSks->id.'.kesenjangan') is-invalid @enderror"
                                                    placeholder="Evaluasi kesenjangan...">{{ old('penilaian.'.$mk->transferSks->id.'.kesenjangan', $mk->transferSks->kesenjangan) }}</textarea>

                                                @error('penilaian.'.$mk->transferSks->id.'.kesenjangan')
                                                <div class="text-danger small mt-1">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>

                                            <div class="col-12 col-md-4">
                                                <label class="form-label fw-bold">
                                                    Hasil Rekognisi (Skor/SKS)
                                                </label>

                                                <input
                                                    type="number"
                                                    name="penilaian[{{ $mk->transferSks->id }}][hasil]"
                                                    class="form-control @error('penilaian.'.$mk->transferSks->id.'.hasil') is-invalid @enderror"
                                                    value="{{ old('penilaian.'.$mk->transferSks->id.'.hasil', $mk->transferSks->hasil) }}"
                                                    placeholder="0-100">

                                                @error('penilaian.'.$mk->transferSks->id.'.hasil')
                                                <div class="text-danger small mt-1">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>

                                            <div class="col-12 col-md-8">
                                                <label class="form-label fw-bold">
                                                    Catatan Verifikasi Asesor
                                                </label>

                                                <textarea
                                                    name="penilaian[{{ $mk->transferSks->id }}][catatan_asesor]"
                                                    rows="2"
                                                    class="form-control @error('penilaian.'.$mk->transferSks->id.'.catatan_asesor') is-invalid @enderror"
                                                    placeholder="Catatan validasi atau rekomendasi asesor...">{{ old('penilaian.'.$mk->transferSks->id.'.catatan_asesor', $mk->transferSks->catatan_asesor) }}</textarea>

                                                @error('penilaian.'.$mk->transferSks->id.'.catatan_asesor')
                                                <div class="text-danger small mt-1">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>

                                        </div>

                                    </div>

                                </div>

                                {{-- NON FORMAL --}}
                                <div class="col-12">

                                    <div class="p-3 rounded bg-purple-lt border border-purple-subtle">

                                        <div class="fw-bold text-purple mb-3 uppercase">
                                            Penilaian Non Formal
                                        </div>

                                        <div class="row g-3">
                                            <div class="col-12">
                                                <label class="form-label fw-bold">Analisis Kesenjangan</label>
                                                <textarea
                                                    name="penilaian_nonformal[{{ $mk->transferSksNonFormal->id }}][kesenjangan]"
                                                    rows="2"
                                                    class="form-control @error('penilaian_nonformal.'.$mk->transferSksNonFormal->id.'.kesenjangan') is-invalid @enderror"
                                                    placeholder="Evaluasi kesenjangan...">{{ old('penilaian_nonformal.'.$mk->transferSksNonFormal->id.'.kesenjangan', $mk->transferSksNonFormal->kesenjangan) }}</textarea>
                                                @error('penilaian_nonformal.'.$mk->transferSksNonFormal->id.'.kesenjangan')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-12 col-md-4">
                                                <label class="form-label fw-bold">Hasil Rekognisi (Skor/SKS)</label>
                                                <input type="number"
                                                    name="penilaian_nonformal[{{ $mk->transferSksNonFormal->id }}][nilai]"
                                                    class="form-control @error('penilaian_nonformal.'.$mk->transferSksNonFormal->id.'.nilai') is-invalid @enderror"
                                                    value="{{ old('penilaian_nonformal.'.$mk->transferSksNonFormal->id.'.nilai', $mk->transferSksNonFormal->nilai) }}"
                                                    placeholder="0-100">
                                                @error('penilaian_nonformal.'.$mk->transferSksNonFormal->id.'.nilai')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-12 col-md-8">
                                                <label class="form-label fw-bold">Catatan Verifikasi Asesor</label>
                                                <textarea
                                                    name="penilaian_nonformal[{{ $mk->transferSksNonFormal->id }}][catatan_asesor]"
                                                    rows="2"
                                                    class="form-control @error('penilaian_nonformal.'.$mk->transferSksNonFormal->id.'.catatan_asesor') is-invalid @enderror"
                                                    placeholder="Catatan validasi atau rekomendasi asesor...">{{ old('penilaian_nonformal.'.$mk->transferSksNonFormal->id.'.catatan_asesor', $mk->transferSksNonFormal->catatan_asesor) }}</textarea>
                                                @error('penilaian_nonformal.'.$mk->transferSksNonFormal->id.'.catatan_asesor')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>


                    {{-- ===================== PENILAIAN ===================== --}}


                </div>
            </div>
            {{-- ===================== END CARD ===================== --}}

            @endif
            @endforeach

            {{-- ===================== FOOTER ===================== --}}
            <div class="card shadow-sm sticky-bottom py-3 px-4 bg-white border-top">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('asesmen.index') }}" class="btn btn-outline-secondary">
                        <i class="ti ti-arrow-left me-1"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-device-floppy me-1"></i> Simpan
                    </button>
                </div>
            </div>

        </form>

    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            function initCheckAll() {

                // FORMAL
                document.querySelectorAll('.check-row').forEach(master => {
                    const id = master.dataset.id;
                    const allItems = document.querySelectorAll(`.check-target-${id}`);
                    const checkedItems = document.querySelectorAll(`.check-target-${id}:checked`);

                    if (allItems.length > 0 && allItems.length === checkedItems.length) {
                        master.checked = true;
                    }
                });

                // NON FORMAL
                document.querySelectorAll('.check-row-nf').forEach(master => {
                    const id = master.dataset.id;
                    const allItems = document.querySelectorAll(`.check-target-nf-${id}`);
                    const checkedItems = document.querySelectorAll(`.check-target-nf-${id}:checked`);

                    if (allItems.length > 0 && allItems.length === checkedItems.length) {
                        master.checked = true;
                    }
                });
            }

            initCheckAll();

            // ================= FORMAL =================
            document.querySelectorAll('.check-row').forEach(rowBtn => {
                rowBtn.addEventListener('change', function() {
                    const id = this.dataset.id;
                    const targets = document.querySelectorAll(`.check-target-${id}`);
                    targets.forEach(item => {
                        item.checked = this.checked;
                    });
                });
            });

            // ================= NON FORMAL =================
            document.querySelectorAll('.check-row-nf').forEach(rowBtn => {
                rowBtn.addEventListener('change', function() {
                    const id = this.dataset.id;
                    const targets = document.querySelectorAll(`.check-target-nf-${id}`);
                    targets.forEach(item => {
                        item.checked = this.checked;
                    });
                });
            });

            // ================= AUTO SYNC =================
            document.addEventListener('change', function(e) {

                // FORMAL
                if (e.target.classList.contains('form-check-input') && e.target.className.includes('check-target-')) {
                    const match = e.target.className.match(/check-target-(\d+)/);
                    if (match) {
                        const id = match[1];
                        const master = document.querySelector(`.check-row[data-id="${id}"]`);

                        if (master) {
                            const allItems = document.querySelectorAll(`.check-target-${id}`);
                            const checkedItems = document.querySelectorAll(`.check-target-${id}:checked`);
                            master.checked = (allItems.length === checkedItems.length);
                        }
                    }
                }

                // NON FORMAL
                if (e.target.classList.contains('form-check-input') && e.target.className.includes('check-target-nf-')) {
                    const match = e.target.className.match(/check-target-nf-(\d+)/);
                    if (match) {
                        const id = match[1];
                        const master = document.querySelector(`.check-row-nf[data-id="${id}"]`);

                        if (master) {
                            const allItems = document.querySelectorAll(`.check-target-nf-${id}`);
                            const checkedItems = document.querySelectorAll(`.check-target-nf-${id}:checked`);
                            master.checked = (allItems.length === checkedItems.length);
                        }
                    }
                }

            });

        });
    </script>

    @if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const firstError = document.querySelector('.is-invalid');

            if (firstError) {

                firstError.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });

                firstError.focus();
            }
        });
    </script>
    @endif

</x-app-layout>