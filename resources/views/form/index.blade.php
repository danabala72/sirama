<x-app-layout>

    <div class="d-flex flex-column gap-3">
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs">
                    @foreach(range(1, 6) as $i)
                    @php
                    $isDisabled = ($i > 1 && !isset($mahasiswa->id));
                    @endphp

                    <li class="nav-item">
                        <a href="{{ $isDisabled ? '#' : route('form.step', 'step=' . $i) }}"
                            class="nav-link {{ $step == $i ? 'active' : '' }} {{ $isDisabled ? 'disabled' : '' }}"
                            @if($isDisabled) tabindex="-1" aria-disabled="true" style="cursor: not-allowed;" @endif>
                            Formulir {{ $i }}
                        </a>
                    </li>
                    @endforeach
                </ul>

            </div>

            <div class="card-body">
                <div class="tab-content">

                    <div class="tab-pane active show" id="form-tab">
                        <h4>{{ $title }}</h4>

                        @includeIf('form.partials.form'.$step)

                    </div>

                    <div class="tab-pane" id="info-tab">
                        <h4>Petunjuk Form {{ $step }}</h4>

                        @if($step == 1)
                        <p>Isi rincian data peserta atau calon peserta.</p>
                        @elseif($step == 2)
                        <p>Masukkan mata kuliah yang direkognisi.</p>
                        @elseif($step == 3)
                        <p>Masukkan pelatihan dan pengalaman kerja.</p>
                        @elseif($step == 4)
                        <p>Isi formulir riwayat hidup.</p>
                        @elseif($step == 5)
                        <p>Periksa kembali seluruh data sebelum submit.</p>
                        @endif

                    </div>

                </div>
            </div>
        </div>
    </div>

</x-app-layout>