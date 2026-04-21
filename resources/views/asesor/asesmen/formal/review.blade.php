<x-app-layout>
    <div class="container-xl p-2">
        <div class="mb-3">
            {{-- Menggunakan variabel namaMahasiswa dari Controller --}}
            <h2 class="text-2xl font-bold text-gray-800">Review Asesmen: {{ $namaMahasiswa }}</h2>
            <p class="text-muted small">Silakan berikan penilaian pada setiap Capaian Pembelajaran (CPMK).</p>
        </div>

        {{-- Route update diarahkan ke fungsi simpan kolektif --}}
        <form action="{{ route('asesmen.formal.update') }}" method="POST">
            @csrf
            @method('PUT')

            @foreach($pilihanMk as $mk)
            @if($mk->transferSks)
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-blue-lt">
                    <div>
                        <div class="text-uppercase fw-bold text-blue small">Mata Kuliah Target</div>
                        <h3 class="card-title fw-bold text-dark">{{ $mk->nama_mk }} ({{$mk->kode_mk}})</h3>
                    </div>
                </div>

                <div class="card-body py-3">
                    <div class="row g-3 mb-4">
                        <!-- CPMK TARGET -->
                        <div class="col-12 col-lg-6">
                            <div class="p-3 bg-blue-lt border border-blue-subtle rounded-3 h-100">
                                <label class="form-label fw-bold text-blue small uppercase mb-2">CPMK Mata Kuliah Target</label>
                                <ul class="list-unstyled mb-0">
                                    @foreach($mk->mataKuliah->cps ?? [] as $cpTarget)
                                    <li class="mb-3 d-flex align-items-start small">
                                        <span class="badge bg-blue text-blue-fg me-2 mt-1 px-2">{{ $loop->iteration }}</span>
                                        {{ $cpTarget->indikator_capaian }}
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        <!-- CPMK ASAL -->
                        <div class="col-12 col-lg-6">
                            <div class="p-3 bg-green-lt border border-green-subtle rounded-3 h-100">
                                <label class="form-label fw-bold text-green small uppercase mb-2">CPMK Mata Kuliah Asal</label>
                                <ul class="list-unstyled mb-0">
                                    @foreach($mk->transferSks->cpmkItems ?? [] as $cpmkAsal)
                                    <li class="mb-2 d-flex align-items-start small text-dark">
                                        <i class="ti ti-check text-green me-2 mt-1"></i>
                                        {{ $cpmkAsal->cpmk }}
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Area Input Penilaian -->
                    <div class="border-top pt-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-bold">Kesenjangan</label>
                                <textarea
                                    name="penilaian[{{ $mk->transferSks->id }}][kesenjangan]"
                                    rows="2"
                                    class="form-control"
                                    placeholder="Deskripsi kesenjangan...">{{ old('penilaian.'.$mk->transferSks->id.'.kesenjangan', trim($mk->transferSks->kesenjangan ?? '')) }}</textarea>
                                @error('penilaian.'.$mk->transferSks->id.'.kesenjangan')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label fw-bold">Hasil</label>
                                <input type="number"
                                    name="penilaian[{{ $mk->transferSks->id }}][hasil]"
                                    class="form-control"
                                    value="{{ old('penilaian.'.$mk->transferSks->id.'.hasil', $mk->transferSks->hasil) }}"
                                    min="1" max="100"
                                    placeholder="Skor">
                                @error('penilaian.'.$mk->transferSks->id.'.hasil')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-8">
                                <label class="form-label fw-bold">Catatan Asesor</label>
                                <textarea
                                    name="penilaian[{{ $mk->transferSks->id }}][catatan_asesor]"
                                    rows="2"
                                    class="form-control"
                                    placeholder="Catatan tambahan...">{{ old('penilaian.'.$mk->transferSks->id.'.catatan_asesor', trim($mk->transferSks->catatan_asesor ?? '')) }}</textarea>
                                @error('penilaian.'.$mk->transferSks->id.'.catatan_asesor')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            @endforeach

            <div class="card shadow-sm sticky-bottom py-3 px-4 bg-white border-top">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('asesmen.formal') }}" class="btn btn-outline-secondary">
                        <i class="ti ti-arrow-left me-1"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-device-floppy me-1"></i> Simpan
                    </button>
                </div>
            </div>
        </form>

    </div>
</x-app-layout>