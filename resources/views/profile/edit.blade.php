<x-app-layout>
    <!-- Page Header -->
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        {{ __('Profile') }}
                    </h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Page Body -->
    <div class="page-body">
        <div class="container">
            <div class="row row-cards">
                <!-- Update Password -->
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    @include('profile.partials.update-password-form')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @if(Auth::user()->role->role === 'Asesor')
            <div class="row row-cards mt-2">
                <div class="col-12">
                    @include('profile.partials.update-signature-form')
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>