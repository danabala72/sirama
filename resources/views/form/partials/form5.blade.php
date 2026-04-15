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
                $hasChecked = collect($mk->cpLevel ?? [])->contains('level_kompetensi', 1);
                $isDanger = count($cps) === 0 || !$hasChecked;
            @endphp

            <!-- HEADER -->
            <h2 class="accordion-header">
                <button class="accordion-button collapsed {{ $isDanger ? 'text-danger' : '' }}"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#collapse{{ $mk->id }}">

                    {{$index + 1}}. {{ $mk->nama_mk }} @if($isDanger)
                        <i class="ti ti-alert-triangle ms-2"></i>
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

                    @php
                    $levels = [];
                    foreach($mk->cpLevel ?? [] as $l){
                        $levels[$l->cp_mata_kuliah_id] = $l->level_kompetensi;
                    }
                    @endphp

                    @forelse($mk->mataKuliah->cps ?? [] as $i => $cp)

                    <div class="row align-items-center my-3 border-bottom pb-2">

                        <!-- CP -->
                        <div class="col-md-8">
                            <div class="fw-medium">
                                {{ $i + 1 }}. {{ $cp->indikator_capaian }}
                            </div>
                        </div>

                        <!-- SWITCH -->
                        <div class="col-md-4">
                            <label class="form-check form-switch">

                                <input class="form-check-input"
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
        title="Lanjut ke Formulir 6"
    >
        <i class="ti ti-arrow-right me-1"></i>
        Ke Formulir 6
    </a>
</div>