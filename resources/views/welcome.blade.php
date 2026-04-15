<x-app-layout>
    <x-slot name="header">
        Formulir 3: Download Template &amp; Upload File
    </x-slot>

    <div class="d-flex flex-column gap-3">
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                <li class="nav-item">
                    <a href="#tabs-home-ex1" class="nav-link active" data-bs-toggle="tab">Home</a>
                </li>
                <li class="nav-item">
                    <a href="#tabs-profile-ex1" class="nav-link" data-bs-toggle="tab">Profile</a>
                </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                <div class="tab-pane active show" id="tabs-home-ex1">
                    <h4>Home tab</h4>
                    <div>
                    Cursus turpis vestibulum, dui in pharetra vulputate id sed non turpis ultricies fringilla
                    at sed facilisis lacus pellentesque purus nibh
                    </div>
                </div>
                <div class="tab-pane" id="tabs-profile-ex1">
                    <h4>Profile tab</h4>
                    <div>
                    Fringilla egestas nunc quis tellus diam rhoncus ultricies tristique enim at diam, sem nunc
                    amet, pellentesque id egestas velit sed
                    </div>
                </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Download Template</h3>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-start gap-3">
                    <span class="avatar bg-blue-lt text-blue">
                        <i class="ti ti-download"></i>
                    </span>
                    <div>
                        <div class="fw-semibold">Download Template</div>
                        <div class="text-muted" style="font-size:12px;">Unggah kembali file setelah data selesai dilengkapi.</div>
                    </div>
                    <div class="ms-auto">
                        <button class="btn btn-outline-primary btn-sm" type="button">Download</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Upload File</h3>
            </div>
            <div class="card-body">
                <form>
                    <div class="row g-2 align-items-center">
                        <div class="col-md-8">
                            <input type="text" class="form-control" value="Plan File" readonly>
                        </div>
                        <div class="col-md-4">
                            <input type="file" class="form-control">
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="button" class="btn btn-primary px-4">Unggah</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
