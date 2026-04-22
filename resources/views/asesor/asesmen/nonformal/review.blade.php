<x-app-layout>
    <div class="container-xl p-2">
        <div class="mb-3">
            <h2 class="text-2xl font-bold text-gray-800">Review Asesmen Non-Formal: {{ $namaMahasiswa }}</h2>
            <p class="text-muted small">Evaluasi bukti portofolio terhadap Capaian Pembelajaran (CPMK) mata kuliah pilihan.</p>
        </div>

        {{-- Route disesuaikan untuk Non-formal --}}
        <form action="{{ route('asesmen.nonformal.update') }}" method="POST">
            @csrf
            @method('PUT')

            @foreach($pilihanMk as $mk)
            {{-- Pastikan baris transfer non-formal ada, jika belum ada bisa dibuatkan otomatis di Controller --}}
            @if($mk->transferSksNonFormal)
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-purple-lt">
                    <div>
                        <div class="text-uppercase fw-bold text-purple small">Mata Kuliah Target</div>
                        <h3 class="card-title fw-bold text-dark">{{ $mk->nama_mk }} ({{$mk->kode_mk}})</h3>
                    </div>
                </div>

                <div class="card-body py-3">
                    <div class="row g-3 mb-4">
                        <!-- 1. CPMK TARGET (Dari Prodi yang Dituju) -->
                        <div class="col-12 col-lg-6">
                            <div class="p-3 bg-blue-lt border border-blue-subtle rounded-3 h-100">
                                <label class="form-label fw-bold text-blue small uppercase mb-2">Capaian Pembelajaran (CPMK)</label>
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

                        <!-- 2. BUKTI PEMOHON (Berdasarkan Gambar: Ijazah, Sertifikat, Kerja, dll) -->
                        <div class="col-12 col-lg-6">
                            <div class="p-3 bg-purple-lt border border-purple-subtle rounded-3 h-100">
                                <label class="form-label fw-bold text-purple small uppercase mb-2">Bukti Portofolio Mahasiswa</label>
                                <div class="table-responsive">
                                    <table class="table table-vcenter table-sm card-table">
                                        <thead>
                                            <tr>
                                                <th class="small">#</th>
                                                <th class="small">Jenis Bukti</th>
                                                <th class="small text-center">File</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($mk->attachment ?? [] as $index => $file)
                                            <tr>
                                                <td class="small">
                                                    {{ $index  + 1}} .
                                                </td>

                                                <td class="small">
                                                    <span class="badge badge-outline text-purple text-uppercase" style="font-size: 10px;">
                                                        {{-- Di sini langsung $file->label, tidak perlu $file->attachment->label --}}
                                                        {{ str_replace('_', ' ', $file->label ?? 'Dokumen') }}
                                                    </span>
                                                    <div class="text-muted mt-1" style="font-size: 11px;">
                                                        {{ $file->file_name }}
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    @if($file->file_path)
                                                    <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank" class="btn btn-icon btn-sm btn-ghost-primary">
                                                        <i class="ti ti-eye"></i>
                                                    </a>
                                                    @else
                                                    <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="2" class="text-center text-muted small">Tidak ada bukti dilampirkan</td>
                                            </tr>
                                            @endforelse

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Area Input Penilaian Sesuai Gambar (Kesenjangan, Hasil, Catatan) -->
                    <div class="border-top pt-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-bold">Analisis Kesenjangan</label>
                                <textarea
                                    name="penilaian[{{ $mk->transferSksNonFormal->id }}][kesenjangan]"
                                    rows="2"
                                    class="form-control"
                                    placeholder="Evaluasi kesenjangan antara bukti portofolio dengan CPMK...">{{ old('penilaian.'.$mk->transferSksNonFormal->id.'.kesenjangan', $mk->transferSksNonFormal->kesenjangan) }}</textarea>
                                @error('penilaian.'.$mk->transferSksNonFormal->id.'.kesenjangan')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label fw-bold">Hasil Rekognisi (Skor/SKS)</label>
                                <input type="number"
                                    name="penilaian[{{ $mk->transferSksNonFormal->id }}][nilai]"
                                    class="form-control"
                                    value="{{ old('penilaian.'.$mk->transferSksNonFormal->id.'.nilai', $mk->transferSksNonFormal->nilai) }}"
                                    placeholder="0-100">
                                @error('penilaian.'.$mk->transferSksNonFormal->id.'.nilai')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-8">
                                <label class="form-label fw-bold">Catatan Verifikasi Asesor</label>
                                <textarea
                                    name="penilaian[{{ $mk->transferSksNonFormal->id }}][catatan_asesor]"
                                    rows="2"
                                    class="form-control"
                                    placeholder="Catatan keabsahan bukti atau rekomendasi...">{{ old('penilaian.'.$mk->transferSksNonFormal->id.'.catatan_asesor', $mk->transferSksNonFormal->catatan_asesor) }}</textarea>
                                @error('penilaian.'.$mk->transferSksNonFormal->id.'.catatan_asesor')
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
                    <a href="{{ route('asesmen.nonformal') }}" class="btn btn-outline-secondary">
                        <i class="ti ti-arrow-left me-1"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-purple text-white">
                        <i class="ti ti-device-floppy me-1"></i> Simpan
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>