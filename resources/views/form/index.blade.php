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
                </div>
            </div>
        </div>
    </div>

</x-app-layout>