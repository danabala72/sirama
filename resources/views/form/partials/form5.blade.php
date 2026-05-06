@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

<form method="POST" action="{{ route('cp-level.store') }}">
    @csrf

    <div class="accordion" id="accordionMK">

        @foreach($mataKuliahPilihan as $index => $mk)
        <div class="accordion-item">

            @php
            $cps = $mk->mataKuliah->cps ?? [];
            $totalCp = count($cps);

            $checkedCount = collect($mk->cpLevel ?? [])
            ->where('level_kompetensi', 1)
            ->count();

            $isDanger = $totalCp === 0 || $checkedCount === 0;
            $isSuccess = $totalCp > 0 && $checkedCount === $totalCp;
            $isWarning = $checkedCount > 0 && $checkedCount < $totalCp;
                @endphp

                <!-- HEADER -->
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed  {{ $isDanger ? 'text-danger' : ($isWarning ? 'text-warning' : 'text-success') }}"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#collapse{{ $mk->id }}">

                        {{$index + 1}}. {{ $mk->nama_mk }}
                        @if($isDanger)
                        <i class="ti ti-alert-triangle ms-2"></i>
                        @elseif($isWarning)
                        <i class="ti ti-alert-circle ms-2"></i>
                        @else
                        <i class="ti ti-check ms-2 text-success"></i>
                        @endif
                    </button>
                </h2>

                <!-- BODY -->
                <div id="collapse{{ $mk->id }}"
                    class="accordion-collapse collapse"
                    data-bs-parent="#accordionMK">

                    <div class="accordion-body">

                        @if(!empty($mk->mataKuliah->cps) && count($mk->mataKuliah->cps) > 0)
                        <div class="row align-items-center mb-4">

                            <!-- kiri (kosong, biar sejajar dengan text CP) -->
                            <div class="col-md-8"></div>

                            <!-- kanan (toggle all) -->
                            <div class="col-md-4 d-flex justify-content-start">
                                <label class="form-check m-0">
                                    <input class="form-check-input btn-toggle-all" type="checkbox">
                                    <span class="form-check-label">Pilih Semua</span>
                                </label>
                            </div>

                        </div>
                        @endif

                        @php
                        $levels = [];
                        foreach($mk->cpLevel ?? [] as $l){
                        $levels[$l->cp_mata_kuliah_id] = $l->level_kompetensi;
                        }
                        @endphp

                        @forelse($mk->mataKuliah->cps ?? [] as $i => $cp)

                        <div class="row align-items-center my-3 pb-2 {{ !$loop->last ? 'border-bottom' : '' }}">

                            <!-- CP -->
                            <div class="col-md-8">
                                <div class="fw-medium">
                                    {{ $i + 1 }}. {{ $cp->indikator_capaian }}
                                </div>
                            </div>

                            <!-- SWITCH -->
                            <div class="col-md-4">
                                <label class="form-check form-switch">

                                    <input class="form-check-input cp-item"
                                        type="checkbox"
                                        name="cp[{{ $mk->id }}][{{ $cp->id }}]"
                                        value="1"
                                        {{ isset($levels[$cp->id]) && $levels[$cp->id] ? 'checked' : '' }}>

                                    <span class="form-check-label">Tercapai</span>
                                </label>
                            </div>

                        </div>

                        @empty

                        <div class="text-sm text-center py-3 text-danger">
                            <i>Tidak ada indikator capaian untuk mata kuliah ini</i>
                        </div>

                        @endforelse

                    </div>
                </div>

        </div>
        @endforeach

    </div>

    <button type="submit" class="btn btn-primary mt-3">
        Simpan
    </button>
</form>

<div class="mt-4 d-flex gap-2">

    <a
        href="{{ route('form.step', 'step=6') }}"
        class="btn btn-outline-primary"
        title="Lanjut ke Formulir 6">
        <i class="ti ti-arrow-right me-1"></i>
        Ke Form 6
    </a>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.accordion-body').forEach(container => {

        const toggleAll = container.querySelector('.btn-toggle-all');
        const items = container.querySelectorAll('.cp-item');

        if (!toggleAll || items.length === 0) return;

        const updateState = () => {
            const total = items.length;
            const checked = Array.from(items).filter(i => i.checked).length;

            if (checked === 0) {
                toggleAll.checked = false;
                toggleAll.indeterminate = false;
            } else if (checked === total) {
                toggleAll.checked = true;
                toggleAll.indeterminate = false;
            } else {
                toggleAll.checked = false;
                toggleAll.indeterminate = true;
            }
        };

        // klik "Pilih Semua"
        toggleAll.addEventListener('change', function () {
            items.forEach(i => i.checked = this.checked);
            updateState();
        });

        // klik item satuan
        items.forEach(item => {
            item.addEventListener('change', updateState);
        });

        updateState(); // init
    });

});
</script>