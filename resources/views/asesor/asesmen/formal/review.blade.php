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

        <form action="{{ route('asesmen.formal.update') }}" method="POST">
            @csrf
            @method('PUT')

            @foreach ($pilihanMk as $mk)
                @if ($mk->transferSks)

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
                                            @foreach ($mk->mataKuliah->cps ?? [] as $cpTarget)
                                                <li class="mb-3 d-flex align-items-start small">
                                                    <span class="badge bg-blue text-blue-fg me-2 mt-1 px-2">
                                                        {{ $loop->iteration }}
                                                    </span>
                                                    {{ $cpTarget->indikator_capaian }}
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
                            <div class="row g-3 mb-4">

                                {{-- ❗ FIX: tidak ada foreach kedua di sini --}}
                                <div class="card mb-4">

                                    <div class="card-header bg-light">
                                        <span class="text-green fw-bold small uppercase">
                                            Verifikasi Asesmen Mandiri: {{ $mk->nama_mk }}
                                        </span>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-vcenter card-table table-bordered">

                                            <thead class="text-center bg-light text-nowrap">
                                                <tr>
                                                    <th rowspan="2" style="width:45%">
                                                        Capaian Pembelajaran Mata Kuliah
                                                    </th>
                                                    <th rowspan="2" style="width:30%">
                                                        Dokumen Pendukung
                                                    </th>
                                                    <th colspan="4" style="width:25%">
                                                        Verifikasi Penilaian
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th>V</th>
                                                    <th>A</th>
                                                    <th>T</th>
                                                    <th>M</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @forelse ($mk->cpLevel ?? [] as $cpmk)

                                                    @php
                                                        $hasFile = $mk->attachment->isNotEmpty();
                                                    @endphp

                                                    <tr>

                                                        <td class="text-wrap">
                                                            <div class="fw-thin text-muted">
                                                                {{ $cpmk->cp->indikator_capaian ?? 'Indikator tidak ditemukan' }}
                                                            </div>
                                                        </td>

                                                        <td>
                                                            @forelse ($mk->attachment as $file)
                                                                <div class="d-flex align-items-center justify-content-between mb-1 border-bottom pb-1">
                                                                    <div class="text-truncate me-2" style="max-width: 140px;">
                                                                        <i class="ti ti-file-text text-primary"></i>
                                                                        <small class="text-capitalize">
                                                                            {{ str_replace('_', ' ', $file->label) }}
                                                                        </small>
                                                                    </div>
                                                                    <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank"
                                                                       class="btn btn-icon btn-sm btn-ghost-primary">
                                                                        <i class="ti ti-eye"></i>
                                                                    </a>
                                                                </div>
                                                            @empty
                                                                <small class="text-muted italic">
                                                                    Tidak ada dokumen
                                                                </small>
                                                            @endforelse
                                                        </td>

                                                        @foreach (['valid', 'asli', 'terkini', 'memadai'] as $attr)
                                                            <td class="text-center p-1">
                                                                @if ($hasFile)
                                                                    <input type="checkbox"
                                                                        class="form-check-input m-0"
                                                                        name="verif[{{ $cpmk->id }}][{{ $attr }}]"
                                                                        value="1"
                                                                        {{ old("verif.$cpmk->id.$attr", $cpmk->$attr) ? 'checked' : '' }}>
                                                                @else
                                                                    <small class="text-muted">-</small>
                                                                @endif
                                                            </td>
                                                        @endforeach

                                                    </tr>

                                                @empty
                                                    <tr>
                                                        <td colspan="6" class="text-center text-muted">
                                                            Data CPMK tidak tersedia untuk mata kuliah ini.
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>

                                        </table>
                                    </div>

                                </div>

                            </div>

                            {{-- ===================== PENILAIAN ===================== --}}
                            <div class="border-top pt-4">

                                <div class="row g-3">

                                    <div class="col-12">
                                        <label class="form-label fw-bold">Kesenjangan</label>
                                        <textarea name="penilaian[{{ $mk->transferSks->id }}][kesenjangan]"
                                            rows="2"
                                            class="form-control"
                                            placeholder="Deskripsi kesenjangan...">{{ old('penilaian.'.$mk->transferSks->id.'.kesenjangan', trim($mk->transferSks->kesenjangan ?? '')) }}</textarea>
                                    </div>

                                    <div class="col-12 col-md-4">
                                        <label class="form-label fw-bold">Hasil</label>
                                        <input type="number"
                                            name="penilaian[{{ $mk->transferSks->id }}][hasil]"
                                            class="form-control"
                                            value="{{ old('penilaian.'.$mk->transferSks->id.'.hasil', $mk->transferSks->hasil) }}"
                                            min="1" max="100">
                                    </div>

                                    <div class="col-12 col-md-8">
                                        <label class="form-label fw-bold">Catatan Asesor</label>
                                        <textarea name="penilaian[{{ $mk->transferSks->id }}][catatan_asesor]"
                                            rows="2"
                                            class="form-control"
                                            placeholder="Catatan tambahan...">{{ old('penilaian.'.$mk->transferSks->id.'.catatan_asesor', trim($mk->transferSks->catatan_asesor ?? '')) }}</textarea>
                                    </div>

                                </div>

                            </div>

                        </div>
                    </div>
                    {{-- ===================== END CARD ===================== --}}

                @endif
            @endforeach

            {{-- ===================== FOOTER ===================== --}}
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
