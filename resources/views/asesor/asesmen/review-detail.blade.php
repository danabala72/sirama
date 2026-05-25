<x-app-layout>
    <div class="container-xl p-2">

        <div class="mb-3">
            <h2 class="text-2xl font-bold text-gray-800">
                Asesmen  {{ $mkSpesifik->nama_mk }} 
            </h2>
            <div class="card mb-4 shadow-sm">

                <div class="card-header bg-light">
                    <div class="fw-bold text-uppercase small">
                        Informasi Mahasiswa
                    </div>
                </div>

                <div class="card-body">

                    <div class="row g-3">

                        {{-- IDENTITAS --}}
                        <div class="col-md-6">
                            <div class="p-3 border rounded bg-white">

                                <div class="fw-bold mb-2">Identitas</div>

                                <div class="small mb-1">
                                    <span class="text-muted">Nama:</span>
                                    <span class="fw-semibold">{{ $mahasiswa->name ?? '' }}</span>
                                </div>

                                <div class="small mb-1">
                                    <span class="text-muted">Jenis Kelamin:</span>
                                    <span class="fw-semibold">
                                        {{ $mahasiswa->jenis_kelamin ? ($mahasiswa->jenis_kelamin === 'L' ? 'Laki-Laki' : 'Perempuan') : '' }}
                                    </span>
                                </div>

                                <div class="small mb-1">
                                    <span class="text-muted">TTL:</span>
                                    <span class="fw-semibold">
                                        {{ $mahasiswa->tempat_lahir ?? '' }},
                                        {{ $mahasiswa->tgl_lahir ?? '' }}
                                    </span>
                                </div>

                                <div class="small">
                                    <span class="text-muted">Status:</span>
                                    <span class="fw-semibold">{{ $mahasiswa->status_perkawinan ?? '' }}</span>
                                </div>

                            </div>
                        </div>

                        {{-- KONTAK --}}
                        <div class="col-md-6">
                            <div class="p-3 border rounded bg-white">

                                <div class="fw-bold mb-2">Kontak</div>

                                <div class="small mb-1">
                                    <span class="text-muted">Email:</span>
                                    <span class="fw-semibold">{{ $mahasiswa->email ?? '' }}</span>
                                </div>

                                <div class="small mb-1">
                                    <span class="text-muted">No HP:</span>
                                    <span class="fw-semibold">{{ $mahasiswa->no_hp ?? '' }}</span>
                                </div>

                                <div class="small">
                                    <span class="text-muted">Alamat:</span><br>
                                    <span class="fw-semibold">{{ $mahasiswa->alamat_rumah ?? '' }}</span>
                                </div>

                            </div>
                        </div>

                        {{-- PENDIDIKAN --}}
                        <div class="col-12">
                            <div class="p-3 border rounded bg-white">

                                <div class="fw-bold mb-2">Riwayat Pendidikan</div>

                                <div class="row g-2 small">

                                    <div class="col-md-6">
                                        <span class="text-muted">SMA:</span>
                                        <span class="fw-semibold">{{ $mahasiswa->nama_sekolah ?? '' }}</span>

                                        <div class="text-muted">
                                            {{ $mahasiswa->alamat_sekolah ?? '' }}
                                            {{ $mahasiswa->tahun_lulus_sekolah ? '(' . $mahasiswa->tahun_lulus_sekolah . ')' : '' }}
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <span class="text-muted">Perguruan Tinggi:</span>
                                        <span class="fw-semibold">{{ $mahasiswa->nama_pt ?? '' }}</span>

                                        <div class="text-muted">
                                            {{ $mahasiswa->prodi_pt ?? '' }}
                                            {{ $mahasiswa->program_pt ? ' - ' . $mahasiswa->program_pt : '' }}
                                            {{ $mahasiswa->tahun_lulus_pt ? '(' . $mahasiswa->tahun_lulus_pt . ')' : '' }}
                                        </div>
                                    </div>

                                </div>

                            </div>
                        </div>

                    </div>

                </div>
            </div>
            <p class="text-muted small">
                Silakan berikan penilaian pada setiap Capaian Pembelajaran (CPMK).
            </p>
        </div>

        <form action="{{ route('asesmen.update', $mahasiswa->id) }}" method="POST">
            @csrf
            @method('PUT')

            @php
            $mk = $mkSpesifik;

            $attachments = collect($mk->attachment ?? []);

            $formalFiles = $attachments->filter(function($file){
            $label = strtolower($file->label);
            return str_contains($label, 'ijazah') || str_contains($label, 'transkrip');
            });

            $nonFormalFiles = $attachments->filter(function($file){
            $label = strtolower($file->label);
            return !str_contains($label, 'ijazah') && !str_contains($label, 'transkrip');
            });
            @endphp

            @if ($mk->transferSks)

            {{-- ===================== CARD MK ===================== --}}
            <div class="mb-4">

               

                <div class="">

                    {{-- ===================== CPMK TARGET & ASAL ===================== --}}
                    <div class="row g-3 mb-4">

                        {{-- TARGET --}}
                        <div class="col-12 col-lg-6">
                            <div class="p-3 bg-blue-lt border border-blue-subtle rounded-3 h-100">
                                <label class="form-label fw-bold text-blue small uppercase mb-2">
                                    CPMK Mata Kuliah Target
                                </label>
                                <ul class="list-unstyled mb-0">
                                    @foreach ($mk->cpLevels ?? [] as $cpLevel)
                                    <li class="mb-3 d-flex align-items-start small">
                                        <span class="badge bg-blue text-blue-fg me-2 mt-1 px-2">
                                            {{ $loop->iteration }}
                                        </span>

                                        {{ $cpLevel->cp->indikator_capaian ?? '-' }}
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
                    {{-- ===================== TABLE CPMK ===================== --}}
                    <div class="card mb-4">

                        <div class="card-header bg-light d-flex justify-content-between align-items-center">

                            <span class="fw-bold small uppercase">
                                Verifikasi CPMK & Dokumen Pendukung
                            </span>

                            <div class="form-check mb-0">
                                <input
                                    type="checkbox"
                                    class="form-check-input check-all-mk"
                                    data-mk="{{ $mk->id }}"
                                    id="check-all-{{ $mk->id }}">

                                <label class="form-check-label fw-bold ms-1"
                                    for="check-all-{{ $mk->id }}">
                                    Isi Semua
                                </label>
                            </div>

                        </div>

                        <div class="table-responsive px-3 pb-3">

                            <table class="table table-vcenter card-table table-bordered">

                                <thead class="text-center bg-light text-nowrap">
                                    <tr>
                                        <th rowspan="2" style="width:45%">
                                            Capaian Pembelajaran Mata Kuliah
                                        </th>

                                        <th rowspan="2" style="width:30%">
                                            Dokumen Pendukung
                                        </th>

                                        <th colspan="5" style="width:25%">
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

                                    @php
                                    $cpLevels = $mk->cpLevels ?? [];
                                    $rowspan = count($cpLevels);

                                    $hasFormal = $formalFiles->isNotEmpty();
                                    $hasNonFormal = $nonFormalFiles->isNotEmpty();

                                    $hasFile = $hasFormal || $hasNonFormal;
                                    @endphp

                                    @forelse ($cpLevels as $index => $cpmk)

                                    <tr>

                                        {{-- CPMK --}}
                                        <td class="text-wrap">
                                            {{ $cpmk->cp->indikator_capaian ?? '-' }}
                                        </td>

                                        {{-- DOKUMEN --}}
                                        @if ($index === 0)

                                        <td rowspan="{{ $rowspan }}" class="align-top">

                                            {{-- ================= FORMAL ================= --}}
                                            <div class="p-2 rounded bg-green-lt border border-green-subtle mb-3">

                                                <div class="fw-bold text-green small uppercase mb-2">
                                                    Dokumen Formal
                                                </div>

                                                @forelse ($formalFiles as $file)

                                                <div class="d-flex align-items-center justify-content-between mb-1 {{ !$loop->last ? 'border-bottom pb-1' : '' }}">

                                                    <div class="text-truncate me-2" style="max-width: 180px;">
                                                        <i class="ti ti-file-text text-green"></i>

                                                        <small class="text-capitalize">
                                                            {{ str_replace('_', ' ', $file->label) }}
                                                        </small>
                                                    </div>

                                                    <a href="{{ asset('storage/' . $file->file_path) }}"
                                                        target="_blank"
                                                        class="btn btn-icon btn-sm btn-ghost-success">

                                                        <i class="ti ti-eye"></i>
                                                    </a>

                                                </div>

                                                @empty

                                                <small class="text-muted">
                                                    Tidak ada dokumen formal
                                                </small>

                                                @endforelse

                                            </div>

                                            {{-- ================= NON FORMAL ================= --}}
                                            <div class="p-2 rounded bg-purple-lt border border-purple-subtle">

                                                <div class="fw-bold text-purple small uppercase mb-2">
                                                    Dokumen Non Formal
                                                </div>

                                                @forelse ($nonFormalFiles as $file)

                                                <div class="d-flex align-items-center justify-content-between mb-1 {{ !$loop->last ? 'border-bottom pb-1' : '' }}">

                                                    <div class="text-truncate me-2" style="max-width: 180px;">
                                                        <i class="ti ti-file-text text-purple"></i>

                                                        <small class="text-capitalize">
                                                            {{ str_replace('_', ' ', $file->label) }}
                                                        </small>
                                                    </div>

                                                    <a href="{{ asset('storage/' . $file->file_path) }}"
                                                        target="_blank"
                                                        class="btn btn-icon btn-sm btn-ghost-secondary">

                                                        <i class="ti ti-eye"></i>
                                                    </a>

                                                </div>

                                                @empty

                                                <small class="text-muted">
                                                    Tidak ada dokumen non formal
                                                </small>

                                                @endforelse

                                            </div>

                                        </td>

                                        @endif

                                        {{-- CHECKBOX --}}
                                        @foreach (['valid', 'asli', 'terkini', 'memadai'] as $attr)
                                        <td class="text-center">

                                            @if($hasFile)
                                            @php
                                            // 1. Ambil objek relasi penilaian milik asesor yang sedang login
                                            $penilaianAsesor = $cpmk->penilaianAsesorLogin;
                                            $dbValue = $penilaianAsesor ? $penilaianAsesor->$attr : 0;
                                            @endphp
                                            <input
                                                type="hidden"
                                                name="verif[{{ $cpmk->id }}][{{ $attr }}]"
                                                value="0">


                                            <input
                                                type="checkbox"
                                                class="form-check-input check-target"
                                                name="verif[{{ $cpmk->id }}][{{ $attr }}]"
                                                value="1"
                                                data-mk="{{ $mk->id }}"
                                                data-cpmk="{{ $cpmk->id }}"
                                                {{ old("verif.$cpmk->id.$attr", $dbValue) == 1 ? 'checked' : '' }}>

                                            @else

                                            <small class="text-muted">-</small>

                                            @endif

                                        </td>
                                        @endforeach

                                    </tr>

                                    @empty

                                    <tr>
                                        <td colspan="7" class="text-center text-muted">
                                            Tidak ada data
                                        </td>
                                    </tr>

                                    @endforelse

                                </tbody>

                            </table>

                        </div>

                        {{-- ===================== FORM PENILAIAN ===================== --}}
                        <div class="m-3">

                            <div class="row g-3">

                                {{-- FORMAL --}}
                                @php
                                $transferSks = $mk->transferSks;
                                $transferSksId = $transferSks?->id;

                                $penilaian = $transferSks?->penilaian->first(); // sudah difilter di controller
                                @endphp

                                @if($transferSksId)
                                <div class="col-12">
                                    <div class="p-3 rounded bg-green-lt border border-green-subtle">

                                        <div class="fw-bold text-green mb-3 uppercase">
                                            Penilaian Formal
                                        </div>

                                        <div class="row g-3">

                                            {{-- KESENJANGAN --}}
                                            <div class="col-12">
                                                <label class="form-label fw-bold">Analisis Kesenjangan</label>

                                                <textarea
                                                    name="penilaian[{{ $transferSksId }}][kesenjangan]"
                                                    rows="2"
                                                    class="form-control @error('penilaian.'.$transferSksId.'.kesenjangan') is-invalid @enderror"
                                                    placeholder="Evaluasi kesenjangan...">{{ old('penilaian.'.$transferSksId.'.kesenjangan', $penilaian?->kesenjangan) }}</textarea>

                                                @error('penilaian.'.$transferSksId.'.kesenjangan')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- HASIL --}}
                                            <div class="col-12 col-md-4">
                                                <label class="form-label fw-bold">Hasil Rekognisi (Skor/SKS)</label>

                                                <input
                                                    type="number"
                                                    name="penilaian[{{ $transferSksId }}][hasil]"
                                                    class="form-control @error('penilaian.'.$transferSksId.'.hasil') is-invalid @enderror"
                                                    value="{{ old('penilaian.'.$transferSksId.'.hasil', $penilaian?->hasil) }}"
                                                    placeholder="0-100">

                                                @error('penilaian.'.$transferSksId.'.hasil')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- CATATAN --}}
                                            <div class="col-12 col-md-8">
                                                <label class="form-label fw-bold">Catatan Verifikasi Asesor</label>

                                                <textarea
                                                    name="penilaian[{{ $transferSksId }}][catatan_asesor]"
                                                    rows="2"
                                                    class="form-control @error('penilaian.'.$transferSksId.'.catatan_asesor') is-invalid @enderror"
                                                    placeholder="Catatan validasi atau rekomendasi asesor...">{{ old('penilaian.'.$transferSksId.'.catatan_asesor', $penilaian?->catatan_asesor) }}</textarea>

                                                @error('penilaian.'.$transferSksId.'.catatan_asesor')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                @endif


                                {{-- NON FORMAL --}}
                                @php
                                $nonFormal = $mk->transferSksNonFormal;

                                $penilaianNF = $nonFormal
                                ? $nonFormal->penilaian->first()
                                : null;
                                @endphp

                                @if($nonFormal)
                                <div class="col-12">
                                    <div class="p-3 rounded bg-purple-lt border border-purple-subtle">

                                        <div class="fw-bold text-purple mb-3 uppercase">
                                            Penilaian Non Formal
                                        </div>

                                        <div class="row g-3">

                                            {{-- KESENJANGAN --}}
                                            <div class="col-12">
                                                <label class="form-label fw-bold">Analisis Kesenjangan</label>

                                                <textarea
                                                    name="penilaian_nonformal[{{ $nonFormal->id }}][kesenjangan]"
                                                    rows="2"
                                                    class="form-control @error('penilaian_nonformal.'.$nonFormal->id.'.kesenjangan') is-invalid @enderror">{{ old('penilaian_nonformal.'.$nonFormal->id.'.kesenjangan', $penilaianNF?->kesenjangan) }}</textarea>

                                                @error('penilaian_nonformal.'.$nonFormal->id.'.kesenjangan')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- NILAI --}}
                                            <div class="col-12 col-md-4">
                                                <label class="form-label fw-bold">Hasil Rekognisi (Skor/SKS)</label>

                                                <input
                                                    type="number"
                                                    name="penilaian_nonformal[{{ $nonFormal->id }}][nilai]"
                                                    class="form-control @error('penilaian_nonformal.'.$nonFormal->id.'.nilai') is-invalid @enderror"
                                                    value="{{ old('penilaian_nonformal.'.$nonFormal->id.'.nilai', $penilaianNF?->nilai) }}"
                                                    placeholder="0-100">

                                                @error('penilaian_nonformal.'.$nonFormal->id.'.nilai')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- CATATAN --}}
                                            <div class="col-12 col-md-8">
                                                <label class="form-label fw-bold">Catatan Verifikasi Asesor</label>

                                                <textarea
                                                    name="penilaian_nonformal[{{ $nonFormal->id }}][catatan_asesor]"
                                                    rows="2"
                                                    class="form-control @error('penilaian_nonformal.'.$nonFormal->id.'.catatan_asesor') is-invalid @enderror">{{ old('penilaian_nonformal.'.$nonFormal->id.'.catatan_asesor', $penilaianNF?->catatan_asesor) }}</textarea>

                                                @error('penilaian_nonformal.'.$nonFormal->id.'.catatan_asesor')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                @endif

                            </div>

                        </div>

                    </div>


                    {{-- ===================== PENILAIAN ===================== --}}


                </div>
            </div>
            {{-- ===================== END CARD ===================== --}}

            @endif

            {{-- ===================== FOOTER ===================== --}}
            <div class="card shadow-sm sticky-bottom py-3 px-4 bg-white border-top">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('asesmen.review', $mahasiswa->id) }}" class="btn btn-outline-secondary">
                        <i class="ti ti-arrow-left me-1"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-device-floppy me-1"></i> Simpan
                    </button>
                </div>
            </div>

        </form>

    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            function syncMaster(mkId) {
                const master = document.querySelector(`.check-all-mk[data-mk="${mkId}"]`);
                if (!master) return;

                const all = document.querySelectorAll(`.check-target[data-mk="${mkId}"]`);
                const checked = document.querySelectorAll(`.check-target[data-mk="${mkId}"]:checked`);

                master.checked = (all.length > 0 && all.length === checked.length);
            }

            // MASTER -> CHILD
            document.querySelectorAll('.check-all-mk').forEach(master => {
                master.addEventListener('change', function() {

                    const mkId = this.dataset.mk;
                    const targets = document.querySelectorAll(`.check-target[data-mk="${mkId}"]`);

                    targets.forEach(cb => cb.checked = this.checked);
                });
            });

            // CHILD -> MASTER
            document.addEventListener('change', function(e) {

                if (!e.target.classList.contains('check-target')) return;

                const mkId = e.target.dataset.mk;
                syncMaster(mkId);
            });

            // INIT
            document.querySelectorAll('.check-all-mk').forEach(master => {
                syncMaster(master.dataset.mk);
            });

        });
    </script>

    @if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const firstError = document.querySelector('.is-invalid');

            if (firstError) {

                firstError.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });

                firstError.focus();
            }
        });
    </script>
    @endif

</x-app-layout>