<div class="card">
    <div class="card-body">
        <section>
            <header class="mb-4">
                <h3 class="card-title text-uppercase">
                    {{ __('Tanda Tangan Digital') }}
                </h3>
                <p class="text-secondary small mb-0">
                    {{ __('Unggah gambar tanda tangan Anda untuk keperluan dokumen asesmen.') }}
                </p>
            </header>

            <form method="post" action="{{ route('profile.signature.update') }}" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label class="form-label" for="signature">{{ __('File Tanda Tangan') }}</label>
                    
                    <!-- Container Flex Responsif -->
                    <div class="d-flex flex-column flex-md-row align-items-center align-items-md-start gap-3">
                        
                        <!-- Area Pratinjau (Fixed width agar tidak goyang) -->
                        <div class="flex-shrink-0">
                            @if(auth()->user()->asesor && auth()->user()->asesor->signature)
                                <div class="border rounded bg-white p-2 shadow-sm text-center" style="width: 160px;">
                                    <div class="small text-muted mb-2" style="font-size: 0.7rem; font-weight: 600;">{{ __('AKTIF') }}</div>
                                    <img src="{{ asset('storage/' . auth()->user()->asesor->signature) }}" 
                                         alt="Signature" 
                                         class="img-fluid rounded" 
                                         style="max-height: 80px; object-fit: contain;">
                                </div>
                            @else
                                <div class="border rounded border-dashed p-2 text-center text-muted bg-light" 
                                     style="width: 160px; height: 110px; display: flex; align-items: center; justify-content: center; flex-direction: column;">
                                    <svg xmlns="http://w3.org" class="icon icon-tabler icon-tabler-photo-off mb-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 8h.01" /><path d="M7 3h10a3 3 0 0 1 3 3v10m-1 3h-12a3 3 0 0 1 -3 -3v-12a3 3 0 0 1 2 -2" /><path d="M3 3l18 18" /></svg>
                                    <small class="fw-medium">{{ __('Kosong') }}</small>
                                </div>
                            @endif
                        </div>

                        <!-- Area Input File -->
                        <div class="w-100">
                            <input id="signature" 
                                   name="signature" 
                                   type="file" 
                                   class="form-control @error('signature') is-invalid @enderror" 
                                   accept="image/png, image/jpeg" 
                                   required />
                            
                            <div class="form-hint mt-2">
                                <span class="badge badge-outline text-blue">{{ __('Info') }}</span>
                                {{ __('Format PNG/JPG. Gunakan latar belakang putih polos atau transparan.') }}
                            </div>
                            
                            @error('signature')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Footer Card / Tombol Aksi -->
                <div class="d-flex flex-column flex-md-row align-items-center gap-3 mt-4 border-top pt-3">
                    <button type="submit" class="btn btn-primary w-100 w-md-auto">
                        <svg xmlns="http://w3.org" class="icon icon-tabler icon-tabler-upload" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" /><path d="M7 9l5 -5l5 5" /><path d="M12 4l0 12" /></svg>
                        {{ __('Simpan Tanda Tangan') }}
                    </button>

                    @if (session('status') === 'signature-updated')
                        <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)"
                             class="alert alert-important alert-success mb-0 py-2 px-3 small shadow-sm">
                            <div class="d-flex">
                                <div><svg xmlns="http://w3.org" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg></div>
                                <div>{{ __('Berhasil diperbarui!') }}</div>
                            </div>
                        </div>
                    @endif
                </div>
            </form>
        </section>
    </div>
</div>
