<x-app-layout>

    @php
    $locked = !$mahasiswa->is_editable;
    @endphp

    <div class="modal fade" id="modalLocked" tabindex="-1"
        data-bs-backdrop="static"
        data-bs-keyboard="false">

        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content text-center p-4">

                <h5 class="text-danger fw-bold mb-3">
                    <i class="ti ti-lock fs-4 me-2"></i>
                    Data Telah Dikunci
                </h5>

                <p class="mb-2">
                    Data tidak dapat diubah karena proses pengisian telah diselesaikan.
                </p>

                <p class="small text-muted">
                    Jika terdapat kebutuhan perubahan data, silakan hubungi admin untuk bantuan lebih lanjut.
                </p>
                <div class="d-flex justify-content-center gap-2 mt-3">


                    <!-- logout di modal -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-danger">
                            <i class="ti ti-logout me-2"></i>
                            Logout
                        </button>
                    </form>

                </div>

            </div>

        </div>
    </div>

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
    @if($locked)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let modal = new bootstrap.Modal(
                document.getElementById('modalLocked'), {
                    backdrop: 'static',
                    keyboard: false
                }
            );

            modal.show();
        });
    </script>
    @endif
</x-app-layout>